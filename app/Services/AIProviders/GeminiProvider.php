<?php

namespace App\Services\AIProviders;

use App\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class GeminiProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = Config::get('services.gemini.api_key') ?? '';
        $this->baseUrl = Config::get('services.gemini.base_url') ?? 'https://generativelanguage.googleapis.com/v1beta';
        $this->model = Config::get('services.gemini.model') ?? 'gemini-1.5-flash';

        if (empty($this->apiKey)) {
            Log::warning('Gemini API key is not configured');
        }
    }

    public function isAvailable(): bool
    {
        if (empty($this->apiKey)) {
            return false;
        }

        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/models', ['key' => $this->apiKey]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Gemini provider availability check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function getProviderInfo(): array
    {
        return [
            'name' => 'Google Gemini',
            'provider' => 'gemini',
            'model' => $this->model,
            'available' => $this->isAvailable(),
            'features' => ['text_generation', 'multimodal', 'code_generation', 'multilingual'],
            'limits' => [
                'max_tokens' => 1048576,
                'rate_limit' => '60 requests/minute',
                'context_window' => '1M tokens'
            ]
        ];
    }

    public function analyzeDiagnosis(array $data): array
    {
        try {
            Log::info('Starting Gemini diagnosis analysis');
            $prompt = $this->buildDiagnosisPrompt($data);
            $response = $this->makeRequest($prompt);
            $analysis = $this->parseResponse($response, $data['user_language'] ?? 'en');
            Log::info('Gemini diagnosis completed');
            return $analysis;
        } catch (\Exception $e) {
            Log::error('Gemini diagnosis failed', ['error' => $e->getMessage()]);
            throw new \Exception('Gemini analysis failed: ' . $e->getMessage());
        }
    }

    private function buildDiagnosisPrompt(array $data): string
    {
        $language = $data['user_language'] ?? 'en';
        $make = $data['make'] ?? $data['car_brand'] ?? 'Unknown';
        $model = $data['model'] ?? $data['car_model'] ?? 'Unknown';
        $year = $data['year'] ?? $data['car_year'] ?? 'Unknown';
        $description = $data['description'] ?? $data['problem_description'] ?? '';
        
        $symptoms = $data['symptoms'] ?? [];
        if (is_string($symptoms)) {
            $symptoms = json_decode($symptoms, true) ?? [$symptoms];
        }
        $symptomsList = implode(', ', $symptoms);

        $languageNames = [
            'sq' => 'Albanian', 'en' => 'English', 'de' => 'German', 'fr' => 'French',
            'es' => 'Spanish', 'it' => 'Italian', 'pt' => 'Portuguese', 'ru' => 'Russian',
            'zh' => 'Chinese', 'ja' => 'Japanese', 'ko' => 'Korean', 'ar' => 'Arabic',
            'hi' => 'Hindi', 'tr' => 'Turkish', 'nl' => 'Dutch', 'pl' => 'Polish',
            'sv' => 'Swedish', 'no' => 'Norwegian', 'da' => 'Danish', 'fi' => 'Finnish',
            'el' => 'Greek', 'he' => 'Hebrew', 'th' => 'Thai', 'vi' => 'Vietnamese'
        ];
        $languageName = $languageNames[$language] ?? 'English';

        return "You are an automotive diagnostic expert. Analyze this vehicle issue and respond in {$languageName}:

Vehicle: {$make} {$model} {$year}
Symptoms: {$symptomsList}
Description: {$description}

Provide structured analysis with:
1. Diagnosis with detailed problem description
2. Likely causes with part names and images
3. Recommended actions with repair videos
4. Suggested parts for purchase with links to car-parts page
5. Urgency level (critical/high/medium/low)
6. Estimated costs
7. Prevention tips

Include:
- Part images and descriptions for problematic components
- Licensed repair videos from authorized companies
- Direct links to car-parts page for purchasing suggested parts
- Detailed compatibility information
- Professional repair instructions

Respond entirely in {$languageName}.";
    }

    private function makeRequest(string $prompt, array $parameters = []): array
    {
        $payload = [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => array_merge([
                'temperature' => 0.3,
                'maxOutputTokens' => 2048,
                'topK' => 40,
                'topP' => 0.95
            ], $parameters)
        ];

        $response = Http::timeout(60)
            ->post($this->baseUrl . "/models/{$this->model}:generateContent?key={$this->apiKey}", $payload);

        if (!$response->successful()) {
            $error = $response->json()['error'] ?? ['message' => 'Unknown error'];
            throw new \Exception('Gemini API error: ' . $error['message']);
        }

        $responseData = $response->json();
        if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \Exception('Invalid response format from Gemini API');
        }

        return $responseData;
    }

    private function parseResponse(array $response, string $language): array
    {
        $content = $response['candidates'][0]['content']['parts'][0]['text'];
        
        // Basic parsing - extract sections or use full content
        $diagnosis = $content;
        $recommendations = $this->generateFallbackRecommendations($language);
        $urgency = 'medium';
        $estimatedCost = $this->generateFallbackCost($language);
        
        // Try to extract urgency level
        if (preg_match('/urgency[:\s]*([a-z]+)/i', $content, $matches)) {
            $urgencyText = strtolower($matches[1]);
            if (in_array($urgencyText, ['critical', 'high', 'medium', 'low'])) {
                $urgency = $urgencyText;
            }
        }

        return [
            'diagnosis' => $diagnosis,
            'recommendations' => $recommendations,
            'urgency' => $urgency,
            'estimated_cost' => $estimatedCost,
            'ai_insights' => "Analysis powered by Google Gemini AI",
            'confidence_score' => 0.85,
            'provider' => 'gemini',
            'model' => $this->model
        ];
    }

    private function generateFallbackRecommendations(string $language): string
    {
        $recommendations = [
            'en' => "1. Consult with a qualified mechanic\n2. Keep record of symptoms\n3. Check warranty status",
            'sq' => "1. Konsultohuni me mekanik të kualifikuar\n2. Mbani shënim simptomat\n3. Kontrolloni statusin e garancisë",
            'de' => "1. Konsultieren Sie einen Mechaniker\n2. Symptome dokumentieren\n3. Garantiestatus prüfen"
        ];
        return $recommendations[$language] ?? $recommendations['en'];
    }

    private function generateFallbackCost(string $language): string
    {
        $costs = [
            'en' => "Cost varies depending on specific issue. Consult mechanic for accurate estimate.",
            'sq' => "Kostoja ndryshon në varësi të problemit. Konsultohuni me mekanik për vlerësim të saktë.",
            'de' => "Kosten variieren je nach Problem. Mechaniker für genaue Schätzung konsultieren."
        ];
        return $costs[$language] ?? $costs['en'];
    }

    public function getProviderName(): string
    {
        return 'Google Gemini';
    }

    public function getEstimatedCost(array $data): float
    {
        // Gemini has free tier - return 0 for cost estimation
        return 0.0;
    }
}