<?php

namespace App\Services\AIProviders;

use App\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class CohereProvider implements AIProviderInterface
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = Config::get('services.cohere.api_key') ?? '';
        $this->baseUrl = Config::get('services.cohere.base_url') ?? 'https://api.cohere.ai/v1';
        $this->model = Config::get('services.cohere.model') ?? 'command-r-plus';

        if (empty($this->apiKey)) {
            Log::warning('Cohere API key is not configured');
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
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/generate', [
                    'model' => $this->model,
                    'prompt' => 'test',
                    'max_tokens' => 5
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Cohere provider availability check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function getProviderInfo(): array
    {
        return [
            'name' => 'Cohere',
            'provider' => 'cohere',
            'model' => $this->model,
            'available' => $this->isAvailable(),
            'features' => ['text_generation', 'multilingual', 'retrieval_augmented'],
            'limits' => [
                'max_tokens' => 4096,
                'rate_limit' => '100 requests/minute',
                'context_window' => '128K tokens'
            ]
        ];
    }

    public function analyzeDiagnosis(array $data): array
    {
        try {
            Log::info('Starting Cohere diagnosis analysis');
            $prompt = $this->buildDiagnosisPrompt($data);
            $response = $this->makeRequest($prompt);
            $analysis = $this->parseResponse($response, $data['user_language'] ?? 'en');
            Log::info('Cohere diagnosis completed');
            return $analysis;
        } catch (\Exception $e) {
            Log::error('Cohere diagnosis failed', ['error' => $e->getMessage()]);
            throw new \Exception('Cohere analysis failed: ' . $e->getMessage());
        }
    }

    private function buildDiagnosisPrompt(array $data): string
    {
        $language = $data['user_language'] ?? 'en';
        $make = $data['make'] ?? $data['car_brand'] ?? 'Unknown';
        $model = $data['model'] ?? $data['car_model'] ?? 'Unknown';
        $year = $data['year'] ?? $data['car_year'] ?? 'Unknown';
        $mileage = $data['mileage'] ?? 'Unknown';
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

        return "You are an expert automotive diagnostic AI for CarWise.ai. Analyze the vehicle problem below and provide your response EXCLUSIVELY in {$languageName}.

VEHICLE INFORMATION:
- Make: {$make}
- Model: {$model}
- Year: {$year}
- Mileage: {$mileage} km

PROBLEM DETAILS:
- Symptoms: {$symptomsList}
- Description: {$description}

INSTRUCTIONS:
Respond ONLY in {$languageName} with a comprehensive diagnostic analysis including:

1. PROBLEM DIAGNOSIS: What is likely wrong with the vehicle
2. RECOMMENDED SOLUTIONS: Specific repair steps
3. URGENCY LEVEL: How quickly this needs attention (Critical/High/Medium/Low)
4. ESTIMATED REPAIR COST: Price range for fixing the issue
5. PREVENTION TIPS: How to avoid this problem in the future

Be specific about this {$make} {$model} {$year} and consider common issues for this vehicle. Provide practical advice that the car owner can understand and act upon.

Important: Your entire response must be in {$languageName} language.";
    }

    private function makeRequest(string $prompt, array $parameters = []): array
    {
        $payload = array_merge([
            'model' => $this->model,
            'prompt' => $prompt,
            'max_tokens' => 2500,
            'temperature' => 0.3,
            'k' => 0,
            'stop_sequences' => [],
            'return_likelihoods' => 'NONE'
        ], $parameters);

        $response = Http::timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($this->baseUrl . '/generate', $payload);

        if (!$response->successful()) {
            $error = $response->json()['message'] ?? 'Unknown error';
            throw new \Exception('Cohere API error: ' . $error);
        }

        $responseData = $response->json();
        if (!isset($responseData['generations'][0]['text'])) {
            throw new \Exception('Invalid response format from Cohere API');
        }

        return $responseData;
    }

    private function parseResponse(array $response, string $language): array
    {
        $content = $response['generations'][0]['text'];
        
        // Parse structured response
        $diagnosis = $content;
        $recommendations = '';
        $urgency = 'medium';
        $estimatedCost = '';

        // Extract diagnosis section
        if (preg_match('/(?:PROBLEM DIAGNOSIS|DIJAGNOZA|PROBLEMDIAGNOSE|DIAGNOSTIC|DIAGNÓSTICO|DIAGNOSI)[:：]\s*(.*?)(?=(?:RECOMMENDED|REKOMAN|EMPFOHLEN|RECOMMANDÉ|RECOMENDADO|RACCOMANDATO))/si', $content, $matches)) {
            $diagnosis = trim($matches[1]);
        }

        // Extract recommendations
        if (preg_match('/(?:RECOMMENDED SOLUTIONS|SOLUTION|LÖSUNG|SOLUÇÃO|SOLUCIÓN|SOLUZIONE|ZGJIDHJE)[:：]\s*(.*?)(?=(?:URGENCY|DRINGLICHKEIT|URGENCE|URGENCIA|SHKALLA))/si', $content, $matches)) {
            $recommendations = trim($matches[1]);
        }

        // Extract urgency level
        if (preg_match('/(?:URGENCY LEVEL|SHKALLA E URGJENCËS|DRINGLICHKEITSSTUFE|NIVEAU D\'URGENCE|NIVEL DE URGENCIA|LIVELLO DI URGENZA)[:：]\s*(Critical|High|Medium|Low|Kritisch|Hoch|Mittel|Niedrig|Critique|Élevé|Moyen|Faible|Crítico|Alto|Medio|Bajo|Critico|Alto|Medio|Basso|Kritike|I lartë|Mesatar|I ulët)/i', $content, $matches)) {
            $urgencyText = strtolower(trim($matches[1]));
            $urgencyMap = [
                'critical' => 'critical', 'kritisch' => 'critical', 'critique' => 'critical', 
                'crítico' => 'critical', 'critico' => 'critical', 'kritike' => 'critical',
                'high' => 'high', 'hoch' => 'high', 'élevé' => 'high', 'alto' => 'high', 
                'i lartë' => 'high',
                'medium' => 'medium', 'mittel' => 'medium', 'moyen' => 'medium', 
                'medio' => 'medium', 'mesatar' => 'medium',
                'low' => 'low', 'niedrig' => 'low', 'faible' => 'low', 'bajo' => 'low', 
                'basso' => 'low', 'i ulët' => 'low'
            ];
            $urgency = $urgencyMap[$urgencyText] ?? 'medium';
        }

        // Extract estimated cost
        if (preg_match('/(?:ESTIMATED REPAIR COST|KOSTEN|COÛT|COSTO|CUSTO|KOSTO)[:：]\s*(.*?)(?=(?:PREVENTION|PRÉVENTION|PREVENCIÓN|PREVENZIONE|PARANDALIM|VORBEUGUNG))/si', $content, $matches)) {
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
            'provider' => 'cohere',
            'model' => $this->model
        ];
    }

    private function generateFallbackRecommendations(string $language): string
    {
        $recommendations = [
            'en' => "1. Have vehicle inspected by qualified technician\n2. Document when symptoms occur\n3. Check maintenance records\n4. Compare with manufacturer recommendations\n5. Consider warranty implications",
            'sq' => "1. Inspektoni automjetin nga teknik i kualifikuar\n2. Dokumentoni kur ndodhin simptomat\n3. Kontrolloni dosjet e mirëmbajtjes\n4. Krahasoni me rekomandimet e prodhuesit\n5. Konsideroni implikimet e garancisë",
            'de' => "1. Fahrzeug von qualifiziertem Techniker inspizieren lassen\n2. Dokumentieren, wann Symptome auftreten\n3. Wartungsunterlagen prüfen\n4. Mit Herstellerempfehlungen vergleichen\n5. Garantieauswirkungen berücksichtigen",
            'fr' => "1. Faire inspecter le véhicule par un technicien qualifié\n2. Documenter quand les symptômes se produisent\n3. Vérifier les dossiers d'entretien\n4. Comparer avec les recommandations du fabricant\n5. Considérer les implications de garantie",
            'pt' => "1. Fazer o veículo ser inspecionado por técnico qualificado\n2. Documentar quando os sintomas ocorrem\n3. Verificar registros de manutenção\n4. Comparar com recomendações do fabricante\n5. Considerar implicações da garantia",
            'es' => "1. Hacer inspeccionar el vehículo por técnico calificado\n2. Documentar cuándo ocurren los síntomas\n3. Revisar registros de mantenimiento\n4. Comparar con recomendaciones del fabricante\n5. Considerar implicaciones de garantía"
        ];
        return $recommendations[$language] ?? $recommendations['en'];
    }

    private function generateFallbackCost(string $language): string
    {
        $costs = [
            'en' => "Repair costs vary significantly based on specific diagnosis and parts needed. Typical range: €75-€1200+. Get quotes from certified mechanics for accurate estimates.",
            'sq' => "Kostot e riparimit ndryshojnë ndjeshëm bazuar në diagnozën specifike dhe pjesët e nevojshme. Diapazoni tipik: €75-€1200+. Merrni oferta nga mekanikë të certifikuar për vlerësime të sakta.",
            'de' => "Reparaturkosten variieren erheblich je nach spezifischer Diagnose und benötigten Teilen. Typischer Bereich: €75-€1200+. Angebote von zertifizierten Mechanikern für genaue Schätzungen einholen.",
            'fr' => "Les coûts de réparation varient considérablement selon le diagnostic spécifique et les pièces nécessaires. Fourchette typique: €75-€1200+. Obtenez des devis de mécaniciens certifiés pour des estimations précises.",
            'pt' => "Os custos de reparo variam significativamente com base no diagnóstico específico e peças necessárias. Faixa típica: €75-€1200+. Obtenha orçamentos de mecânicos certificados para estimativas precisas.",
            'es' => "Los costos de reparación varían significativamente según el diagnóstico específico y las piezas necesarias. Rango típico: €75-€1200+. Obtenga cotizaciones de mecánicos certificados para estimaciones precisas."
        ];
        return $costs[$language] ?? $costs['en'];
    }

    private function generateAIInsights(string $language): string
    {
        $insights = [
            'en' => "Cohere AI Analysis: This diagnosis leverages large-scale automotive knowledge to identify patterns and provide contextual repair guidance.",
            'sq' => "Analiza Cohere AI: Kjo diagnozë përdor njohuri të gjera të automjeteve për të identifikuar modelet dhe për të dhënë udhëzim kontekstual riparimi.",
            'de' => "Cohere KI-Analyse: Diese Diagnose nutzt umfassendes Automobilwissen zur Mustererkennung und kontextuellen Reparaturberatung.",
            'fr' => "Analyse Cohere IA : Ce diagnostic exploite des connaissances automobiles étendues pour identifier les modèles et fournir des conseils de réparation contextuels.",
            'pt' => "Análise Cohere AI: Este diagnóstico aproveita conhecimento automotivo extenso para identificar padrões e fornecer orientação contextual de reparo.",
            'es' => "Análisis Cohere AI: Este diagnóstico aprovecha el conocimiento automotriz extenso para identificar patrones y proporcionar orientación contextual de reparación."
        ];
        return $insights[$language] ?? $insights['en'];
    }

    private function calculateConfidenceScore(array $response): float
    {
        $confidence = 0.8;

        // Check finish reason
        if (isset($response['generations'][0]['finish_reason'])) {
            $finishReason = $response['generations'][0]['finish_reason'];
            if ($finishReason === 'COMPLETE') {
                $confidence += 0.1;
            } elseif ($finishReason === 'MAX_TOKENS') {
                $confidence -= 0.1;
            }
        }

        // Check content length
        $contentLength = strlen($response['generations'][0]['text']);
        if ($contentLength > 1000) {
            $confidence += 0.05;
        } elseif ($contentLength < 200) {
            $confidence -= 0.15;
        }

        // Check likelihood scores if available
        if (isset($response['generations'][0]['likelihood'])) {
            $likelihood = $response['generations'][0]['likelihood'];
            if ($likelihood > 0.8) {
                $confidence += 0.05;
            } elseif ($likelihood < 0.5) {
                $confidence -= 0.1;
            }
        }

        return max(0.0, min(1.0, $confidence));
    }

    public function getProviderName(): string
    {
        return 'Cohere';
    }

    public function getEstimatedCost(array $data): float
    {
        // Cohere has free tier - return 0 for basic estimation
        return 0.0;
    }
}