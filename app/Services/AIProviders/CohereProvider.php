<?php

namespace App\Services\AIProviders;

use App\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CohereProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = (string) (Config::get('services.cohere.api_key') ?? '');
        $this->baseUrl = rtrim((string) (
            Config::get('services.cohere.api_url')
            ?? Config::get('services.cohere.base_url')
            ?? 'https://api.cohere.ai/v1'
        ), '/');
        // Default to a current Chat-compatible model (legacy "command" was removed Sept 2025).
        $this->model = (string) (Config::get('services.cohere.model') ?? 'command-a-03-2025');
    }

    public function getProviderName(): string
    {
        return 'cohere';
    }

    public function isAvailable(): bool
    {
        if ($this->apiKey === '') {
            return false;
        }

        try {
            $response = Http::timeout(12)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->baseUrl.'/chat', [
                    'model' => $this->model,
                    'message' => 'ping',
                    'max_tokens' => 5,
                    'temperature' => 0,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Cohere provider availability check failed', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function getEstimatedCost(array $data): float
    {
        return 0.02;
    }

    public function analyzeDiagnosis(array $data): array
    {
        if (! $this->isAvailable()) {
            throw new \Exception('Cohere API key/model is not available.');
        }

        $prompt = $this->buildDiagnosisPrompt($data);
        $response = $this->makeChatRequest($prompt);
        $text = $this->extractText($response);
        $language = $data['user_language'] ?? 'en';

        return $this->parseResponse($text, $language, $response);
    }

    /**
     * Lightweight ranked-cause review for the Multi-AI ensemble.
     */
    public function reviewCauses(array $data): array
    {
        $make = $data['make'] ?? $data['car_brand'] ?? '';
        $model = $data['model'] ?? $data['car_model'] ?? '';
        $year = $data['year'] ?? $data['car_year'] ?? '';
        $engine = trim(($data['engine_type'] ?? '').' '.($data['engine_size'] ?? ''));
        $mileage = $data['mileage'] ?? '';
        $description = $data['description'] ?? $data['problem_description'] ?? '';
        $obd = $data['obd_code'] ?? '';
        $symptoms = is_array($data['symptoms'] ?? null) ? implode(', ', $data['symptoms']) : '';

        $prompt = <<<PROMPT
You are a senior automotive diagnostic engineer. Return ONLY raw JSON (no markdown fences, no commentary):
{"causes":[{"title":"...","probability":0,"typical_dtc_examples":["P0000"]}]}
Give 4-5 ranked causes for this vehicle. Prefer model-family patterns. Do not invent a single high-confidence cause without a DTC.
Vehicle: {$year} {$make} {$model}, {$engine}, {$mileage} km
Symptoms: {$symptoms}
Description: {$description}
OBD code: {$obd}
PROMPT;

        $response = $this->makeChatRequest($prompt, 1200);
        $text = $this->extractText($response);
        $json = $this->decodeJsonPayload($text);
        $causes = is_array($json) ? ($json['causes'] ?? $json['likely_causes'] ?? []) : [];

        return is_array($causes) ? array_values(array_filter($causes, 'is_array')) : [];
    }

    /**
     * Cohere often wraps JSON in ```json fences; strip and extract the object.
     */
    private function decodeJsonPayload(string $text): ?array
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return null;
        }

        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $trimmed, $fence)) {
            $trimmed = trim($fence[1]);
        }

        $decoded = json_decode($trimmed, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{[\s\S]*\}/', $trimmed, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    private function buildDiagnosisPrompt(array $data): string
    {
        $language = $data['user_language'] ?? 'en';
        $make = $data['make'] ?? $data['car_brand'] ?? 'Unknown';
        $model = $data['model'] ?? $data['car_model'] ?? 'Unknown';
        $year = $data['year'] ?? $data['car_year'] ?? 'Unknown';
        $mileage = $data['mileage'] ?? 'Unknown';
        $description = $data['description'] ?? $data['problem_description'] ?? '';

        $symptoms = $data['symptoms'] ?? [];
        if (is_string($symptoms)) {
            $symptoms = json_decode($symptoms, true) ?? [$symptoms];
        }
        $symptomsList = is_array($symptoms) ? implode(', ', array_filter($symptoms, 'is_string')) : '';

        $languageNames = [
            'sq' => 'Albanian (Shqip)', 'en' => 'English', 'de' => 'German (Deutsch)',
            'fr' => 'French (Français)', 'pt' => 'Portuguese (Português)',
            'es' => 'Spanish (Español)', 'it' => 'Italian (Italiano)',
        ];
        $languageName = $languageNames[$language] ?? 'English';

        return "You are an expert automotive diagnostic AI for CarWise.ai. Respond EXCLUSIVELY in {$languageName}.

VEHICLE:
- Make: {$make}
- Model: {$model}
- Year: {$year}
- Mileage: {$mileage} km

PROBLEM:
- Symptoms: {$symptomsList}
- Description: {$description}

Return a practical differential diagnosis with ranked likely causes, next actions (OBD/VCDS first when no code), and EUR guide costs. Expand abbreviations on first use (EGR, DPF, MAF, DTC, OBD, ECU).";
    }

    private function makeChatRequest(string $message, int $maxTokens = 2500): array
    {
        $response = Http::timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post($this->baseUrl.'/chat', [
                'model' => $this->model,
                'message' => $message,
                'max_tokens' => $maxTokens,
                'temperature' => (float) (Config::get('services.cohere.temperature') ?? 0.2),
            ]);

        if (! $response->successful()) {
            $error = $response->json('message') ?? $response->body();
            throw new \Exception('Cohere API error: '.$error);
        }

        return $response->json() ?? [];
    }

    private function extractText(array $response): string
    {
        if (! empty($response['text']) && is_string($response['text'])) {
            return $response['text'];
        }

        $parts = data_get($response, 'message.content');
        if (is_array($parts)) {
            foreach ($parts as $part) {
                if (is_array($part) && ($part['type'] ?? null) === 'text' && ! empty($part['text'])) {
                    return (string) $part['text'];
                }
            }
        }

        throw new \Exception('Invalid response format from Cohere Chat API');
    }

    private function parseResponse(string $content, string $language, array $raw): array
    {
        return [
            'problem_title' => 'Cohere diagnostic review',
            'problem_description' => $content,
            'severity' => 'medium',
            'confidence_score' => 55,
            'likely_causes' => [],
            'recommended_actions' => [[
                'title' => 'Read OBD / VCDS fault codes',
                'description' => 'Use a live scan before replacing parts.',
                'urgency' => 'Within 24 hours',
                'video_url' => '',
            ]],
            'estimated_costs' => [],
            'ai_insights' => [mb_substr($content, 0, 500)],
            'provider' => 'cohere',
            'ai_provider' => 'cohere',
            'model' => $this->model,
            'usage' => [
                'model' => $this->model,
                'input_tokens' => (int) data_get($raw, 'meta.billed_units.input_tokens', 0),
                'output_tokens' => (int) data_get($raw, 'meta.billed_units.output_tokens', 0),
            ],
        ];
    }
}
