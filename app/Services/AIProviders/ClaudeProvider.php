<?php

namespace App\Services\AIProviders;

use App\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;
    private float $temperature;
    private int $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('services.claude.api_key') ?? '';
        $this->apiUrl = config('services.claude.api_url', 'https://api.anthropic.com/v1');
        $this->model = config('services.claude.model', 'claude-3-sonnet-20240229');
        $this->temperature = config('services.claude.temperature', 0.3);
        $this->maxTokens = config('services.claude.max_tokens', 2000);
    }

    public function getProviderName(): string
    {
        return 'claude';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function getEstimatedCost(array $data): float
    {
        // Claude pricing: ~$0.015 per 1K input tokens, ~$0.075 per 1K output tokens
        // Estimated cost per diagnosis: ~$0.05-0.08
        return 0.06;
    }

    public function analyzeDiagnosis(array $data): array
    {
        if (!$this->isAvailable()) {
            throw new \Exception('Claude API key is not set.');
        }

        $prompt = $this->buildPrompt($data);
        
        try {
            $response = Http::timeout(60)->withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01'
            ])->post("{$this->apiUrl}/messages", [
                'model' => $this->model,
                'max_tokens' => $this->maxTokens,
                'temperature' => $this->temperature,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

            if (!$response->successful()) {
                Log::error('Claude API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Claude API request failed: ' . $response->body());
            }

            $responseData = $response->json();
            
            if (!isset($responseData['content'][0]['text'])) {
                Log::error('Claude API: Unexpected response format', ['response' => $responseData]);
                throw new \Exception('Unexpected response format from Claude API.');
            }

            $jsonString = $responseData['content'][0]['text'];
            $parsedResult = json_decode($jsonString, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Claude API: Failed to parse JSON response', ['response' => $jsonString, 'error' => json_last_error_msg()]);
                throw new \Exception('Failed to parse Claude API response.');
            }
            
            // Add AI provider to the result for tracking
            $parsedResult['ai_provider'] = $this->getProviderName();

            return $parsedResult;

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Claude API Request Failed: ' . $e->getMessage(), [
                'status' => $e->response->status(),
                'response' => $e->response->json(),
                'data' => $data
            ]);
            throw new \Exception('Claude API request failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Claude API Error: ' . $e->getMessage(), ['data' => $data]);
            throw new \Exception('Claude API error: ' . $e->getMessage());
        }
    }

    private function buildPrompt(array $data): string
    {
        $currency = $data['currency_code'] ?? 'USD';
        $language = $data['user_language'] ?? 'en';

        $symptomsList = implode(', ', $data['symptoms'] ?? []);

        return "You are a professional automotive diagnostic expert. Analyze the following vehicle diagnosis request and provide a comprehensive response in {$language} language.

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
  \"ai_model_version\": \"claude-3-sonnet\"
}

Provide accurate, professional diagnosis based on the symptoms and vehicle information.";
    }
}
