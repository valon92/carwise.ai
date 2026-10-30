<?php

namespace App\Services;

use App\Models\DiagnosisSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DiagnosisIllustrationService
{
    /**
     * Build educational visuals for the problem + top likely causes.
     */
    public function forSession(DiagnosisSession $session, int $maxImages = 3): array
    {
        $meta = is_array($session->ai_response) ? $session->ai_response : [];
        $existing = $meta['illustrations'] ?? null;

        if (is_array($existing) && count($existing) >= 1) {
            return array_values(array_filter($existing, fn ($row) => is_array($row) && ! empty($row['url'])));
        }

        $key = trim((string) config('services.openai.api_key'));
        if ($key === '') {
            return [];
        }

        $result = $session->result;
        if (! $result) {
            return [];
        }

        $vehicle = trim($session->year.' '.$session->make.' '.$session->model);
        $targets = $this->illustrationTargets($result, $maxImages);
        $illustrations = [];

        foreach ($targets as $index => $target) {
            $illustration = $this->generateOne($key, $session->session_id, $index, $vehicle, $target);
            if ($illustration) {
                $illustrations[] = $illustration;
            }
        }

        if ($illustrations !== []) {
            $meta['illustrations'] = $illustrations;
            $session->update(['ai_response' => $meta]);
        }

        return $illustrations;
    }

    private function illustrationTargets($result, int $maxImages): array
    {
        $targets = [[
            'slug' => 'overview',
            'caption' => (string) ($result->problem_title ?? 'Vehicle problem overview'),
            'focus' => (string) ($result->problem_title ?? 'check engine diagnosis'),
        ]];

        $causes = is_array($result->likely_causes) ? $result->likely_causes : [];
        foreach (array_slice($causes, 0, max(0, $maxImages - 1)) as $i => $cause) {
            if (! is_array($cause)) {
                continue;
            }
            $title = (string) ($cause['title'] ?? $cause['part_name'] ?? 'Component');
            $targets[] = [
                'slug' => 'cause-'.($i + 1),
                'caption' => $title,
                'focus' => $title.' — '.(string) ($cause['description'] ?? ''),
            ];
        }

        return array_slice($targets, 0, $maxImages);
    }

    private function generateOne(string $key, string $sessionId, int $index, string $vehicle, array $target): ?array
    {
        $prompt = $this->prompt($vehicle, $target['caption'], $target['focus']);

        try {
            $response = Http::withToken($key)
                ->timeout(90)
                ->acceptJson()
                ->post('https://api.openai.com/v1/images/generations', [
                    'model' => 'dall-e-3',
                    'prompt' => $prompt,
                    'n' => 1,
                    'size' => '1024x1024',
                    'quality' => 'standard',
                    'response_format' => 'b64_json',
                ]);

            if (! $response->successful()) {
                Log::warning('Diagnosis illustration failed', [
                    'session' => $sessionId,
                    'target' => $target['slug'] ?? $index,
                    'status' => $response->status(),
                    'body' => substr((string) $response->body(), 0, 300),
                ]);

                return null;
            }

            $b64 = $response->json('data.0.b64_json');
            if (! is_string($b64) || $b64 === '') {
                return null;
            }

            $slug = preg_replace('/[^a-z0-9\-]+/i', '-', (string) ($target['slug'] ?? 'img-'.$index)) ?: 'img';
            $path = "diagnosis-illustrations/{$sessionId}/{$slug}.png";
            Storage::disk('public')->put($path, base64_decode($b64));

            return [
                'url' => Storage::disk('public')->url($path),
                'path' => $path,
                'caption' => DiagnosisTerminology::expandText((string) $target['caption']),
                'provider' => 'openai',
                'model' => 'dall-e-3',
                'created_at' => now()->toIso8601String(),
            ];
        } catch (\Throwable $e) {
            Log::warning('Diagnosis illustration exception: '.$e->getMessage(), [
                'session' => $sessionId,
            ]);

            return null;
        }
    }

    private function prompt(string $vehicle, string $title, string $focus): string
    {
        return 'Create a clear educational automotive cutaway illustration for a professional car diagnosis PDF. '
            .'Vehicle context: '.($vehicle !== '' ? $vehicle : 'modern passenger car').'. '
            .'Topic: '.$title.'. Focus detail: '.$focus.'. '
            .'Show the affected system/component with simple labeled callout arrows so a non-mechanic understands the location. '
            .'Clean modern technical blueprint style, bright neutral workshop lighting, high clarity, no logos, no brand trademarks, no people, no fake UI screens, no watermark text.';
    }
}
