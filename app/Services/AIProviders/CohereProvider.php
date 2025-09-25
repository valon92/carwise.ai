<?php

namespace App\Services\AIProviders;

use App\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CohereProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;
    private float $temperature;
    private int $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('services.cohere.api_key') ?? '';
        $this->apiUrl = config('services.cohere.api_url', 'https://api.cohere.ai/v1');
        $this->model = config('services.cohere.model', 'command');
        $this->temperature = config('services.cohere.temperature', 0.3);
        $this->maxTokens = config('services.cohere.max_tokens', 2000);
    }

    public function getProviderName(): string
    {
        return 'cohere';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_cohere_api_key_here';
    }

    public function getEstimatedCost(array $data): float
    {
        // Cohere free tier: 1000 requests/month
        // Paid: $0.0015 per 1K tokens
        return 0.0;
    }

    public function analyzeDiagnosis(array $data): array
    {
        if (!$this->isAvailable()) {
            throw new \Exception('Cohere API key is not set.');
        }

        $prompt = $this->buildPrompt($data);
        
        try {
            $response = Http::timeout(60)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post("{$this->apiUrl}/chat", [
                'model' => $this->model,
                'message' => $prompt,
                'temperature' => $this->temperature,
                'max_tokens' => $this->maxTokens,
                'stop_sequences' => ['\n\n---']
            ]);

            if (!$response->successful()) {
                Log::error('Cohere API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Cohere API request failed: ' . $response->body());
            }

            $responseData = $response->json();
            
            if (!isset($responseData['text'])) {
                Log::error('Cohere API: Unexpected response format', ['response' => $responseData]);
                throw new \Exception('Unexpected response format from Cohere API.');
            }

            $jsonString = $responseData['text'];
            
            // Clean up the response
            $jsonString = trim($jsonString);
            if (strpos($jsonString, '```json') !== false) {
                $jsonString = preg_replace('/```json\s*/', '', $jsonString);
                $jsonString = preg_replace('/\s*```/', '', $jsonString);
            }
            
            $parsedResult = json_decode($jsonString, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Cohere API: Failed to parse JSON response', ['response' => $jsonString, 'error' => json_last_error_msg()]);
                throw new \Exception('Failed to parse Cohere API response.');
            }
            
            // Add AI provider to the result for tracking
            $parsedResult['ai_provider'] = $this->getProviderName();

            return $parsedResult;

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Cohere API Request Failed: ' . $e->getMessage(), [
                'status' => $e->response->status(),
                'response' => $e->response->json(),
                'data' => $data
            ]);
            throw new \Exception('Cohere API request failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Cohere API Error: ' . $e->getMessage(), ['data' => $data]);
            throw new \Exception('Cohere API error: ' . $e->getMessage());
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
```json
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
  \"ai_model_version\": \"cohere-command\"
}
```

Provide accurate, professional diagnosis based on the symptoms and vehicle information.";
    }
}
