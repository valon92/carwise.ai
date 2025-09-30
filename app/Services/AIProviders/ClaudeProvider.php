<?php

namespace App\Services\AIProviders;

use App\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ClaudeProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = Config::get('services.claude.api_key') ?? '';
        $this->baseUrl = Config::get('services.claude.base_url') ?? 'https://api.anthropic.com/v1';
        $this->model = Config::get('services.claude.model') ?? 'claude-3-5-sonnet-20241022';

        if (empty($this->apiKey)) {
            Log::warning('Claude API key is not configured');
        }
    }

    public function isAvailable(): bool
    {
        if (empty($this->apiKey)) {
            return false;
        }

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'anthropic-version' => '2023-06-01',
                ])
                ->post($this->baseUrl . '/messages', [
                    'model' => $this->model,
                    'max_tokens' => 10,
                    'messages' => [
                        ['role' => 'user', 'content' => 'test']
                    ]
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Claude provider availability check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function getProviderInfo(): array
    {
        return [
            'name' => 'Anthropic Claude',
            'provider' => 'claude',
            'model' => $this->model,
            'available' => $this->isAvailable(),
            'features' => ['text_generation', 'reasoning', 'multilingual', 'analysis'],
            'limits' => [
                'max_tokens' => 200000,
                'rate_limit' => '50 requests/minute',
                'context_window' => '200K tokens'
            ]
        ];
    }

    public function analyzeDiagnosis(array $data): array
    {
        try {
            Log::info('Starting Claude diagnosis analysis');
            $prompt = $this->buildDiagnosisPrompt($data);
            $response = $this->makeRequest($prompt);
            $analysis = $this->parseResponse($response, $data['user_language'] ?? 'en');
            Log::info('Claude diagnosis completed');
            return $analysis;
        } catch (\Exception $e) {
            Log::error('Claude diagnosis failed', ['error' => $e->getMessage()]);
            throw new \Exception('Claude analysis failed: ' . $e->getMessage());
        }
    }

    private function buildDiagnosisPrompt(array $data): string
    {
        $language = $data['user_language'] ?? 'en';
        $make = $data['make'] ?? $data['car_brand'] ?? 'Unknown';
        $model = $data['model'] ?? $data['car_model'] ?? 'Unknown';
        $year = $data['year'] ?? $data['car_year'] ?? 'Unknown';
        $mileage = $data['mileage'] ?? 'Unknown';
        $engineType = $data['engine_type'] ?? 'Unknown';
        $description = $data['description'] ?? $data['problem_description'] ?? '';
        
        $symptoms = $data['symptoms'] ?? [];
        if (is_string($symptoms)) {
            $symptoms = json_decode($symptoms, true) ?? [$symptoms];
        }
        $symptomsList = implode(', ', $symptoms);

        $languageNames = [
            'sq' => 'Albanian (Shqip)', 'en' => 'English', 'de' => 'German (Deutsch)',
            'fr' => 'French (Français)', 'pt' => 'Portuguese (Português)', 
            'es' => 'Spanish (Español)', 'it' => 'Italian (Italiano)'
        ];
        $languageName = $languageNames[$language] ?? 'English';

        return "You are an expert automotive diagnostic assistant for CarWise.ai. Analyze the following vehicle issue and provide a comprehensive response STRICTLY in {$languageName}.

CRITICAL: Your entire response must be in {$languageName}. Do not use any other language.

VEHICLE DETAILS:
- Make: {$make}
- Model: {$model}
- Year: {$year}
- Mileage: {$mileage} km
- Engine: {$engineType}

REPORTED PROBLEM:
- Symptoms: {$symptomsList}
- Description: {$description}

Please provide a structured diagnostic analysis in {$languageName} including:

1. **DIAGNOSIS**: Detailed technical analysis of the problem
2. **PROBABLE CAUSES**: Most likely causes ranked by probability
3. **REPAIR RECOMMENDATIONS**: Step-by-step recommended actions
4. **URGENCY ASSESSMENT**: Rate as Critical/High/Medium/Low with reasoning
5. **COST ESTIMATE**: Expected repair cost range
6. **MAINTENANCE ADVICE**: How to prevent similar issues

Be specific about this {$make} {$model} {$year} vehicle. Consider common issues for this make/model/year combination. Provide practical, actionable advice that a car owner can understand and act upon.

Remember: Respond ONLY in {$languageName}.";
    }

    private function makeRequest(string $prompt, array $parameters = []): array
    {
        $payload = [
                'model' => $this->model,
            'max_tokens' => $parameters['max_tokens'] ?? 3000,
            'temperature' => $parameters['temperature'] ?? 0.3,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
        ];

        $response = Http::timeout(90)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json'
            ])
            ->post($this->baseUrl . '/messages', $payload);

            if (!$response->successful()) {
            $error = $response->json()['error'] ?? ['message' => 'Unknown error'];
            throw new \Exception('Claude API error: ' . ($error['message'] ?? 'Request failed'));
            }

            $responseData = $response->json();
        if (!isset($responseData['content'][0]['text'])) {
            throw new \Exception('Invalid response format from Claude API');
        }

        return $responseData;
    }

    private function parseResponse(array $response, string $language): array
    {
        $content = $response['content'][0]['text'];
        
        // Parse structured sections
        $diagnosis = $content;
        $recommendations = '';
        $urgency = 'medium';
        $estimatedCost = '';

        // Extract diagnosis section
        if (preg_match('/(?:\*\*)?(?:DIAGNOSIS|DIJAGNOZA|DIAGNOSE|DIAGNOSTIC)(?:\*\*)?[:：]\s*(.*?)(?=(?:\*\*)?(?:PROBABLE|MÖGLICH|MOGELIJK|POSSIB|SHKAQ|CAUSE))/si', $content, $matches)) {
            $diagnosis = trim($matches[1]);
        }

        // Extract recommendations
        if (preg_match('/(?:\*\*)?(?:REPAIR RECOMMENDATIONS|RECOMMENDED|REPARATUR|RÉPARATION|REPARACIÓN|RIPARAZIONE|REKOMANDIMET)(?:\*\*)?[:：]\s*(.*?)(?=(?:\*\*)?(?:URGENCY|DRINGLICHKEIT|URGENCE|URGENCIA|SHKALLA))/si', $content, $matches)) {
            $recommendations = trim($matches[1]);
        }

        // Extract urgency
        if (preg_match('/(?:\*\*)?(?:URGENCY|DRINGLICHKEIT|URGENCE|URGENCIA|SHKALLA)(?:\*\*)?.*?[:：]\s*(?:Rate as |Level: )?(Critical|High|Medium|Low|Kritisch|Hoch|Mittel|Niedrig|Critique|Élevé|Moyen|Faible|Crítico|Alto|Medio|Bajo|Kritike|I lartë|Mesatar|I ulët)/i', $content, $matches)) {
            $urgencyText = strtolower(trim($matches[1]));
            $urgencyMap = [
                'critical' => 'critical', 'kritisch' => 'critical', 'critique' => 'critical', 'crítico' => 'critical', 'kritike' => 'critical',
                'high' => 'high', 'hoch' => 'high', 'élevé' => 'high', 'alto' => 'high', 'i lartë' => 'high',
                'medium' => 'medium', 'mittel' => 'medium', 'moyen' => 'medium', 'medio' => 'medium', 'mesatar' => 'medium',
                'low' => 'low', 'niedrig' => 'low', 'faible' => 'low', 'bajo' => 'low', 'i ulët' => 'low'
            ];
            $urgency = $urgencyMap[$urgencyText] ?? 'medium';
        }

        // Extract cost estimate
        if (preg_match('/(?:\*\*)?(?:COST ESTIMATE|KOSTEN|COÛT|COSTO|CUSTO|KOSTO)(?:\*\*)?[:：]\s*(.*?)(?=(?:\*\*)?(?:MAINTENANCE|WARTUNG|ENTRETIEN|MANTENIMIENTO|MANUTENÇÃO|MIRËMBAJTJE))/si', $content, $matches)) {
            $estimatedCost = trim($matches[1]);
        }

        // Fallbacks
        if (empty($recommendations)) {
            $recommendations = $this->generateFallbackRecommendations($language);
        }
        if (empty($estimatedCost)) {
            $estimatedCost = $this->generateFallbackCost($language);
        }

        return [
            'diagnosis' => $diagnosis,
            'recommendations' => $recommendations,
            'urgency' => $urgency,
            'estimated_cost' => $estimatedCost,
            'ai_insights' => $this->generateAIInsights($language),
            'confidence_score' => $this->calculateConfidenceScore($response),
            'provider' => 'claude',
            'model' => $this->model
        ];
    }

    private function generateFallbackRecommendations(string $language): string
    {
        $recommendations = [
            'en' => "1. Schedule inspection with certified mechanic\n2. Document all symptoms and their frequency\n3. Check vehicle maintenance history\n4. Verify warranty coverage\n5. Get second opinion for major repairs",
            'sq' => "1. Planifikoni inspektim me mekanik të certifikuar\n2. Dokumentoni të gjitha simptomat dhe frekuencën\n3. Kontrolloni historikun e mirëmbajtjes\n4. Verifikoni mbulimin e garancisë\n5. Merrni mendim të dytë për riparime të mëdha",
            'de' => "1. Inspektion bei zertifiziertem Mechaniker planen\n2. Alle Symptome und deren Häufigkeit dokumentieren\n3. Wartungshistorie des Fahrzeugs prüfen\n4. Garantieabdeckung überprüfen\n5. Zweite Meinung für größere Reparaturen einholen",
            'fr' => "1. Planifier inspection avec mécanicien certifié\n2. Documenter tous les symptômes et leur fréquence\n3. Vérifier l'historique d'entretien du véhicule\n4. Vérifier la couverture de garantie\n5. Obtenir un deuxième avis pour les réparations importantes",
            'pt' => "1. Agendar inspeção com mecânico certificado\n2. Documentar todos os sintomas e sua frequência\n3. Verificar histórico de manutenção do veículo\n4. Verificar cobertura da garantia\n5. Obter segunda opinião para reparos importantes",
            'es' => "1. Programar inspección con mecánico certificado\n2. Documentar todos los síntomas y su frecuencia\n3. Verificar historial de mantenimiento del vehículo\n4. Verificar cobertura de garantía\n5. Obtener segunda opinión para reparaciones importantes"
        ];
        return $recommendations[$language] ?? $recommendations['en'];
    }

    private function generateFallbackCost(string $language): string
    {
        $costs = [
            'en' => "Cost estimate depends on specific diagnosis and required parts. Range: €50-€800+ depending on complexity. Consult local mechanic for accurate pricing.",
            'sq' => "Vlerësimi i kostos varet nga diagnoza specifike dhe pjesët e kërkuara. Diapazoni: €50-€800+ në varësi të kompleksitetit. Konsultohuni me mekanik lokal për çmim të saktë.",
            'de' => "Kostenschätzung hängt von spezifischer Diagnose und benötigten Teilen ab. Bereich: €50-€800+ je nach Komplexität. Lokalen Mechaniker für genaue Preise konsultieren.",
            'fr' => "L'estimation des coûts dépend du diagnostic spécifique et des pièces requises. Fourchette: €50-€800+ selon la complexité. Consulter un mécanicien local pour un prix précis.",
            'pt' => "A estimativa de custo depende do diagnóstico específico e peças necessárias. Faixa: €50-€800+ dependendo da complexidade. Consulte mecânico local para preços precisos.",
            'es' => "La estimación de costos depende del diagnóstico específico y las piezas requeridas. Rango: €50-€800+ según la complejidad. Consulte mecánico local para precios precisos."
        ];
        return $costs[$language] ?? $costs['en'];
    }

    private function generateAIInsights(string $language): string
    {
        $insights = [
            'en' => "Claude AI Analysis: This assessment uses advanced reasoning to analyze symptoms in context of vehicle specifications and common failure patterns.",
            'sq' => "Analiza Claude AI: Ky vlerësim përdor arsyetim të avancuar për të analizuar simptomat në kontekstin e specifikimeve të automjetit dhe modeleve të zakonshme të defekteve.",
            'de' => "Claude KI-Analyse: Diese Bewertung nutzt fortgeschrittenes Reasoning zur Analyse der Symptome im Kontext der Fahrzeugspezifikationen und häufiger Ausfallmuster.",
            'fr' => "Analyse Claude IA : Cette évaluation utilise un raisonnement avancé pour analyser les symptômes dans le contexte des spécifications du véhicule et des schémas de défaillance courants.",
            'pt' => "Análise Claude AI: Esta avaliação usa raciocínio avançado para analisar sintomas no contexto das especificações do veículo e padrões comuns de falha.",
            'es' => "Análisis Claude AI: Esta evaluación utiliza razonamiento avanzado para analizar síntomas en el contexto de las especificaciones del vehículo y patrones comunes de falla."
        ];
        return $insights[$language] ?? $insights['en'];
    }

    private function calculateConfidenceScore(array $response): float
    {
        $confidence = 0.9; // Claude generally provides high-quality responses

        // Check for stop reason
        if (isset($response['stop_reason'])) {
            if ($response['stop_reason'] === 'end_turn') {
                $confidence += 0.05;
            } elseif ($response['stop_reason'] === 'max_tokens') {
                $confidence -= 0.1;
            }
        }

        // Check content length
        $contentLength = strlen($response['content'][0]['text']);
        if ($contentLength > 1500) {
            $confidence += 0.03;
        } elseif ($contentLength < 300) {
            $confidence -= 0.15;
        }

        // Check usage stats if available
        if (isset($response['usage'])) {
            $inputTokens = $response['usage']['input_tokens'] ?? 0;
            $outputTokens = $response['usage']['output_tokens'] ?? 0;
            
            // Reasonable token usage indicates good response
            if ($outputTokens > 200 && $outputTokens < 2500) {
                $confidence += 0.02;
            }
        }

        return max(0.0, min(1.0, $confidence));
    }

    public function getProviderName(): string
    {
        return 'Anthropic Claude';
    }

    public function getEstimatedCost(array $data): float
    {
        // Claude pricing estimation
        $inputText = ($data['description'] ?? '') . ' ' . implode(' ', $data['symptoms'] ?? []);
        $estimatedTokens = strlen($inputText) / 4; // Rough estimation
        $costPer1kTokens = 0.024; // Claude pricing
        return ($estimatedTokens / 1000) * $costPer1kTokens;
    }
}