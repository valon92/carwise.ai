<?php

namespace App\Services;

use App\Models\DiagnosisSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DiagnosisIllustrationService
{
    public function forSession(DiagnosisSession $session): array
    {
        $meta = is_array($session->ai_response) ? $session->ai_response : [];
        $existing = $meta['illustrations'] ?? null;

        if (is_array($existing) && $existing !== []) {
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

        $title = trim((string) ($result->problem_title ?? 'vehicle problem'));
        $cause = '';
        $causes = is_array($result->likely_causes) ? $result->likely_causes : [];
        if ($causes !== []) {
            $first = $causes[0];
            $cause = is_array($first)
                ? (string) ($first['title'] ?? $first['cause'] ?? $first['description'] ?? '')
                : (string) $first;
        }

        $vehicle = trim($session->year.' '.$session->make.' '.$session->model);
        $prompt = $this->prompt($vehicle, $title, $cause);

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
                    'session' => $session->session_id,
                    'status' => $response->status(),
                    'body' => substr((string) $response->body(), 0, 300),
                ]);

                return [];
            }

            $b64 = $response->json('data.0.b64_json');
            if (! is_string($b64) || $b64 === '') {
                return [];
            }

            $path = 'diagnosis-illustrations/'.$session->session_id.'/problem.png';
            Storage::disk('public')->put($path, base64_decode($b64));

            $illustration = [
                'url' => Storage::disk('public')->url($path),
                'path' => $path,
                'caption' => $title,
                'provider' => 'openai',
                'model' => 'dall-e-3',
                'created_at' => now()->toIso8601String(),
            ];

            $meta['illustrations'] = [$illustration];
            $session->update(['ai_response' => $meta]);

            return [$illustration];
        } catch (\Throwable $e) {
            Log::warning('Diagnosis illustration exception: '.$e->getMessage(), [
                'session' => $session->session_id,
            ]);

            return [];
        }
    }

    private function prompt(string $vehicle, string $title, string $cause): string
    {
        $detail = $cause !== '' ? " Main cause: {$cause}." : '';

        return 'Create a clear educational automotive illustration for a car diagnosis report. '
            .'Vehicle: '.($vehicle !== '' ? $vehicle : 'modern passenger car').'. '
            .'Problem: '.$title.'.'.$detail.' '
            .'Show the affected vehicle area with a simple cutaway or labeled callout so a car owner understands where the fault is. '
            .'Clean technical drawing style, bright workshop lighting, no text overlays, no logos, no brand trademarks, no people, realistic proportions.';
    }
}
