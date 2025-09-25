<?php

namespace App\Services;

use App\Contracts\AIProviderInterface;
use App\Models\Currency;
use App\Services\AIProviders\GeminiProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIDiagnosisService
{
    protected AIProviderInterface $aiProvider;
    protected $fallbackEnabled;

    public function __construct()
    {
        $this->fallbackEnabled = config('app.ai_fallback_enabled', true);
        $this->initializeAIProvider();
    }

    /**
     * Initialize AI provider based on configuration
     */
    private function initializeAIProvider(): void
    {
        try {
            // Use AIProviderManager to get the best available provider
            $manager = new \App\Services\AIProviderManager();
            $this->aiProvider = $manager->getBestProvider();
        } catch (\Exception $e) {
            // If no providers are available, create a dummy provider that will use fallback
            $this->aiProvider = new class implements AIProviderInterface {
                public function analyzeDiagnosis(array $data): array { throw new \Exception('No AI provider available'); }
                public function getProviderName(): string { return 'none'; }
                public function isAvailable(): bool { return false; }
                public function getEstimatedCost(array $data): float { return 0.0; }
            };
        }
    }

    /**
     * Get current AI provider info
     */
    public function getProviderInfo(): array
    {
        return [
            'provider' => $this->aiProvider->getProviderName(),
            'available' => $this->aiProvider->isAvailable(),
            'fallback_enabled' => $this->fallbackEnabled
        ];
    }

    /**
     * Analyze diagnosis data using AI.
     */
    public function analyzeDiagnosis(array $data): array
    {
        try {
            // Try primary AI provider first
            if ($this->aiProvider->isAvailable()) {
                Log::info('Using AI provider: ' . $this->aiProvider->getProviderName());
                $result = $this->aiProvider->analyzeDiagnosis($data);
                
                // Translate response to user's language
                return $this->translateResponse($result, $data['user_language'] ?? 'en');
            }
            
            // Fallback to mock response if AI provider is not available
            if ($this->fallbackEnabled) {
                Log::warning('AI provider not available, using fallback response');
                return $this->generateMockAIResponse($data);
            }
            
            throw new \Exception('No AI provider available and fallback is disabled');
            
        } catch (\Exception $e) {
            Log::error('AI Diagnosis Service Error: ' . $e->getMessage());
            
            // Try fallback if primary provider fails
            if ($this->fallbackEnabled) {
                Log::warning('Primary AI provider failed, using fallback response');
                return $this->generateMockAIResponse($data);
            }
            
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
            'model' => config('services.openai.model', 'gpt-4'),
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
            'max_tokens' => config('services.openai.max_tokens', 2000),
            'temperature' => config('services.openai.temperature', 0.3)
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
        $userLanguage = $data['user_language'] ?? 'en';
        
        // Enhanced rule-based mock responses with more sophisticated analysis
        if (str_contains($description, 'engine') && (str_contains($description, 'start') || str_contains($description, 'won\'t start'))) {
            $response = [
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
                'estimated_costs' => $this->convertCosts([
                    ['service' => 'Battery Replacement', 'min' => 150, 'max' => 300],
                    ['service' => 'Starter Motor Replacement', 'min' => 300, 'max' => 600],
                    ['service' => 'Fuel Pump Replacement', 'min' => 400, 'max' => 800]
                ], $data['user_currency_id'] ?? null),
                'requires_immediate_attention' => true,
                'model_version' => '1.0',
                'ai_provider' => 'fallback'
            ];
            return $this->translateResponse($response, $userLanguage);
        }
        
        if (str_contains($description, 'noise') || str_contains($description, 'sound')) {
            $response = [
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
                'estimated_costs' => $this->convertCosts([
                    ['service' => 'Engine Inspection', 'min' => 100, 'max' => 200],
                    ['service' => 'Exhaust Repair', 'min' => 200, 'max' => 500]
                ], $data['user_currency_id'] ?? null),
                'requires_immediate_attention' => false,
                'model_version' => '1.0',
                'ai_provider' => 'fallback'
            ];
            return $this->translateResponse($response, $userLanguage);
        }
        
        // Enhanced analysis based on symptoms
        if (in_array('Warning lights on', $symptoms)) {
            $response = [
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
                'estimated_costs' => $this->convertCosts([
                    ['service' => 'Diagnostic Scan', 'min' => 50, 'max' => 100],
                    ['service' => 'System Repair', 'min' => 200, 'max' => 1000]
                ], $data['user_currency_id'] ?? null),
                'requires_immediate_attention' => true,
                'model_version' => '1.0',
                'ai_provider' => 'fallback'
            ];
            return $this->translateResponse($response, $userLanguage);
        }

        if (in_array('Poor fuel economy', $symptoms)) {
            $response = [
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
                'estimated_costs' => $this->convertCosts([
                    ['service' => 'Air Filter Replacement', 'min' => 20, 'max' => 50],
                    ['service' => 'Spark Plug Replacement', 'min' => 100, 'max' => 300]
                ], $data['user_currency_id'] ?? null),
                'requires_immediate_attention' => false,
                'model_version' => '1.0',
                'ai_provider' => 'fallback'
            ];
            return $this->translateResponse($response, $userLanguage);
        }

        if (in_array('Overheating', $symptoms)) {
            $response = [
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
                'estimated_costs' => $this->convertCosts([
                    ['service' => 'Cooling System Repair', 'min' => 300, 'max' => 800],
                    ['service' => 'Engine Damage Assessment', 'min' => 200, 'max' => 500]
                ], $data['user_currency_id'] ?? null),
                'requires_immediate_attention' => true,
                'model_version' => '1.0',
                'ai_provider' => 'fallback'
            ];
            return $this->translateResponse($response, $userLanguage);
        }

        // Default enhanced response
        $response = [
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
            'estimated_costs' => $this->convertCosts([
                ['service' => 'Diagnostic Fee', 'min' => 100, 'max' => 200],
                ['service' => 'Maintenance Service', 'min' => 150, 'max' => 400]
            ], $data['user_currency_id'] ?? null),
            'requires_immediate_attention' => false,
            'model_version' => '1.0',
            'ai_provider' => 'fallback',
            'ai_insights' => [
                'Based on vehicle age and mileage, consider preventive maintenance.',
                'Regular diagnostic checks can prevent costly repairs.',
                'Document symptoms for better diagnostic accuracy.'
            ]
        ];
        return $this->translateResponse($response, $userLanguage);
    }

    /**
     * Check if symptoms indicate engine issues
     */
    private function hasEngineSymptoms(array $symptoms, string $description): bool
    {
        $engineKeywords = ['ndalesa e motori', 'zhurma të çuditshme', 'motori nuk fillon', 'punë e ashpër', 'nxitim i dobët', 'tym nga shkarkimi', 'ngrohje e tepërt', 'konsum i lartë i karburantit'];
        
        foreach ($engineKeywords as $keyword) {
            if (in_array($keyword, $symptoms) || strpos($description, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if symptoms indicate transmission issues
     */
    private function hasTransmissionSymptoms(array $symptoms, string $description): bool
    {
        $transmissionKeywords = ['probleme me transmetimin', 'ndryshim i vështirë i shpejtësive', 'zhurma nga transmetimi'];
        
        foreach ($transmissionKeywords as $keyword) {
            if (in_array($keyword, $symptoms) || strpos($description, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if symptoms indicate electrical issues
     */
    private function hasElectricalSymptoms(array $symptoms, string $description): bool
    {
        $electricalKeywords = ['dritat e paralajmërimit janë ndezur', 'probleme elektrike', 'klima nuk funksionon', 'sistemi elektrik'];
        
        foreach ($electricalKeywords as $keyword) {
            if (in_array($keyword, $symptoms) || strpos($description, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get engine-specific diagnosis
     */
    private function getEngineDiagnosis(array $data, string $make): array
    {
        $userLanguage = $data['user_language'] ?? 'en';
        
        if ($userLanguage === 'sq') {
            return [
                'problem_title' => 'Probleme me Motorin',
                'problem_description' => 'Bazuar në simptomat e përshkruara, ka probleme të mundshme me motorin që kërkojnë vëmendje të menjëhershme.',
                'severity' => 'high',
                'confidence_score' => 80,
                'likely_causes' => [
                    [
                        'title' => 'Probleme me Sistemin e Karburantit',
                        'description' => 'Filtrat e karburantit të ndotur ose pompa e karburantit me defekt mund të shkaktojnë ndalesa të motori.',
                        'probability' => 70
                    ],
                    [
                        'title' => 'Probleme me Sistemin e Shkarkimit',
                        'description' => 'Filtrat e shkarkimit të bllokuar ose sensorët me defekt mund të shkaktojnë zhurma të çuditshme.',
                        'probability' => 60
                    ]
                ],
                'recommended_actions' => [
                    [
                        'title' => 'Kontroll i Menjëhershëm',
                        'description' => 'Kontaktoni një mekanik të certifikuar për kontroll të menjëhershëm të motorit.',
                        'urgency' => 'Immediate'
                    ],
                    [
                        'title' => 'Diagnostikim OBD-II',
                        'description' => 'Përdorni skaner OBD-II për të lexuar kodet e gabimeve të motorit.',
                        'urgency' => 'Within 24 hours'
                    ]
                ],
                'estimated_costs' => $this->convertCosts([
                    ['service' => 'Engine Diagnostic', 'min' => 150, 'max' => 300],
                    ['service' => 'Fuel System Service', 'min' => 200, 'max' => 500],
                    ['service' => 'Exhaust System Check', 'min' => 100, 'max' => 250]
                ], $data['user_currency_id'] ?? null),
                'requires_immediate_attention' => true,
                'model_version' => '1.0',
                'ai_provider' => 'fallback',
                'ai_insights' => [
                    'Problemet me motorin mund të përkeqësohen shpejt nëse nuk trajtohen.',
                    'Kontrolli i rregullt i motorit mund të parandalojë riparime të shtrenjta.',
                    'Dokumentoni të gjitha simptomat për diagnozë më të saktë.'
                ]
            ];
        }
        
        // English version
        return [
            'problem_title' => 'Engine Issues Detected',
            'problem_description' => 'Based on the described symptoms, there are potential engine problems that require immediate attention.',
            'severity' => 'high',
            'confidence_score' => 80,
            'likely_causes' => [
                [
                    'title' => 'Fuel System Problems',
                    'description' => 'Clogged fuel filters or defective fuel pump may cause engine stalling.',
                    'probability' => 70
                ],
                [
                    'title' => 'Exhaust System Issues',
                    'description' => 'Blocked exhaust filters or defective sensors may cause strange noises.',
                    'probability' => 60
                ]
            ],
            'recommended_actions' => [
                [
                    'title' => 'Immediate Inspection',
                    'description' => 'Contact a certified mechanic for immediate engine inspection.',
                    'urgency' => 'Immediate'
                ],
                [
                    'title' => 'OBD-II Diagnostic',
                    'description' => 'Use OBD-II scanner to read engine error codes.',
                    'urgency' => 'Within 24 hours'
                ]
            ],
            'estimated_costs' => $this->convertCosts([
                ['service' => 'Engine Diagnostic', 'min' => 150, 'max' => 300],
                ['service' => 'Fuel System Service', 'min' => 200, 'max' => 500],
                ['service' => 'Exhaust System Check', 'min' => 100, 'max' => 250]
            ], $data['user_currency_id'] ?? null),
            'requires_immediate_attention' => true,
            'model_version' => '1.0',
            'ai_provider' => 'fallback',
            'ai_insights' => [
                'Engine problems can worsen quickly if not addressed.',
                'Regular engine maintenance can prevent costly repairs.',
                'Document all symptoms for more accurate diagnosis.'
            ]
        ];
    }

    /**
     * Get transmission-specific diagnosis
     */
    private function getTransmissionDiagnosis(array $data, string $make): array
    {
        $userLanguage = $data['user_language'] ?? 'en';
        
        if ($userLanguage === 'sq') {
            return [
                'problem_title' => 'Probleme me Transmetimin',
                'problem_description' => 'Simptomat tregojnë probleme të mundshme me sistemin e transmetimit.',
                'severity' => 'medium',
                'confidence_score' => 75,
                'likely_causes' => [
                    [
                        'title' => 'Probleme me Lëngun e Transmetimit',
                        'description' => 'Lëngu i transmetimit i vjetër ose i ndotur mund të shkaktojë probleme me ndryshimin e shpejtësive.',
                        'probability' => 65
                    ]
                ],
                'recommended_actions' => [
                    [
                        'title' => 'Kontroll i Transmetimit',
                        'description' => 'Kontaktoni një specialist për kontroll të sistemit të transmetimit.',
                        'urgency' => 'Within 1 week'
                    ]
                ],
                'estimated_costs' => $this->convertCosts([
                    ['service' => 'Transmission Diagnostic', 'min' => 200, 'max' => 400],
                    ['service' => 'Transmission Service', 'min' => 300, 'max' => 600]
                ], $data['user_currency_id'] ?? null),
                'requires_immediate_attention' => false,
                'model_version' => '1.0',
                'ai_provider' => 'fallback',
                'ai_insights' => [
                    'Problemet me transmetimin mund të jenë të shtrenjta për t\'u riparuar.',
                    'Kontrolli i rregullt i lëngut të transmetimit është i rëndësishëm.'
                ]
            ];
        }
        
        // English version
        return [
            'problem_title' => 'Transmission Issues',
            'problem_description' => 'Symptoms indicate potential transmission system problems.',
            'severity' => 'medium',
            'confidence_score' => 75,
            'likely_causes' => [
                [
                    'title' => 'Transmission Fluid Problems',
                    'description' => 'Old or contaminated transmission fluid may cause shifting problems.',
                    'probability' => 65
                ]
            ],
            'recommended_actions' => [
                [
                    'title' => 'Transmission Inspection',
                    'description' => 'Contact a specialist for transmission system inspection.',
                    'urgency' => 'Within 1 week'
                ]
            ],
            'estimated_costs' => $this->convertCosts([
                ['service' => 'Transmission Diagnostic', 'min' => 200, 'max' => 400],
                ['service' => 'Transmission Service', 'min' => 300, 'max' => 600]
            ], $data['user_currency_id'] ?? null),
            'requires_immediate_attention' => false,
            'model_version' => '1.0',
            'ai_provider' => 'fallback',
            'ai_insights' => [
                'Transmission problems can be expensive to repair.',
                'Regular transmission fluid checks are important.'
            ]
        ];
    }

    /**
     * Get electrical-specific diagnosis
     */
    private function getElectricalDiagnosis(array $data, string $make): array
    {
        $userLanguage = $data['user_language'] ?? 'en';
        
        if ($userLanguage === 'sq') {
            return [
                'problem_title' => 'Probleme Elektrike',
                'problem_description' => 'Simptomat tregojnë probleme të mundshme me sistemin elektrik të vozilës.',
                'severity' => 'medium',
                'confidence_score' => 70,
                'likely_causes' => [
                    [
                        'title' => 'Probleme me Baterinë',
                        'description' => 'Bateria e dobët ose terminalet e ndotur mund të shkaktojnë probleme elektrike.',
                        'probability' => 60
                    ],
                    [
                        'title' => 'Probleme me Alternatorin',
                        'description' => 'Alternatori me defekt mund të mos ngarkojë baterinë siç duhet.',
                        'probability' => 50
                    ]
                ],
                'recommended_actions' => [
                    [
                        'title' => 'Kontroll i Sistemit Elektrik',
                        'description' => 'Kontaktoni një elektricist automobilistik për kontroll të sistemit elektrik.',
                        'urgency' => 'Within 1 week'
                    ]
                ],
                'estimated_costs' => $this->convertCosts([
                    ['service' => 'Electrical Diagnostic', 'min' => 100, 'max' => 200],
                    ['service' => 'Battery Check', 'min' => 50, 'max' => 100],
                    ['service' => 'Alternator Service', 'min' => 150, 'max' => 300]
                ], $data['user_currency_id'] ?? null),
                'requires_immediate_attention' => false,
                'model_version' => '1.0',
                'ai_provider' => 'fallback',
                'ai_insights' => [
                    'Problemet elektrike mund të ndikojnë në funksionimin e sistemeve të tjera.',
                    'Kontrolli i rregullt i baterisë është i rëndësishëm.'
                ]
            ];
        }
        
        // English version
        return [
            'problem_title' => 'Electrical Issues',
            'problem_description' => 'Symptoms indicate potential electrical system problems.',
            'severity' => 'medium',
            'confidence_score' => 70,
            'likely_causes' => [
                [
                    'title' => 'Battery Problems',
                    'description' => 'Weak battery or dirty terminals may cause electrical issues.',
                    'probability' => 60
                ],
                [
                    'title' => 'Alternator Issues',
                    'description' => 'Defective alternator may not charge the battery properly.',
                    'probability' => 50
                ]
            ],
            'recommended_actions' => [
                [
                    'title' => 'Electrical System Check',
                    'description' => 'Contact an automotive electrician for electrical system inspection.',
                    'urgency' => 'Within 1 week'
                ]
            ],
            'estimated_costs' => $this->convertCosts([
                ['service' => 'Electrical Diagnostic', 'min' => 100, 'max' => 200],
                ['service' => 'Battery Check', 'min' => 50, 'max' => 100],
                ['service' => 'Alternator Service', 'min' => 150, 'max' => 300]
            ], $data['user_currency_id'] ?? null),
            'requires_immediate_attention' => false,
            'model_version' => '1.0',
            'ai_provider' => 'fallback',
            'ai_insights' => [
                'Electrical problems can affect other system operations.',
                'Regular battery checks are important.'
            ]
        ];
    }

    /**
     * Convert costs to user's preferred currency
     */
    private function convertCosts(array $costs, $userCurrencyId = null): array
    {
        if (!$userCurrencyId) {
            return $costs;
        }

        $userCurrency = Currency::find($userCurrencyId);
        if (!$userCurrency) {
            return $costs;
        }

        $usdCurrency = Currency::where('code', 'USD')->first();
        if (!$usdCurrency) {
            return $costs;
        }

        $convertedCosts = [];
        foreach ($costs as $cost) {
            $convertedMin = $usdCurrency->convertTo($userCurrency, $cost['min']);
            $convertedMax = $usdCurrency->convertTo($userCurrency, $cost['max']);
            
            $convertedCosts[] = [
                'service' => $cost['service'],
                'min' => round($convertedMin, 2),
                'max' => round($convertedMax, 2),
                'currency' => $userCurrency->code,
                'formatted_min' => $userCurrency->format($convertedMin),
                'formatted_max' => $userCurrency->format($convertedMax)
            ];
        }

        return $convertedCosts;
    }

    /**
     * Translate AI response to user's language
     */
    private function translateResponse(array $response, string $language): array
    {
        if ($language === 'en') {
            return $response;
        }

        $translations = $this->getAITranslations($language);
        
        // Translate main fields
        if (isset($translations[$response['problem_title']])) {
            $response['problem_title'] = $translations[$response['problem_title']];
        }
        
        if (isset($translations[$response['problem_description']])) {
            $response['problem_description'] = $translations[$response['problem_description']];
        }

        // Note: We don't translate severity here as it needs to be stored in database
        // Severity translation will be handled in the frontend

        // Translate likely causes
        if (isset($response['likely_causes'])) {
            foreach ($response['likely_causes'] as &$cause) {
                if (isset($translations[$cause['title']])) {
                    $cause['title'] = $translations[$cause['title']];
                }
                if (isset($translations[$cause['description']])) {
                    $cause['description'] = $translations[$cause['description']];
                }
            }
        }

        // Translate recommended actions
        if (isset($response['recommended_actions'])) {
            foreach ($response['recommended_actions'] as &$action) {
                if (isset($translations[$action['title']])) {
                    $action['title'] = $translations[$action['title']];
                }
                if (isset($translations[$action['description']])) {
                    $action['description'] = $translations[$action['description']];
                }
                if (isset($translations[$action['urgency']])) {
                    $action['urgency'] = $translations[$action['urgency']];
                }
            }
        }

        // Translate AI insights
        if (isset($response['ai_insights'])) {
            foreach ($response['ai_insights'] as &$insight) {
                if (isset($translations[$insight])) {
                    $insight = $translations[$insight];
                }
            }
        }

        // Translate estimated costs
        if (isset($response['estimated_costs'])) {
            foreach ($response['estimated_costs'] as &$cost) {
                if (isset($translations[$cost['service']])) {
                    $cost['service'] = $translations[$cost['service']];
                }
            }
        }

        return $response;
    }

    /**
     * Get AI response translations for different languages
     */
    private function getAITranslations(string $language): array
    {
        $translations = [
            'sq' => [
                // Problem titles
                'Engine Starting Issues' => 'Probleme me Fillimin e Motorit',
                'Engine Performance Issues' => 'Probleme me Performancën e Motorit',
                'Warning Light Issues' => 'Probleme me Dritat e Paralajmërimit',
                'Fuel Economy Issues' => 'Probleme me Konsumin e Karburantit',
                'Overheating Issues' => 'Probleme me Shtimin e Temperaturës',
                'General Maintenance' => 'Mirëmbajtje e Përgjithshme',
                'Vehicle Diagnostic Required' => 'Kërkohet Diagnostikimi i Vozilës',
                
                // Problem descriptions
                'The vehicle is experiencing difficulty starting, which could be related to the ignition system, fuel delivery, or battery.' => 'Vozila po përjeton vështirësi në fillim, e cila mund të jetë e lidhur me sistemin e ndezjes, furnizimin me karburant, ose baterinë.',
                'The vehicle is showing signs of poor engine performance, including rough idle, poor acceleration, or strange noises.' => 'Vozila po tregon shenja të performancës së dobët të motorit, duke përfshirë funksionimin e ashpër, përshpejtim të dobët, ose zhurma të çuditshme.',
                'Warning lights are illuminated on the dashboard, indicating potential system issues that require attention.' => 'Dritat e paralajmërimit janë të ndezura në panel, duke treguar probleme të mundshme të sistemit që kërkojnë vëmendje.',
                'The vehicle is consuming more fuel than expected, which could indicate various engine or driving issues.' => 'Vozila po konsumon më shumë karburant sesa pritej, e cila mund të tregojë probleme të ndryshme të motorit ose të vozitjes.',
                'The engine is overheating, which is a serious issue that requires immediate attention to prevent damage.' => 'Motori po nxehet shumë, e cila është një problem serioz që kërkon vëmendje të menjëhershme për të parandaluar dëmtimin.',
                'Based on the symptoms described, this appears to be a general maintenance issue that can be addressed with routine service.' => 'Bazuar në simptomat e përshkruara, kjo duket të jetë një problem i mirëmbajtjes së përgjithshme që mund të adresohet me shërbim rutinor.',
                'Based on the symptoms described, a comprehensive diagnostic is recommended to identify the root cause.' => 'Bazuar në simptomat e përshkruara, rekomandohet një diagnostikim i plotë për të identifikuar shkakun rrënjësor.',
                
                // Likely causes
                'Battery Issues' => 'Probleme me Baterinë',
                'Starter Motor Problems' => 'Probleme me Motorin e Fillimit',
                'Fuel System Issues' => 'Probleme me Sistemin e Karburantit',
                'Engine Performance Problems' => 'Probleme me Performancën e Motorit',
                'Exhaust System Issues' => 'Probleme me Sistemin e Gazrave',
                'Warning Light System' => 'Sistemi i Dritave të Paralajmërimit',
                'Fuel Economy Problems' => 'Probleme me Konsumin e Karburantit',
                'Cooling System Issues' => 'Probleme me Sistemin e Ftohjes',
                'General Maintenance Issues' => 'Probleme të Mirëmbajtjes së Përgjithshme',
                'Multiple System Analysis' => 'Analiza e Sistemeve të Shumta',
                'Wear and Tear' => 'Konsumim dhe Veshje',
                
                // Cause descriptions
                'Weak or dead battery preventing engine from starting.' => 'Bateri e dobët ose e shkarkuar që parandalon fillimin e motorit.',
                'Faulty starter motor not engaging properly.' => 'Motor i gabuar i fillimit që nuk funksionon siç duhet.',
                'Fuel pump or fuel filter problems preventing fuel delivery.' => 'Probleme me pompën e karburantit ose filtrin e karburantit që parandalojnë furnizimin me karburant.',
                'Engine components not functioning optimally, causing performance issues.' => 'Komponentët e motorit nuk funksionojnë optimalisht, duke shkaktuar probleme performancë.',
                'Exhaust system leaks or damage affecting engine performance.' => 'Rrjedhje ose dëmtim i sistemit të gazrave që ndikon në performancën e motorit.',
                'Sensor or system malfunction triggering warning lights.' => 'Gabim i sensorit ose i sistemit që shkakton dritat e paralajmërimit.',
                'Inefficient fuel consumption due to various engine or driving factors.' => 'Konsum i paefektshëm i karburantit për shkak të faktorëve të ndryshëm të motorit ose të vozitjes.',
                'Cooling system failure causing engine overheating.' => 'Dështim i sistemit të ftohjes që shkakton nxehtësinë e motorit.',
                'Routine maintenance items that need attention.' => 'Artikuj të mirëmbajtjes rutinore që kanë nevojë për vëmendje.',
                'Symptoms may be related to engine, transmission, electrical, or other systems.' => 'Simptomat mund të jenë të lidhura me motorin, transmetimin, sistemin elektrik, ose sisteme të tjera.',
                'Normal aging components may require maintenance or replacement.' => 'Komponentët e moshës normale mund të kërkojnë mirëmbajtje ose zëvendësim.',
                
                // Recommended actions
                'Check Battery Voltage' => 'Kontrollo Tensionin e Baterisë',
                'Inspect Starter Motor' => 'Inspekto Motorin e Fillimit',
                'Fuel System Diagnostic' => 'Diagnostikimi i Sistemit të Karburantit',
                'Engine Performance Check' => 'Kontrolli i Performancës së Motorit',
                'Exhaust System Inspection' => 'Inspektimi i Sistemit të Gazrave',
                'Warning Light Diagnostic' => 'Diagnostikimi i Dritave të Paralajmërimit',
                'Fuel Economy Analysis' => 'Analiza e Konsumit të Karburantit',
                'Cooling System Check' => 'Kontrolli i Sistemit të Ftohjes',
                'Preventive Maintenance' => 'Mirëmbajtje Parandaluese',
                'Comprehensive Diagnostic' => 'Diagnostikim i Plotë',
                
                // Action descriptions
                'Test battery voltage and connections.' => 'Testo tensionin e baterisë dhe lidhjet.',
                'Have starter motor tested by a professional.' => 'Bëj testin e motorit të fillimit nga një profesionist.',
                'Check fuel pump and filter condition.' => 'Kontrollo gjendjen e pompës dhe filtrit të karburantit.',
                'Have engine performance analyzed by a mechanic.' => 'Bëj analizën e performancës së motorit nga një mekanik.',
                'Inspect exhaust system for leaks or damage.' => 'Inspekto sistemin e gazrave për rrjedhje ose dëmtim.',
                'Use diagnostic tools to identify warning light causes.' => 'Përdor mjetet diagnostikuese për të identifikuar shkaqet e dritave të paralajmërimit.',
                'Analyze fuel consumption patterns and driving habits.' => 'Analizo modelet e konsumit të karburantit dhe zakonet e vozitjes.',
                'Check cooling system components and fluid levels.' => 'Kontrollo komponentët e sistemit të ftohjes dhe nivelet e lëngut.',
                'Consider routine maintenance to prevent future issues.' => 'Konsidero mirëmbajtjen rutinore për të parandaluar problemet e ardhshme.',
                'Schedule a full vehicle inspection with diagnostic equipment.' => 'Programo një inspektim të plotë të vozilës me pajisje diagnostikuese.',
                
                // Urgency levels
                'Immediate' => 'I Menjëhershëm',
                'Within 1 week' => 'Brenda 1 javës',
                'Within 2 weeks' => 'Brenda 2 javëve',
                'Within 1 month' => 'Brenda 1 muajit',
                
                // AI insights
                'Based on vehicle age and mileage, consider preventive maintenance.' => 'Bazuar në moshën dhe kilometrazhin e vozilës, konsidero mirëmbajtjen parandaluese.',
                'Regular diagnostic checks can prevent costly repairs.' => 'Kontrollet diagnostikuese të rregullta mund të parandalojnë riparime të shtrenjta.',
                'Document symptoms for better diagnostic accuracy.' => 'Dokumento simptomat për saktësi më të mirë diagnostikuese.',
                
                // Additional translations for mixed language issue
                'Vehicle Diagnostic Required' => 'Kërkohet Diagnostikimi i Vozilës',
                'Based on the symptoms described, a comprehensive diagnostic is recommended to identify the root cause.' => 'Bazuar në simptomat e përshkruara, rekomandohet një diagnostikim i plotë për të identifikuar shkakun rrënjësor.',
                'Multiple System Analysis' => 'Analiza e Sistemeve të Shumta',
                'Symptoms may be related to engine, transmission, electrical, or other systems.' => 'Simptomat mund të jenë të lidhura me motorin, transmetimin, sistemin elektrik, ose sisteme të tjera.',
                'Wear and Tear' => 'Konsumim dhe Veshje',
                'Normal aging components may require maintenance or replacement.' => 'Komponentët e moshës normale mund të kërkojnë mirëmbajtje ose zëvendësim.',
                'Comprehensive Diagnostic' => 'Diagnostikim i Plotë',
                'Schedule a full vehicle inspection with diagnostic equipment.' => 'Programo një inspektim të plotë të vozilës me pajisje diagnostikuese.',
                'Preventive Maintenance' => 'Mirëmbajtje Parandaluese',
                'Consider routine maintenance to prevent future issues.' => 'Konsidero mirëmbajtjen rutinore për të parandaluar problemet e ardhshme.',
                'Within 1 week' => 'Brenda 1 javës',
                'Within 2 weeks' => 'Brenda 2 javëve',
                
                // Priority levels
                'MEDIUM Priority' => 'Prioritet i Mesëm',
                'HIGH Priority' => 'Prioritet i Lartë',
                'LOW Priority' => 'Prioritet i Ulët',
                'CRITICAL Priority' => 'Prioritet Kritik',
                
                // Confidence levels
                '65% Confidence' => '65% Besim',
                '75% Confidence' => '75% Besim',
                '85% Confidence' => '85% Besim',
                '90% Confidence' => '90% Besim',
                
                // Cost estimates
                'Diagnostic Fee' => 'Tarifa Diagnostikuese',
                'Maintenance Service' => 'Shërbimi i Mirëmbajtjes',
                'Estimated Costs' => 'Kostot e Vlerësuara',
                'Cost estimates are approximate and may vary based on location and specific vehicle requirements.' => 'Vlerësimet e kostove janë të përafërta dhe mund të ndryshojnë bazuar në vendndodhjen dhe kërkesat specifike të vozilës.',
                
                // Additional common phrases
                'probability' => 'probabilitet',
                'Shkaqet e Mundshme' => 'Shkaqet e Mundshme',
                'Veprimet e Rekomanduara' => 'Veprimet e Rekomanduara',
                
                // More specific translations
                'Dashboard Warning Lights' => 'Dritat e Paralajmërimit të Panelit',
                'Warning lights on the dashboard indicate a system malfunction that requires immediate attention.' => 'Dritat e paralajmërimit në panel tregojnë një mosfunksionim të sistemit që kërkon vëmendje të menjëhershme.',
                'Engine Management System' => 'Sistemi i Menaxhimit të Motorit',
                'Check Engine Light (CEL) indicates engine or emissions system issues.' => 'Drita e Kontrollit të Motorit (CEL) tregon probleme me motorin ose sistemin e emetimeve.',
                'ABS System' => 'Sistemi ABS',
                'Anti-lock Braking System warning may indicate brake system problems.' => 'Paralajmërimi i Sistemit të Frenave Anti-Bllokimi mund të tregojë probleme me sistemin e frenave.',
                'Airbag System' => 'Sistemi i Airbag-ëve',
                'Airbag warning light indicates safety system malfunction.' => 'Drita e paralajmërimit të airbag-ëve tregon mosfunksionim të sistemit të sigurisë.',
                'Immediate Diagnostic Scan' => 'Skanim Diagnostik i Menjëhershëm',
                'Use OBD-II scanner to read diagnostic trouble codes.' => 'Përdor skanerin OBD-II për të lexuar kodet e problemeve diagnostikuese.',
                'Professional Inspection' => 'Inspektim Profesional',
                'Have certified mechanic inspect the affected systems.' => 'Lër mekanikun e certifikuar të inspektojë sistemet e prekura.',
                'Within 24 hours' => 'Brenda 24 orëve',
                'Diagnostic Scan' => 'Skanim Diagnostik',
                'System Repair' => 'Riparimi i Sistemit',
            ],
            'de' => [
                // German translations would go here
                'Engine Starting Issues' => 'Motorstartprobleme',
                'Engine Performance Issues' => 'Motorleistungsprobleme',
                'Warning Light Issues' => 'Warnleuchtenprobleme',
                'Fuel Economy Issues' => 'Kraftstoffverbrauchsprobleme',
                'Overheating Issues' => 'Überhitzungsprobleme',
                'General Maintenance' => 'Allgemeine Wartung',
            ],
            'fr' => [
                // French translations would go here
                'Engine Starting Issues' => 'Problèmes de Démarrage du Moteur',
                'Engine Performance Issues' => 'Problèmes de Performance du Moteur',
                'Warning Light Issues' => 'Problèmes de Voyants d\'Avertissement',
                'Fuel Economy Issues' => 'Problèmes de Consommation de Carburant',
                'Overheating Issues' => 'Problèmes de Surchauffe',
                'General Maintenance' => 'Maintenance Générale',
            ]
        ];

        return $translations[$language] ?? [];
    }
}
