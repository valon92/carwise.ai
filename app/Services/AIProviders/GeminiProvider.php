<?php

namespace App\Services\AIProviders;

use App\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;
    private float $temperature;
    private int $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->apiUrl = config('services.gemini.api_url');
        $this->model = config('services.gemini.model');
        $this->temperature = config('services.gemini.temperature');
        $this->maxTokens = config('services.gemini.max_tokens');
    }

    public function analyzeDiagnosis(array $data): array
    {
        if (!$this->isAvailable()) {
            throw new \Exception('Gemini API is not available');
        }

        $prompt = $this->buildPrompt($data);
        
        try {
            $response = Http::timeout(30)->post(
                "{$this->apiUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => $this->temperature,
                        'maxOutputTokens' => $this->maxTokens,
                        'topP' => 0.8,
                        'topK' => 10
                    ],
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ]
                    ]
                ]
            );

            if (!$response->successful()) {
                Log::error('Gemini API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Gemini API request failed: ' . $response->body());
            }

            $responseData = $response->json();
            
            if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                throw new \Exception('Invalid Gemini API response format');
            }

            $aiResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
            
            return $this->parseResponse($aiResponse, $data);

        } catch (\Exception $e) {
            Log::error('Gemini API error', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function getProviderName(): string
    {
        return 'gemini';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey) && 
               $this->apiKey !== 'your_gemini_api_key_here';
    }

    public function getEstimatedCost(array $data): float
    {
        // Gemini is free for most use cases
        return 0.0;
    }

    private function buildPrompt(array $data): string
    {
        $symptoms = $data['symptoms'] ?? [];
        $description = $data['description'] ?? '';
        $language = $data['user_language'] ?? 'en';
        $currency = $data['currency_code'] ?? 'USD';

        $prompt = "You are a professional automotive diagnostic expert. Analyze the following vehicle information and provide a detailed diagnosis in {$language} language.

Vehicle Information:
- Make: {$data['make']}
- Model: {$data['model']}
- Year: {$data['year']}
- Mileage: {$data['mileage']} km
- Engine Type: {$data['engine_type']}
- Engine Size: {$data['engine_size']}

Symptoms: " . implode(', ', $symptoms) . "
Description: {$description}

Please provide a comprehensive diagnosis in JSON format with the following structure:
{
    \"problem_title\": \"Brief title of the problem\",
    \"problem_description\": \"Detailed description of the problem\",
    \"severity\": \"low|medium|high|critical\",
    \"confidence_score\": 85,
    \"likely_causes\": [
        {
            \"title\": \"Cause title\",
            \"description\": \"Detailed explanation\",
            \"probability\": 80
        }
    ],
    \"recommended_actions\": [
        {
            \"title\": \"Action title\",
            \"description\": \"Detailed steps\",
            \"urgency\": \"Immediate|Within 24 hours|Within 1 week|Within 2 weeks\"
        }
    ],
    \"estimated_costs\": [
        {
            \"service\": \"Service name\",
            \"min\": 100,
            \"max\": 300,
            \"currency\": \"{$currency}\"
        }
    ],
    \"requires_immediate_attention\": true,
    \"ai_insights\": \"Additional insights and recommendations\"
}

Respond ONLY with valid JSON, no additional text.";

        return $prompt;
    }

    private function parseResponse(string $aiResponse, array $data): array
    {
        try {
            // Clean the response - remove any markdown formatting
            $cleanResponse = preg_replace('/```json\s*/', '', $aiResponse);
            $cleanResponse = preg_replace('/```\s*$/', '', $cleanResponse);
            $cleanResponse = trim($cleanResponse);

            $parsed = json_decode($cleanResponse, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from Gemini: ' . json_last_error_msg());
            }

            // Validate required fields
            $requiredFields = ['problem_title', 'problem_description', 'severity', 'confidence_score'];
            foreach ($requiredFields as $field) {
                if (!isset($parsed[$field])) {
                    throw new \Exception("Missing required field: {$field}");
                }
            }

            // Ensure severity is valid
            $validSeverities = ['low', 'medium', 'high', 'critical'];
            if (!in_array($parsed['severity'], $validSeverities)) {
                $parsed['severity'] = 'medium';
            }

            // Ensure confidence score is valid
            $parsed['confidence_score'] = max(0, min(100, (int)$parsed['confidence_score']));

            // Add model version
            $parsed['model_version'] = '1.0';
            $parsed['ai_provider'] = 'gemini';

            return $parsed;

        } catch (\Exception $e) {
            Log::error('Failed to parse Gemini response', [
                'response' => $aiResponse,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Failed to parse AI response: ' . $e->getMessage());
        }
    }
}
