<?php

namespace App\Services;

use App\Models\DiagnosisSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DiagnosisIllustrationService
{
    /**
     * Build photorealistic, vehicle-specific reference photos for top causes.
     * Prefer OpenAI image models; reuse per vehicle+part cache. Never ship diagram drawings as primary.
     */
    public function forSession(DiagnosisSession $session, int $maxImages = 5, bool $generateAi = true): array
    {
        $meta = is_array($session->ai_response) ? $session->ai_response : [];
        $existing = is_array($meta['illustrations'] ?? null) ? $meta['illustrations'] : [];
        $realisticExisting = $this->onlyRealistic($existing);

        // Keep good photorealistic cache; discard diagram/SVG leftovers.
        if ($realisticExisting !== [] && ! $this->needsRealisticUpgrade($existing)) {
            $filled = $this->fillFromVehicleCache($session, $realisticExisting, $maxImages);
            if ($filled !== []) {
                if ($filled !== $existing) {
                    $meta['illustrations'] = $filled;
                    $session->update(['ai_response' => $meta]);
                }

                return $filled;
            }
        }

        $result = $session->result;
        if (! $result) {
            return [];
        }

        $vehicle = $this->vehicleLabel($session);
        $vehicleSlug = $this->vehicleSlug($session);
        $targets = $this->illustrationTargets($result, $maxImages);
        $illustrations = [];

        // 1) Instant hits from vehicle-specific photo cache
        foreach ($targets as $index => $target) {
            $cached = $this->fromVehicleCache($vehicleSlug, $target);
            if ($cached) {
                $illustrations[$index] = $cached;
            }
        }

        // 2) Generate missing photorealistic photos (parallel where possible)
        if ($generateAi) {
            $apiKey = trim((string) config('services.openai.api_key', ''));
            if ($apiKey !== '') {
                $missing = [];
                foreach ($targets as $index => $target) {
                    if (! isset($illustrations[$index])) {
                        $missing[$index] = $target;
                    }
                }

                if ($missing !== []) {
                    $generated = $this->generateMany($apiKey, $session->session_id, $vehicle, $vehicleSlug, $missing);
                    foreach ($generated as $index => $row) {
                        $illustrations[$index] = $row;
                    }
                }
            }
        }

        ksort($illustrations);
        $illustrations = array_values($illustrations);

        if ($illustrations !== []) {
            $meta['illustrations'] = $illustrations;
            $session->update(['ai_response' => $meta]);
        }

        return $illustrations;
    }

    /**
     * True when stored visuals are diagram drawings / SVG catalog — must upgrade.
     */
    public function needsRealisticUpgrade(array $illustrations): bool
    {
        if ($illustrations === []) {
            return true;
        }

        foreach ($illustrations as $row) {
            if (! is_array($row) || empty($row['url'])) {
                continue;
            }
            if ($this->isRealisticRow($row)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Attach realistic image_url onto each likely cause (no diagram fallback).
     */
    public function enrichCauses(array $causes, array $illustrations = [], ?DiagnosisSession $session = null): array
    {
        $byKey = [];
        foreach ($this->onlyRealistic($illustrations) as $row) {
            $key = (string) ($row['visual_key'] ?? '');
            if ($key !== '') {
                $byKey[$key] = $row;
            }
        }

        $vehicleSlug = $session ? $this->vehicleSlug($session) : null;
        $vehicleLabel = $session ? $this->vehicleLabel($session) : '';
        $sessionLanguage = 'en';
        if ($session) {
            $metaLang = is_array($session->ai_response) ? $session->ai_response : [];
            $sessionLanguage = (string) ($metaLang['user_language'] ?? $metaLang['language'] ?? 'sq');
        }

        foreach ($causes as &$cause) {
            if (! is_array($cause)) {
                continue;
            }

            $title = (string) ($cause['title'] ?? $cause['cause'] ?? $cause['part_name'] ?? '');
            $description = (string) ($cause['description'] ?? $cause['explanation'] ?? '');
            $key = (string) ($cause['visual_key'] ?? DiagnosisVisualCatalog::resolveKey($title, $description));
            $cause['visual_key'] = $key;

            $match = $byKey[$key] ?? null;
            if (! $match && $vehicleSlug) {
                $match = $this->fromVehicleCache($vehicleSlug, [
                    'visual_key' => $key,
                    'caption' => $this->shortCaption($title),
                ]);
            }

            $language = (string) ($cause['user_language'] ?? $sessionLanguage);
            $cause['annotation'] = DiagnosisVisualCatalog::annotationFor($key, $language, $title);

            if ($match && ! empty($match['url'])) {
                $cause['image_url'] = $match['url'];
                $cause['image_caption'] = $match['caption']
                    ?? $this->photoCaption($vehicleLabel, $key, $title);
                $cause['image_source'] = $match['provider'] ?? 'openai';
            } else {
                // Do not attach SVG drawings — UI stays clean until a real photo exists.
                unset($cause['image_url'], $cause['image_caption'], $cause['image_source']);
            }
        }
        unset($cause);

        return array_values($causes);
    }

    /**
     * Resolve realistic photos from vehicle cache only (history / PDF, no API wait).
     */
    public function catalogForResult($result, int $maxImages = 5, ?DiagnosisSession $session = null): array
    {
        if (! $result || ! $session) {
            return [];
        }

        $targets = $this->illustrationTargets($result, $maxImages);
        $out = [];
        foreach ($targets as $target) {
            $cached = $this->fromVehicleCache($this->vehicleSlug($session), $target);
            if ($cached) {
                $out[] = $cached;
            }
        }

        return $out;
    }

    private function onlyRealistic(array $illustrations): array
    {
        return array_values(array_filter(
            $illustrations,
            fn ($row) => is_array($row) && $this->isRealisticRow($row)
        ));
    }

    private function isRealisticRow(array $row): bool
    {
        $url = (string) ($row['url'] ?? '');
        $provider = (string) ($row['provider'] ?? '');

        if ($url === '' || str_contains($url, '/images/diagnosis/')) {
            return false;
        }
        if (str_ends_with(strtolower(parse_url($url, PHP_URL_PATH) ?: $url), '.svg')) {
            return false;
        }
        if (in_array($provider, ['catalog', 'diagram'], true)) {
            return false;
        }

        return true;
    }

    private function fillFromVehicleCache(DiagnosisSession $session, array $cached, int $maxImages): array
    {
        $result = $session->result;
        if (! $result) {
            return $cached;
        }

        $byKey = [];
        foreach ($cached as $row) {
            if (! empty($row['visual_key'])) {
                $byKey[(string) $row['visual_key']] = $row;
            }
        }

        $merged = $cached;
        foreach ($this->illustrationTargets($result, $maxImages) as $target) {
            $key = (string) ($target['visual_key'] ?? '');
            if ($key === '' || isset($byKey[$key])) {
                continue;
            }
            $hit = $this->fromVehicleCache($this->vehicleSlug($session), $target);
            if ($hit) {
                $merged[] = $hit;
                $byKey[$key] = $hit;
            }
        }

        return $merged;
    }

    private function illustrationTargets($result, int $maxImages): array
    {
        $targets = [];
        $seen = [];

        $causes = is_array($result->likely_causes) ? $result->likely_causes : [];
        foreach ($causes as $i => $cause) {
            if (! is_array($cause)) {
                continue;
            }
            $title = (string) ($cause['title'] ?? $cause['part_name'] ?? $cause['cause'] ?? 'Component');
            $description = (string) ($cause['description'] ?? $cause['explanation'] ?? '');
            $key = DiagnosisVisualCatalog::resolveKey($title, $description);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $targets[] = [
                'slug' => 'cause-'.($i + 1).'-'.$key,
                'visual_key' => $key,
                'caption' => $this->shortCaption($title),
                'focus' => $title.' — '.$description,
            ];
            if (count($targets) >= $maxImages) {
                break;
            }
        }

        // If no causes, one engine-bay overview photo.
        if ($targets === []) {
            $problemTitle = (string) ($result->problem_title ?? 'Engine bay inspection');
            $key = DiagnosisVisualCatalog::resolveKey($problemTitle, (string) ($result->problem_description ?? ''));
            $targets[] = [
                'slug' => 'overview-'.$key,
                'visual_key' => $key,
                'caption' => $this->shortCaption($problemTitle),
                'focus' => $problemTitle,
            ];
        }

        return array_slice($targets, 0, $maxImages);
    }

    private function fromVehicleCache(string $vehicleSlug, array $target): ?array
    {
        $visualKey = (string) ($target['visual_key'] ?? '');
        if ($vehicleSlug === '' || $visualKey === '') {
            return null;
        }

        // v2 = photorealistic photo with red fault-area mark in the generation prompt
        $path = "diagnosis-visuals/v2/{$vehicleSlug}/{$visualKey}.png";
        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        return [
            'url' => Storage::disk('public')->url($path),
            'path' => $path,
            'caption' => $this->shortCaption((string) ($target['caption'] ?? DiagnosisVisualCatalog::labelFor($visualKey))),
            'visual_key' => $visualKey,
            'provider' => 'vehicle-cache',
            'model' => (string) config('services.openai.image_model', 'gpt-image-1'),
            'style' => 'photorealistic',
            'created_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @param  array<int, array>  $missing  index => target
     * @return array<int, array>
     */
    private function generateMany(string $apiKey, string $sessionId, string $vehicle, string $vehicleSlug, array $missing): array
    {
        $endpoint = rtrim((string) config('services.openai.api_url', 'https://api.openai.com/v1'), '/').'/images/generations';
        $model = (string) config('services.openai.image_model', 'gpt-image-1');
        $out = [];

        // Parallel requests for speed on unlock.
        $responses = Http::pool(function ($pool) use ($missing, $apiKey, $endpoint, $model, $vehicle) {
            foreach ($missing as $index => $target) {
                $prompt = $this->photorealisticPrompt($vehicle, $target);
                $payload = $this->imagePayload($model, $prompt);
                $pool->as((string) $index)
                    ->withToken($apiKey)
                    ->timeout(100)
                    ->acceptJson()
                    ->post($endpoint, $payload);
            }
        });

        foreach ($missing as $index => $target) {
            $response = $responses[(string) $index] ?? null;
            $usedModel = $model;

            if (! $response || ! $response->successful()) {
                // Retry once sequentially with the platform default / gpt-image-1.5
                $fallbackModel = $model === 'gpt-image-2' ? 'gpt-image-1.5' : 'gpt-image-2';
                $fallback = $this->generateOne($apiKey, $sessionId, $index, $vehicle, $vehicleSlug, $target, $fallbackModel);
                if ($fallback) {
                    $out[$index] = $fallback;
                } else {
                    Log::warning('Photorealistic diagnosis visual failed', [
                        'session' => $sessionId,
                        'visual_key' => $target['visual_key'] ?? null,
                        'status' => $response?->status(),
                        'body' => substr((string) ($response?->body() ?? ''), 0, 280),
                    ]);
                }

                continue;
            }

            $saved = $this->persistImageResponse($response, $sessionId, $index, $vehicle, $vehicleSlug, $target, $usedModel);
            if ($saved) {
                $out[$index] = $saved;
            }
        }

        return $out;
    }

    private function generateOne(
        string $apiKey,
        string $sessionId,
        int $index,
        string $vehicle,
        string $vehicleSlug,
        array $target,
        ?string $forceModel = null
    ): ?array {
        $model = $forceModel ?: (string) config('services.openai.image_model', 'gpt-image-1');
        $prompt = $this->photorealisticPrompt($vehicle, $target);
        $endpoint = rtrim((string) config('services.openai.api_url', 'https://api.openai.com/v1'), '/').'/images/generations';

        try {
            $response = Http::withToken($apiKey)
                ->timeout(100)
                ->acceptJson()
                ->post($endpoint, $this->imagePayload($model, $prompt));

            if (! $response->successful()) {
                $alt = $model === 'gpt-image-2' ? 'gpt-image-1.5' : 'gpt-image-2';
                if ($alt !== $model) {
                    $model = $alt;
                    $response = Http::withToken($apiKey)
                        ->timeout(100)
                        ->acceptJson()
                        ->post($endpoint, $this->imagePayload($model, $prompt));
                }
            }

            if (! $response->successful()) {
                return null;
            }

            return $this->persistImageResponse($response, $sessionId, $index, $vehicle, $vehicleSlug, $target, $model);
        } catch (\Throwable $e) {
            Log::warning('Photorealistic visual exception: '.$e->getMessage(), ['session' => $sessionId]);

            return null;
        }
    }

    private function imagePayload(string $model, string $prompt): array
    {
        // Current OpenAI image models (gpt-image-2 / 1.5) accept a minimal payload
        // and return b64_json by default. Legacy dall-e-* params are rejected.
        return [
            'model' => $model,
            'prompt' => $prompt,
            'n' => 1,
            'size' => '1024x1024',
        ];
    }

    private function persistImageResponse($response, string $sessionId, int $index, string $vehicle, string $vehicleSlug, array $target, string $model): ?array
    {
        $b64 = $response->json('data.0.b64_json');
        if (! is_string($b64) || $b64 === '') {
            $remoteUrl = $response->json('data.0.url');
            if (is_string($remoteUrl) && $remoteUrl !== '') {
                $bytes = Http::timeout(60)->get($remoteUrl)->body();
                if ($bytes !== '') {
                    $b64 = base64_encode($bytes);
                }
            }
        }

        if (! is_string($b64) || $b64 === '') {
            return null;
        }

        $binary = base64_decode($b64, true);
        if ($binary === false || $binary === '') {
            return null;
        }

        $visualKey = (string) ($target['visual_key'] ?? 'engine-overview');
        $vehiclePath = "diagnosis-visuals/v2/{$vehicleSlug}/{$visualKey}.png";
        Storage::disk('public')->put($vehiclePath, $binary);

        $slug = preg_replace('/[^a-z0-9\-]+/i', '-', (string) ($target['slug'] ?? 'img-'.$index)) ?: 'img';
        Storage::disk('public')->put("diagnosis-illustrations/{$sessionId}/{$slug}.png", $binary);

        return [
            'url' => Storage::disk('public')->url($vehiclePath),
            'path' => $vehiclePath,
            'caption' => $this->photoCaption($vehicle, $visualKey, (string) ($target['caption'] ?? '')),
            'visual_key' => $visualKey,
            'provider' => 'openai',
            'model' => $model,
            'style' => 'photorealistic',
            'vehicle' => $vehicle,
            'created_at' => now()->toIso8601String(),
        ];
    }

    private function photorealisticPrompt(string $vehicle, array $target): string
    {
        $visualKey = (string) ($target['visual_key'] ?? 'engine-overview');
        $subject = DiagnosisVisualCatalog::photoSubject($visualKey);
        $car = $vehicle !== '' ? $vehicle : 'modern European passenger car';
        $annotation = DiagnosisVisualCatalog::annotationFor($visualKey, 'en', (string) ($target['caption'] ?? ''));
        $zone = (string) ($annotation['label'] ?? 'fault component');

        return 'Photorealistic documentary photograph for a professional automotive diagnosis app (CarWise). '
            ."Exact vehicle context: {$car}. "
            ."Primary subject: {$subject}. "
            .'Shoot like a real independent garage inspection photo: natural workshop lighting, '
            .'true metal/plastic/rubber textures, shallow depth of field, handheld mirrorless camera, 35-50mm lens. '
            .'Show the real installed part/location on this class of vehicle so a car owner recognizes it. '
            ."CRITICAL: clearly mark the problem component ({$zone}) with a thin bright RED outline or RED circle "
            .'and one small RED arrow pointing exactly at the faulty part — this is an inspection callout. '
            .'Keep the photo photorealistic; the red mark is the only graphic overlay. '
            .'NO blueprint style, NO cartoon, NO 3D render look, NO text labels, NO watermark, NO brand logos, '
            .'NO people, NO fake UI. Single clear photo, high detail.';
    }

    private function photoCaption(string $vehicle, string $visualKey, string $title): string
    {
        $part = $this->shortCaption($title !== '' ? $title : DiagnosisVisualCatalog::labelFor($visualKey));
        $car = trim($vehicle);

        return $car !== ''
            ? "Realistic reference · {$car} · {$part}"
            : "Realistic reference · {$part}";
    }

    private function vehicleLabel(DiagnosisSession $session): string
    {
        return trim(implode(' ', array_filter([
            $session->year,
            $session->make,
            $session->model,
            $session->engine_size,
            $session->engine_type,
        ], fn ($v) => filled($v))));
    }

    private function vehicleSlug(DiagnosisSession $session): string
    {
        $raw = strtolower(trim(($session->make ?? '').'-'.($session->model ?? '').'-'.($session->year ?? '')));
        $slug = Str::slug($raw);

        return $slug !== '' ? $slug : 'vehicle';
    }

    private function shortCaption(string $text): string
    {
        $clean = trim(preg_replace('/\s*\([^)]*\)\s*/u', ' ', $text) ?? $text);
        $clean = preg_replace('/\s+/u', ' ', $clean) ?? $clean;

        return mb_substr($clean, 0, 120);
    }
}
