<?php

namespace App\Services\AIProviders;

use App\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;
    private float $temperature;
    private int $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key') ?? '';
        $this->apiUrl = config('services.openai.api_url') ?? 'https://api.openai.com/v1';
        $this->model = config('services.openai.model') ?? 'gpt-4o';
        $this->temperature = config('services.openai.temperature') ?? 0.2;
        $this->maxTokens = config('services.openai.max_tokens') ?? 3500;
    }

    public function getProviderName(): string
    {
        return 'openai';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function getEstimatedCost(array $data): float
    {
        return 0.08;
    }

    public function analyzeDiagnosis(array $data): array
    {
        if (!$this->isAvailable()) {
            throw new \Exception('OpenAI API key is not set.');
        }

        $prompt = $this->buildPrompt($data);

        try {
            $response = Http::timeout(90)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->systemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => $this->temperature,
                'max_tokens' => $this->maxTokens,
                'response_format' => ['type' => 'json_object'],
            ]);

            if (!$response->successful()) {
                Log::error('OpenAI API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('OpenAI API request failed: ' . $response->body());
            }

            $responseData = $response->json();

            if (!isset($responseData['choices'][0]['message']['content'])) {
                Log::error('OpenAI API: Unexpected response format', ['response' => $responseData]);
                throw new \Exception('Unexpected response format from OpenAI API.');
            }

            $jsonString = $responseData['choices'][0]['message']['content'];
            $parsedResult = json_decode($jsonString, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('OpenAI API: Failed to parse JSON response', [
                    'response' => $jsonString,
                    'error' => json_last_error_msg(),
                ]);
                throw new \Exception('Failed to parse OpenAI API response.');
            }

            $parsedResult = $this->normalizeResult($parsedResult, $data);
            $parsedResult['ai_provider'] = $this->getProviderName();
            $parsedResult['usage'] = [
                'model' => $responseData['model'] ?? $this->model,
                'input_tokens' => (int) ($responseData['usage']['prompt_tokens'] ?? 0),
                'output_tokens' => (int) ($responseData['usage']['completion_tokens'] ?? 0),
            ];

            return $parsedResult;
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('OpenAI API Request Failed: ' . $e->getMessage(), [
                'status' => $e->response?->status(),
                'response' => $e->response?->json(),
                'data' => $data,
            ]);
            throw new \Exception('OpenAI API request failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage(), ['data' => $data]);
            throw new \Exception('OpenAI API error: ' . $e->getMessage());
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
You are CarWise.ai — a senior automotive diagnostic engineer writing a paid evidence report for a car owner.

Rules you must follow:
1. Be more useful than a generic chatbot: structured, ranked, workshop-ready, honest about uncertainty.
2. NEVER invent a single "most likely" fault with high confidence when no OBD-II / VCDS fault code is provided.
3. Always return 3 to 5 ranked likely_causes. Probabilities must sum to roughly 100 (±10).
4. For common platforms, prefer model-family patterns (e.g. VW/Audi 1.6 TDI Golf 7 high mileage: EGR, DPF, exhaust pressure sensor, MAF, turbo underboost) when symptoms fit — do not jump to one part.
5. First recommended action MUST be to read and interpret DTC codes (OBD-II / VCDS / OBDEleven) unless a valid DTC is already provided.
6. If a DTC is provided, lead with that code family and map causes to it.
7. Confidence_score guidance:
   - No DTC, thin symptoms only: 35–55
   - Clarifying answers present but no DTC: 50–68
   - Valid DTC present: 70–92
   Never invent 85%+ without a code.
8. Costs must be EUR only (European workshop guide ranges).
9. Do NOT invent YouTube URLs, video IDs, Unsplash images, fake part numbers, or licensed video companies. Leave video_url empty string. Leave part_image_url empty string. Leave suggested_parts empty arrays unless you know a real common OEM/OES part family name without inventing SKUs.
10. requires_immediate_attention = true only for flashing CEL, overheating, oil pressure, brake failure, stalled in traffic, safe-mode with severe power loss, smoke/fire risk.
11. Write the entire JSON content in the user's language.
12. Include diagnostic_codes_to_check with example P-codes typical for the ranked causes on this vehicle family.
13. Include next_workshop_questions the owner should answer or bring to the mechanic.
14. Include hypothesis_disclaimer stating this is an AI differential diagnosis, not a final workshop verdict.
15. On FIRST use of abbreviations expand them for non-technical owners, e.g. EGR (Exhaust Gas Recirculation), DPF (Diesel Particulate Filter), MAF (Mass Air Flow sensor), DTC (Diagnostic Trouble Code), OBD (On-Board Diagnostics), ECU (Engine Control Unit), VCDS (VAG diagnostic system).
PROMPT;
    }

    private function buildPrompt(array $data): string
    {
        $currency = strtoupper($data['currency_code'] ?? 'EUR');
        if ($currency === '' || $currency === 'USD') {
            $currency = 'EUR';
        }
        $language = $data['user_language'] ?? 'en';

        $symptoms = $data['symptoms'] ?? [];
        if (is_array($symptoms)) {
            $symptomsList = implode(', ', array_filter($symptoms, 'is_string'));
        } elseif (is_string($symptoms)) {
            $symptomsList = $symptoms;
        } elseif (is_object($symptoms) && method_exists($symptoms, 'toArray')) {
            $symptomsArray = $symptoms->toArray();
            $symptomsList = is_array($symptomsArray) ? implode(', ', array_filter($symptomsArray, 'is_string')) : '';
        } else {
            $symptomsList = '';
        }

        $make = $data['make'] ?? $data['car_brand'] ?? 'Unknown';
        $model = $data['model'] ?? $data['car_model'] ?? 'Unknown';
        $year = $data['year'] ?? $data['car_year'] ?? 'Unknown';
        $mileage = $data['mileage'] ?? 'Unknown';
        $engineType = $data['engine_type'] ?? 'Unknown';
        $engineSize = $data['engine_size'] ?? 'Unknown';
        $description = $data['description'] ?? $data['problem_description'] ?? '';
        $obdCode = trim((string) ($data['obd_code'] ?? ''));
        $clarifying = $data['clarifying_answers'] ?? [];
        if (!is_array($clarifying)) {
            $clarifying = [];
        }

        $clarifyingText = '';
        foreach ($clarifying as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $label = is_string($key) ? $key : 'note';
            $clarifyingText .= "- {$label}: {$value}\n";
        }
        if ($clarifyingText === '') {
            $clarifyingText = "- (none provided)\n";
        }

        $languageNames = [
            'sq' => 'Albanian (Shqip)', 'en' => 'English', 'de' => 'German (Deutsch)',
            'fr' => 'French (Français)', 'es' => 'Spanish (Español)', 'it' => 'Italian (Italiano)',
            'pt' => 'Portuguese (Português)', 'ru' => 'Russian (Русский)', 'zh' => 'Chinese (中文)',
            'ja' => 'Japanese (日本語)', 'ko' => 'Korean (한국어)', 'ar' => 'Arabic (العربية)',
            'hi' => 'Hindi (हिन्दी)', 'tr' => 'Turkish (Türkçe)', 'nl' => 'Dutch (Nederlands)',
            'pl' => 'Polish (Polski)', 'sv' => 'Swedish (Svenska)', 'no' => 'Norwegian (Norsk)',
            'da' => 'Danish (Dansk)', 'fi' => 'Finnish (Suomi)', 'el' => 'Greek (Ελληνικά)',
            'he' => 'Hebrew (עברית)', 'th' => 'Thai (ไทย)', 'vi' => 'Vietnamese (Tiếng Việt)',
        ];

        $languageName = $languageNames[$language] ?? 'English';
        $hasDtc = $obdCode !== '';

        return <<<PROMPT
Produce a paid-grade differential diagnosis STRICTLY in {$languageName}. Entire JSON string values must be in {$languageName}.

Vehicle:
- Make: {$make}
- Model: {$model}
- Year: {$year}
- Mileage: {$mileage} km
- Engine type: {$engineType}
- Engine size: {$engineSize}

Checklist symptoms: {$symptomsList}
Owner description: {$description}

Clarifying answers from owner:
{$clarifyingText}
OBD / VCDS fault code provided: {$obdCode}
DTC available: {$hasDtc}

Return ONLY valid JSON with this exact shape:
{
  "problem_title": "string",
  "problem_description": "string — honest summary; say if this is a hypothesis pending DTC",
  "severity": "low|medium|high|critical",
  "confidence_score": 48,
  "data_quality": "low|medium|high",
  "hypothesis_disclaimer": "string",
  "likely_causes": [
    {
      "title": "string",
      "description": "string — why this fits THIS vehicle family + symptoms",
      "probability": 30,
      "part_name": "string",
      "part_image_url": "",
      "part_category": "engine|brakes|electrical|suspension|transmission|exhaust|cooling|fuel|body|interior|exterior|lighting",
      "suggested_parts": [],
      "typical_dtc_examples": ["P0401"]
    }
  ],
  "recommended_actions": [
    {
      "title": "string",
      "description": "string",
      "urgency": "Immediate|Within 24 hours|Within 1 week|Within 2 weeks",
      "video_url": "",
      "video_title": "",
      "video_duration": "",
      "video_company": "",
      "tools_needed": ["OBD-II scanner or VCDS"],
      "difficulty_level": "easy|medium|hard|professional"
    }
  ],
  "estimated_costs": [
    {
      "service": "string — one row per ranked scenario / cause",
      "min": 80,
      "max": 350,
      "currency": "{$currency}",
      "formatted_min": "{$currency} 80",
      "formatted_max": "{$currency} 350"
    }
  ],
  "diagnostic_codes_to_check": [
    {
      "code": "P0401",
      "meaning": "string",
      "related_cause": "string"
    }
  ],
  "next_workshop_questions": [
    "string"
  ],
  "ai_insights": [
    "string — brand/model specific tips, what NOT to replace first"
  ],
  "related_issues": [
    "string"
  ],
  "requires_immediate_attention": false,
  "ai_model_version": "gpt-4o"
}

Hard requirements:
- 3–5 likely_causes, ranked, with probabilities.
- First recommended_actions item = read/clear/document DTCs (unless DTC already provided — then interpret that code first).
- Do not recommend buying a random part before scanning when no DTC exists.
- All estimated_costs.currency must be "{$currency}".
- Empty strings for any video/image URL fields.
PROMPT;
    }

    private function normalizeResult(array $result, array $data): array
    {
        $obdCode = trim((string) ($data['obd_code'] ?? ''));
        $hasDtc = $obdCode !== '';

        $causes = $result['likely_causes'] ?? [];
        if (!is_array($causes)) {
            $causes = [];
        }
        $causes = array_values(array_filter($causes, 'is_array'));

        // Cap overconfident scores without DTC
        $confidence = (int) ($result['confidence_score'] ?? 50);
        if (!$hasDtc && $confidence > 68) {
            $confidence = 68;
        }
        if (!$hasDtc && count($causes) <= 1 && $confidence > 55) {
            $confidence = 55;
        }
        if ($hasDtc && $confidence < 70) {
            $confidence = max($confidence, 70);
        }
        $result['confidence_score'] = $confidence;

        // Strip fake media
        foreach ($causes as &$cause) {
            $cause['part_image_url'] = '';
            $cause['suggested_parts'] = [];
            if (!isset($cause['typical_dtc_examples']) || !is_array($cause['typical_dtc_examples'])) {
                $cause['typical_dtc_examples'] = [];
            }
        }
        unset($cause);
        $result['likely_causes'] = $causes;

        $actions = $result['recommended_actions'] ?? [];
        if (!is_array($actions)) {
            $actions = [];
        }
        foreach ($actions as &$action) {
            if (!is_array($action)) {
                continue;
            }
            $action['video_url'] = '';
            $action['video_title'] = $action['video_title'] ?? '';
            $action['video_duration'] = '';
            $action['video_company'] = '';
        }
        unset($action);
        $result['recommended_actions'] = array_values(array_filter($actions, 'is_array'));

        $costs = $result['estimated_costs'] ?? [];
        if (!is_array($costs)) {
            $costs = [];
        }
        foreach ($costs as &$cost) {
            if (!is_array($cost)) {
                continue;
            }
            $cost['currency'] = 'EUR';
            $min = $cost['min'] ?? null;
            $max = $cost['max'] ?? null;
            $cost['formatted_min'] = $min !== null ? 'EUR ' . $min : ($cost['formatted_min'] ?? '');
            $cost['formatted_max'] = $max !== null ? 'EUR ' . $max : ($cost['formatted_max'] ?? '');
        }
        unset($cost);
        $result['estimated_costs'] = array_values(array_filter($costs, 'is_array'));

        $result['repair_videos'] = [];
        $result['suggested_parts_for_purchase'] = [];

        if (empty($result['hypothesis_disclaimer'])) {
            $result['hypothesis_disclaimer'] = $hasDtc
                ? 'AI-assisted interpretation of the reported fault code. Confirm with a live scan and workshop inspection.'
                : 'Differential diagnosis without a live OBD/VCDS code. Read the fault codes before replacing parts.';
        }

        if (empty($result['data_quality'])) {
            $result['data_quality'] = $hasDtc ? 'high' : (count($causes) >= 3 ? 'medium' : 'low');
        }

        $result['diagnostic_codes_to_check'] = array_values(array_filter(
            is_array($result['diagnostic_codes_to_check'] ?? null) ? $result['diagnostic_codes_to_check'] : [],
            'is_array'
        ));
        $result['next_workshop_questions'] = array_values(array_filter(
            is_array($result['next_workshop_questions'] ?? null) ? $result['next_workshop_questions'] : [],
            'is_string'
        ));

        return $result;
    }
}
