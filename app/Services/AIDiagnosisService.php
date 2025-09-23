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
     * Generate enhanced mock AI response for demo purposes.
     */
    private function generateMockAIResponse(array $data): array
    {
        $symptoms = $data['symptoms'] ?? [];
        $description = strtolower($data['description']);
        $vehicleInfo = $data['vehicle_info'] ?? [];
        
        // Enhanced rule-based mock responses with more sophisticated analysis
        if (str_contains($description, 'engine') && (str_contains($description, 'start') || str_contains($description, 'won\'t start'))) {
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
        
        // Enhanced analysis based on symptoms
        if (in_array('Warning lights on', $symptoms)) {
            return [
                'problem_title' => 'Dashboard Warning Lights',
                'problem_description' => 'Warning lights on the dashboard indicate a system malfunction that requires immediate attention.',
                'severity' => 'high',
                'confidence_score' => 90,
                'likely_causes' => [
                    [
                        'title' => 'Engine Management System',
                        'description' => 'Check Engine Light (CEL) indicates engine or emissions system issues.',
                        'probability' => 80
                    ],
                    [
                        'title' => 'ABS System',
                        'description' => 'Anti-lock Braking System warning may indicate brake system problems.',
                        'probability' => 60
                    ],
                    [
                        'title' => 'Airbag System',
                        'description' => 'Airbag warning light indicates safety system malfunction.',
                        'probability' => 50
                    ]
                ],
                'recommended_actions' => [
                    [
                        'title' => 'Immediate Diagnostic Scan',
                        'description' => 'Use OBD-II scanner to read diagnostic trouble codes.',
                        'urgency' => 'Immediate'
                    ],
                    [
                        'title' => 'Professional Inspection',
                        'description' => 'Have certified mechanic inspect the affected systems.',
                        'urgency' => 'Within 24 hours'
                    ]
                ],
                'estimated_costs' => [
                    ['service' => 'Diagnostic Scan', 'min' => 50, 'max' => 100],
                    ['service' => 'System Repair', 'min' => 200, 'max' => 1000]
                ],
                'requires_immediate_attention' => true,
                'model_version' => '1.0'
            ];
        }

        if (in_array('Poor fuel economy', $symptoms)) {
            return [
                'problem_title' => 'Reduced Fuel Efficiency',
                'problem_description' => 'The vehicle is consuming more fuel than normal, indicating potential efficiency issues.',
                'severity' => 'medium',
                'confidence_score' => 75,
                'likely_causes' => [
                    [
                        'title' => 'Dirty Air Filter',
                        'description' => 'Clogged air filter restricts airflow to the engine.',
                        'probability' => 70
                    ],
                    [
                        'title' => 'Faulty Oxygen Sensor',
                        'description' => 'O2 sensor not properly regulating fuel mixture.',
                        'probability' => 65
                    ],
                    [
                        'title' => 'Worn Spark Plugs',
                        'description' => 'Old or fouled spark plugs reduce combustion efficiency.',
                        'probability' => 60
                    ]
                ],
                'recommended_actions' => [
                    [
                        'title' => 'Replace Air Filter',
                        'description' => 'Check and replace air filter if dirty.',
                        'urgency' => 'Within 1 week'
                    ],
                    [
                        'title' => 'Spark Plug Service',
                        'description' => 'Inspect and replace spark plugs if needed.',
                        'urgency' => 'Within 2 weeks'
                    ]
                ],
                'estimated_costs' => [
                    ['service' => 'Air Filter Replacement', 'min' => 20, 'max' => 50],
                    ['service' => 'Spark Plug Replacement', 'min' => 100, 'max' => 300]
                ],
                'requires_immediate_attention' => false,
                'model_version' => '1.0'
            ];
        }

        if (in_array('Overheating', $symptoms)) {
            return [
                'problem_title' => 'Engine Overheating',
                'problem_description' => 'Engine temperature is exceeding normal operating range, which can cause serious damage.',
                'severity' => 'critical',
                'confidence_score' => 95,
                'likely_causes' => [
                    [
                        'title' => 'Coolant Leak',
                        'description' => 'Loss of coolant due to leak in the cooling system.',
                        'probability' => 85
                    ],
                    [
                        'title' => 'Faulty Thermostat',
                        'description' => 'Thermostat not opening properly to allow coolant flow.',
                        'probability' => 70
                    ],
                    [
                        'title' => 'Water Pump Failure',
                        'description' => 'Water pump not circulating coolant through the engine.',
                        'probability' => 60
                    ]
                ],
                'recommended_actions' => [
                    [
                        'title' => 'Stop Driving Immediately',
                        'description' => 'Pull over and turn off engine to prevent damage.',
                        'urgency' => 'Immediate'
                    ],
                    [
                        'title' => 'Check Coolant Level',
                        'description' => 'Inspect coolant reservoir and radiator for leaks.',
                        'urgency' => 'Immediate'
                    ],
                    [
                        'title' => 'Emergency Towing',
                        'description' => 'Have vehicle towed to repair facility.',
                        'urgency' => 'Immediate'
                    ]
                ],
                'estimated_costs' => [
                    ['service' => 'Cooling System Repair', 'min' => 300, 'max' => 800],
                    ['service' => 'Engine Damage Assessment', 'min' => 200, 'max' => 500]
                ],
                'requires_immediate_attention' => true,
                'model_version' => '1.0'
            ];
        }

        // Default enhanced response
        return [
            'problem_title' => 'Vehicle Diagnostic Required',
            'problem_description' => 'Based on the symptoms described, a comprehensive diagnostic is recommended to identify the root cause.',
            'severity' => 'medium',
            'confidence_score' => 65,
            'likely_causes' => [
                [
                    'title' => 'Multiple System Analysis',
                    'description' => 'Symptoms may be related to engine, transmission, electrical, or other systems.',
                    'probability' => 55
                ],
                [
                    'title' => 'Wear and Tear',
                    'description' => 'Normal aging components may require maintenance or replacement.',
                    'probability' => 45
                ]
            ],
            'recommended_actions' => [
                [
                    'title' => 'Comprehensive Diagnostic',
                    'description' => 'Schedule a full vehicle inspection with diagnostic equipment.',
                    'urgency' => 'Within 1 week'
                ],
                [
                    'title' => 'Preventive Maintenance',
                    'description' => 'Consider routine maintenance to prevent future issues.',
                    'urgency' => 'Within 2 weeks'
                ]
            ],
            'estimated_costs' => [
                ['service' => 'Diagnostic Fee', 'min' => 100, 'max' => 200],
                ['service' => 'Maintenance Service', 'min' => 150, 'max' => 400]
            ],
            'requires_immediate_attention' => false,
            'model_version' => '1.0',
            'ai_insights' => [
                'Based on vehicle age and mileage, consider preventive maintenance.',
                'Regular diagnostic checks can prevent costly repairs.',
                'Document symptoms for better diagnostic accuracy.'
            ]
        ];
    }
}
