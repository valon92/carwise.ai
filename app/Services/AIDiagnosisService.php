<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIDiagnosisService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->apiUrl = config('services.openai.api_url', 'https://api.openai.com/v1');
    }

    /**
     * Analyze diagnosis data using AI.
     */
    public function analyzeDiagnosis(array $data): array
    {
        try {
            // For demo purposes, we'll use a mock AI response
            // In production, this would call OpenAI or another AI service
            return $this->generateMockAIResponse($data);
            
            // Uncomment below for real AI integration
            // return $this->callOpenAI($data);
            
        } catch (\Exception $e) {
            Log::error('AI Diagnosis Service Error: ' . $e->getMessage());
            throw new \Exception('AI analysis failed: ' . $e->getMessage());
        }
    }

    /**
     * Call OpenAI API for diagnosis analysis.
     */
    private function callOpenAI(array $data): array
    {
        $prompt = $this->buildPrompt($data);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert automotive diagnostic AI. Analyze vehicle problems and provide detailed, accurate diagnoses with confidence scores and recommended actions.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 2000,
            'temperature' => 0.3
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API request failed: ' . $response->body());
        }

        $aiResponse = $response->json();
        return $this->parseAIResponse($aiResponse['choices'][0]['message']['content']);
    }

    /**
     * Build prompt for AI analysis.
     */
    private function buildPrompt(array $data): string
    {
        $vehicleInfo = $data['vehicle_info'];
        $symptoms = $data['symptoms'] ?? [];
        $description = $data['description'];

        $prompt = "Analyze this automotive diagnosis request:\n\n";
        $prompt .= "Vehicle: {$vehicleInfo['year']} {$vehicleInfo['make']} {$vehicleInfo['model']}\n";
        $prompt .= "Mileage: " . ($vehicleInfo['mileage'] ?? 'Unknown') . " miles\n";
        $prompt .= "Engine: " . ($vehicleInfo['engine_type'] ?? 'Unknown') . " " . ($vehicleInfo['engine_size'] ?? '') . "\n\n";
        
        if (!empty($symptoms)) {
            $prompt .= "Reported Symptoms: " . implode(', ', $symptoms) . "\n\n";
        }
        
        $prompt .= "Problem Description: {$description}\n\n";
        
        $prompt .= "Please provide a detailed analysis in JSON format with the following structure:\n";
        $prompt .= "{\n";
        $prompt .= "  \"problem_title\": \"Brief title of the main issue\",\n";
        $prompt .= "  \"problem_description\": \"Detailed description of the problem\",\n";
        $prompt .= "  \"severity\": \"low|medium|high|critical\",\n";
        $prompt .= "  \"confidence_score\": 85,\n";
        $prompt .= "  \"likely_causes\": [\n";
        $prompt .= "    {\"title\": \"Cause 1\", \"description\": \"Description\", \"probability\": 75},\n";
        $prompt .= "    {\"title\": \"Cause 2\", \"description\": \"Description\", \"probability\": 60}\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"recommended_actions\": [\n";
        $prompt .= "    {\"title\": \"Action 1\", \"description\": \"Description\", \"urgency\": \"Immediate\"},\n";
        $prompt .= "    {\"title\": \"Action 2\", \"description\": \"Description\", \"urgency\": \"Within 1 week\"}\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"estimated_costs\": [\n";
        $prompt .= "    {\"service\": \"Service Name\", \"min\": 100, \"max\": 300}\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"requires_immediate_attention\": false\n";
        $prompt .= "}";

        return $prompt;
    }

    /**
     * Parse AI response into structured data.
     */
    private function parseAIResponse(string $response): array
    {
        // Extract JSON from response
        $jsonStart = strpos($response, '{');
        $jsonEnd = strrpos($response, '}') + 1;
        
        if ($jsonStart === false || $jsonEnd === false) {
            throw new \Exception('Invalid AI response format');
        }
        
        $jsonString = substr($response, $jsonStart, $jsonEnd - $jsonStart);
        $data = json_decode($jsonString, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse AI response JSON');
        }
        
        return $data;
    }

    /**
     * Generate mock AI response for demo purposes.
     */
    private function generateMockAIResponse(array $data): array
    {
        $symptoms = $data['symptoms'] ?? [];
        $description = strtolower($data['description']);
        
        // Simple rule-based mock responses
        if (str_contains($description, 'engine') && str_contains($description, 'start')) {
            return [
                'problem_title' => 'Engine Starting Issues',
                'problem_description' => 'The vehicle is experiencing difficulty starting, which could be related to the ignition system, fuel delivery, or battery.',
                'severity' => 'high',
                'confidence_score' => 85,
                'likely_causes' => [
                    [
                        'title' => 'Battery Issues',
                        'description' => 'Weak or dead battery preventing engine from starting.',
                        'probability' => 70
                    ],
                    [
                        'title' => 'Starter Motor Problems',
                        'description' => 'Faulty starter motor not engaging properly.',
                        'probability' => 60
                    ],
                    [
                        'title' => 'Fuel System Issues',
                        'description' => 'Fuel pump or fuel filter problems preventing fuel delivery.',
                        'probability' => 45
                    ]
                ],
                'recommended_actions' => [
                    [
                        'title' => 'Check Battery Voltage',
                        'description' => 'Test battery voltage and connections.',
                        'urgency' => 'Immediate'
                    ],
                    [
                        'title' => 'Inspect Starter Motor',
                        'description' => 'Have starter motor tested by a professional.',
                        'urgency' => 'Within 1 week'
                    ],
                    [
                        'title' => 'Fuel System Diagnostic',
                        'description' => 'Check fuel pump and filter condition.',
                        'urgency' => 'Within 2 weeks'
                    ]
                ],
                'estimated_costs' => [
                    ['service' => 'Battery Replacement', 'min' => 150, 'max' => 300],
                    ['service' => 'Starter Motor Replacement', 'min' => 300, 'max' => 600],
                    ['service' => 'Fuel Pump Replacement', 'min' => 400, 'max' => 800]
                ],
                'requires_immediate_attention' => true,
                'model_version' => '1.0'
            ];
        }
        
        if (str_contains($description, 'noise') || str_contains($description, 'sound')) {
            return [
                'problem_title' => 'Unusual Engine Noises',
                'problem_description' => 'The vehicle is producing unusual sounds that may indicate mechanical issues requiring attention.',
                'severity' => 'medium',
                'confidence_score' => 75,
                'likely_causes' => [
                    [
                        'title' => 'Worn Engine Components',
                        'description' => 'Timing belt, bearings, or other engine components may be worn.',
                        'probability' => 65
                    ],
                    [
                        'title' => 'Exhaust System Issues',
                        'description' => 'Loose or damaged exhaust components causing noise.',
                        'probability' => 55
                    ],
                    [
                        'title' => 'Accessory Belt Problems',
                        'description' => 'Worn or loose accessory belts causing squealing or grinding.',
                        'probability' => 50
                    ]
                ],
                'recommended_actions' => [
                    [
                        'title' => 'Engine Inspection',
                        'description' => 'Have a mechanic inspect the engine for worn components.',
                        'urgency' => 'Within 1 week'
                    ],
                    [
                        'title' => 'Exhaust System Check',
                        'description' => 'Inspect exhaust system for leaks or damage.',
                        'urgency' => 'Within 2 weeks'
                    ]
                ],
                'estimated_costs' => [
                    ['service' => 'Engine Inspection', 'min' => 100, 'max' => 200],
                    ['service' => 'Exhaust Repair', 'min' => 200, 'max' => 500]
                ],
                'requires_immediate_attention' => false,
                'model_version' => '1.0'
            ];
        }
        
        // Default response
        return [
            'problem_title' => 'General Vehicle Issue',
            'problem_description' => 'Based on the symptoms described, there appears to be a vehicle issue that requires professional diagnosis.',
            'severity' => 'medium',
            'confidence_score' => 60,
            'likely_causes' => [
                [
                    'title' => 'Multiple Potential Causes',
                    'description' => 'The symptoms could be related to several different systems.',
                    'probability' => 50
                ]
            ],
            'recommended_actions' => [
                [
                    'title' => 'Professional Diagnostic',
                    'description' => 'Schedule a comprehensive diagnostic with a certified mechanic.',
                    'urgency' => 'Within 1 week'
                ]
            ],
            'estimated_costs' => [
                ['service' => 'Diagnostic Fee', 'min' => 100, 'max' => 200]
            ],
            'requires_immediate_attention' => false,
            'model_version' => '1.0'
        ];
    }
}
