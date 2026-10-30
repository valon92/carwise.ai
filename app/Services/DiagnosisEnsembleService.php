<?php

namespace App\Services;

use App\Services\AIProviders\GeminiProvider;
use App\Services\AIProviders\OpenAIProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Combines available professional AI engines for a stronger differential diagnosis.
 * OpenAI provides the structured paid report; Gemini cross-checks ranked causes when available.
 */
class DiagnosisEnsembleService
{
    public function __construct(
        private readonly OpenAIProvider $openAI,
        private readonly GeminiProvider $gemini,
    ) {
    }

    public function analyze(array $data): array
    {
        $language = $data['user_language'] ?? 'en';
        $used = [];
        $usage = [
            'model' => 'ensemble',
            'input_tokens' => 0,
            'output_tokens' => 0,
            'engines' => [],
        ];

        if (! $this->openAI->isAvailable()) {
            throw new \RuntimeException('Primary diagnostic engine (OpenAI) is not available.');
        }

        $primary = $this->openAI->analyzeDiagnosis($data);
        $used[] = 'OpenAI GPT';
        if (isset($primary['usage']) && is_array($primary['usage'])) {
            $usage['input_tokens'] += (int) ($primary['usage']['input_tokens'] ?? 0);
            $usage['output_tokens'] += (int) ($primary['usage']['output_tokens'] ?? 0);
            $usage['engines'][] = [
                'name' => 'openai',
                'model' => $primary['usage']['model'] ?? 'gpt-4o',
                'input_tokens' => (int) ($primary['usage']['input_tokens'] ?? 0),
                'output_tokens' => (int) ($primary['usage']['output_tokens'] ?? 0),
            ];
        }
        unset($primary['usage']);

        $secondaryCauses = [];
        if ($this->geminiQuickAvailable()) {
            try {
                $secondaryCauses = $this->geminiCauseReview($data);
                if ($secondaryCauses !== []) {
                    $used[] = 'Google Gemini';
                    $usage['engines'][] = [
                        'name' => 'gemini',
                        'model' => config('services.gemini.model', 'gemini-3.5-flash'),
                        'role' => 'cause_cross_check',
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini ensemble cross-check skipped', ['error' => $e->getMessage()]);
            }
        }

        $merged = $this->mergeCauses($primary, $secondaryCauses);
        $merged = DiagnosisTerminology::expandResult($merged, $language);
        $merged['ai_provider'] = 'CarWise Multi-AI';
        $merged['diagnostic_engines'] = $used;
        $merged['ai_model_version'] = implode(' + ', $used);
        $merged['usage'] = $usage;

        return $merged;
    }

    private function geminiQuickAvailable(): bool
    {
        $key = trim((string) config('services.gemini.api_key'));

        return $key !== '';
    }

    private function geminiCauseReview(array $data): array
    {
        $key = trim((string) config('services.gemini.api_key'));
        $base = rtrim((string) (config('services.gemini.api_url') ?? 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $model = config('services.gemini.model') ?? 'gemini-3.5-flash';

        $make = $data['make'] ?? $data['car_brand'] ?? '';
        $modelName = $data['model'] ?? $data['car_model'] ?? '';
        $year = $data['year'] ?? $data['car_year'] ?? '';
        $engine = trim(($data['engine_type'] ?? '').' '.($data['engine_size'] ?? ''));
        $mileage = $data['mileage'] ?? '';
        $description = $data['description'] ?? $data['problem_description'] ?? '';
        $obd = $data['obd_code'] ?? '';
        $symptoms = is_array($data['symptoms'] ?? null) ? implode(', ', $data['symptoms']) : '';

        $prompt = <<<PROMPT
You are a senior automotive diagnostic engineer. Return ONLY JSON:
{"causes":[{"title":"...","probability":0,"typical_dtc_examples":["P0000"]}]}
Give 4-5 ranked causes for this vehicle. Prefer model-family patterns. Do not invent a single high-confidence cause without a DTC.
Vehicle: {$year} {$make} {$modelName}, {$engine}, {$mileage} km
Symptoms: {$symptoms}
Description: {$description}
OBD code: {$obd}
PROMPT;

        $url = "{$base}/models/{$model}:generateContent?key={$key}";
        $response = Http::timeout(45)->acceptJson()->post($url, [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => [
                'temperature' => 0.2,
                'responseMimeType' => 'application/json',
            ],
        ]);

        if (! $response->successful()) {
            return [];
        }

        $text = (string) data_get($response->json(), 'candidates.0.content.parts.0.text', '');
        $json = json_decode($text, true);
        if (! is_array($json)) {
            return [];
        }

        $causes = $json['causes'] ?? $json['likely_causes'] ?? [];

        return is_array($causes) ? array_values(array_filter($causes, 'is_array')) : [];
    }

    private function mergeCauses(array $primary, array $secondaryCauses): array
    {
        $primaryCauses = is_array($primary['likely_causes'] ?? null) ? $primary['likely_causes'] : [];
        if ($secondaryCauses === []) {
            return $primary;
        }

        $normalized = [];
        foreach ($primaryCauses as $cause) {
            if (! is_array($cause)) {
                continue;
            }
            $key = $this->causeKey((string) ($cause['title'] ?? ''));
            $normalized[$key] = $cause;
        }

        foreach ($secondaryCauses as $cause) {
            $title = trim((string) ($cause['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $key = $this->causeKey($title);
            if (isset($normalized[$key])) {
                // Agreement boost (small)
                $prob = (int) ($normalized[$key]['probability'] ?? 0);
                $normalized[$key]['probability'] = min(40, $prob + 3);
                $normalized[$key]['ensemble_agreement'] = true;
                continue;
            }
            // Add unique secondary cause with tempered probability
            $normalized[$key] = [
                'title' => $title,
                'description' => (string) ($cause['description'] ?? 'Cross-checked by secondary diagnostic engine.'),
                'probability' => min(18, max(8, (int) ($cause['probability'] ?? 12))),
                'part_name' => (string) ($cause['part_name'] ?? $title),
                'part_image_url' => '',
                'part_category' => (string) ($cause['part_category'] ?? 'engine'),
                'suggested_parts' => [],
                'typical_dtc_examples' => is_array($cause['typical_dtc_examples'] ?? null)
                    ? $cause['typical_dtc_examples']
                    : [],
                'ensemble_source' => 'gemini',
            ];
        }

        $merged = array_values($normalized);
        usort($merged, fn ($a, $b) => ((int) ($b['probability'] ?? 0)) <=> ((int) ($a['probability'] ?? 0)));
        $merged = array_slice($merged, 0, 5);

        // Renormalize probabilities roughly to ~100
        $sum = array_sum(array_map(fn ($c) => (int) ($c['probability'] ?? 0), $merged)) ?: 1;
        foreach ($merged as &$cause) {
            $cause['probability'] = (int) round(((int) ($cause['probability'] ?? 0)) * 100 / $sum);
        }
        unset($cause);

        $primary['likely_causes'] = $merged;

        $insight = 'Cause ranking was cross-checked across multiple professional AI engines for better accuracy.';
        $insights = is_array($primary['ai_insights'] ?? null) ? $primary['ai_insights'] : [];
        array_unshift($insights, $insight);
        $primary['ai_insights'] = array_values(array_unique(array_filter($insights, 'is_string')));

        return $primary;
    }

    private function causeKey(string $title): string
    {
        $t = mb_strtolower($title);
        $t = preg_replace('/[^a-z0-9\s]+/u', ' ', $t) ?? $t;
        $t = preg_replace('/\s+/', ' ', trim($t)) ?? $t;

        foreach (['egr', 'dpf', 'maf', 'turbo', 'sensor', 'injector', 'battery', 'alternator', 'catalyst'] as $token) {
            if (str_contains($t, $token)) {
                return $token;
            }
        }

        return $t;
    }
}
