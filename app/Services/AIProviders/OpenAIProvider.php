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
        $this->apiUrl = config('services.openai.api_url');
        $this->model = config('services.openai.model');
        $this->temperature = config('services.openai.temperature');
        $this->maxTokens = config('services.openai.max_tokens');
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
        // OpenAI GPT-4 pricing: ~$0.03 per 1K input tokens, ~$0.06 per 1K output tokens
        // Estimated cost per diagnosis: ~$0.05-0.10
        return 0.08;
    }

    public function analyzeDiagnosis(array $data): array
    {
        if (!$this->isAvailable()) {
            throw new \Exception('OpenAI API key is not set.');
        }

        $prompt = $this->buildPrompt($data);
        
        try {
            $response = Http::timeout(60)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post("{$this->apiUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional automotive diagnostic expert with 20+ years of experience. Provide accurate, detailed, and actionable diagnosis for vehicle problems.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => $this->temperature,
                'max_tokens' => $this->maxTokens,
                'response_format' => ['type' => 'json_object']
            ]);

            if (!$response->successful()) {
                Log::error('OpenAI API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
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
                Log::error('OpenAI API: Failed to parse JSON response', ['response' => $jsonString, 'error' => json_last_error_msg()]);
                throw new \Exception('Failed to parse OpenAI API response.');
            }
            
            // Add AI provider to the result for tracking
            $parsedResult['ai_provider'] = $this->getProviderName();

            return $parsedResult;

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('OpenAI API Request Failed: ' . $e->getMessage(), [
                'status' => $e->response->status(),
                'response' => $e->response->json(),
                'data' => $data
            ]);
            throw new \Exception('OpenAI API request failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage(), ['data' => $data]);
            throw new \Exception('OpenAI API error: ' . $e->getMessage());
        }
    }

    private function buildPrompt(array $data): string
    {
        $currency = $data['currency_code'] ?? 'USD';
        $language = $data['user_language'] ?? 'en';

        $symptomsList = implode(', ', $data['symptoms'] ?? []);

        return "Analyze the following vehicle diagnosis request and provide a comprehensive response in {$language} language.

Vehicle Information:
- Make: {$data['make']}
- Model: {$data['model']}
- Year: {$data['year']}
- Mileage: {$data['mileage']} km
- Engine Type: {$data['engine_type']}
- Engine Size: {$data['engine_size']}

Symptoms: {$symptomsList}
Description: {$data['description']}

Please provide a detailed diagnosis in JSON format with the following structure:
{
  \"problem_title\": \"A concise title for the main problem\",
  \"problem_description\": \"A detailed description of the problem\",
  \"severity\": \"low|medium|high|critical\",
  \"confidence_score\": 85,
  \"likely_causes\": [
    {
      \"title\": \"Cause title\",
      \"description\": \"Detailed description\",
      \"probability\": 75
    }
  ],
  \"recommended_actions\": [
    {
      \"title\": \"Action title\",
      \"description\": \"Detailed description\",
      \"urgency\": \"Immediate|Within 1 week|Within 2 weeks\"
    }
  ],
  \"estimated_costs\": [
    {
      \"service\": \"Service name\",
      \"min\": 100,
      \"max\": 300,
      \"currency\": \"{$currency}\",
      \"formatted_min\": \"{$currency} 100\",
      \"formatted_max\": \"{$currency} 300\"
    }
  ],
  \"ai_insights\": [
    \"Additional professional insights\"
  ],
  \"related_issues\": [
    \"Other potential issues to watch for\"
  ],
  \"requires_immediate_attention\": true,
  \"ai_model_version\": \"gpt-4\"
}

Provide accurate, professional diagnosis based on the symptoms and vehicle information.";
    }
}
