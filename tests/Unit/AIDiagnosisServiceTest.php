<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AIDiagnosisService;
use App\Models\DiagnosisSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIDiagnosisServiceTest extends TestCase
{
    use RefreshDatabase;

    private $aiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->aiService = new AIDiagnosisService();
    }

    /** @test */
    public function it_returns_provider_info()
    {
        $info = $this->aiService->getProviderInfo();
        
        $this->assertIsArray($info);
        $this->assertArrayHasKey('provider', $info);
        $this->assertArrayHasKey('available', $info);
        $this->assertArrayHasKey('fallback_enabled', $info);
    }

    /** @test */
    public function it_analyzes_diagnosis_with_valid_data()
    {
        $data = [
            'car_brand' => 'Toyota',
            'car_model' => 'Camry',
            'car_year' => 2020,
            'mileage' => 50000,
            'engine_type' => '4 cylinder',
            'engine_size' => '2.5L',
            'symptoms' => ['Strange noises', 'Engine stops'],
            'problem_description' => 'Engine makes strange noises and stops working',
            'user_currency_id' => 1,
            'user_language' => 'en'
        ];

        $result = $this->aiService->analyzeDiagnosis($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('problem_title', $result);
        $this->assertArrayHasKey('problem_description', $result);
        $this->assertArrayHasKey('severity', $result);
        $this->assertArrayHasKey('confidence_score', $result);
        $this->assertArrayHasKey('likely_causes', $result);
        $this->assertArrayHasKey('recommended_actions', $result);
    }

    /** @test */
    public function it_handles_albanian_input()
    {
        $data = [
            'car_brand' => 'Toyota',
            'car_model' => 'Camry',
            'car_year' => 2020,
            'mileage' => 50000,
            'engine_type' => '4 cylinder',
            'engine_size' => '2.5L',
            'symptoms' => ['Zhurma të çuditshme', 'Ndalesa e motori'],
            'problem_description' => 'Motori bën zhurma të çuditshme dhe nuk fillon mirë',
            'user_currency_id' => 1,
            'user_language' => 'sq'
        ];

        $result = $this->aiService->analyzeDiagnosis($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('problem_title', $result);
        // Should contain Albanian text or be translated
        $this->assertNotEmpty($result['problem_title']);
    }

    /** @test */
    public function it_detects_engine_problems()
    {
        $data = [
            'car_brand' => 'Honda',
            'car_model' => 'Civic',
            'car_year' => 2018,
            'mileage' => 75000,
            'engine_type' => '4 cylinder',
            'engine_size' => '2.0L',
            'symptoms' => ['Engine won\'t start'],
            'problem_description' => 'Engine won\'t start properly',
            'user_currency_id' => 1,
            'user_language' => 'en'
        ];

        $result = $this->aiService->analyzeDiagnosis($data);

        $this->assertStringContainsStringIgnoringCase('engine', $result['problem_title']);
        $this->assertStringContainsStringIgnoringCase('start', $result['problem_title']);
    }

    /** @test */
    public function it_detects_noise_problems()
    {
        $data = [
            'car_brand' => 'Ford',
            'car_model' => 'Focus',
            'car_year' => 2019,
            'mileage' => 60000,
            'engine_type' => '4 cylinder',
            'engine_size' => '2.0L',
            'symptoms' => ['Strange noises'],
            'problem_description' => 'Car makes unusual noise while driving',
            'user_currency_id' => 1,
            'user_language' => 'en'
        ];

        $result = $this->aiService->analyzeDiagnosis($data);

        $this->assertStringContainsStringIgnoringCase('noise', $result['problem_title']);
    }

    /** @test */
    public function it_includes_cost_estimates()
    {
        $data = [
            'car_brand' => 'BMW',
            'car_model' => 'X3',
            'car_year' => 2021,
            'mileage' => 30000,
            'engine_type' => '6 cylinder',
            'engine_size' => '3.0L',
            'symptoms' => ['Engine issues'],
            'problem_description' => 'Engine has performance issues',
            'user_currency_id' => 1,
            'user_language' => 'en'
        ];

        $result = $this->aiService->analyzeDiagnosis($data);

        $this->assertArrayHasKey('estimated_costs', $result);
        $this->assertIsArray($result['estimated_costs']);
        
        if (!empty($result['estimated_costs'])) {
            $firstCost = $result['estimated_costs'][0];
            $this->assertArrayHasKey('service', $firstCost);
            $this->assertArrayHasKey('min', $firstCost);
            $this->assertArrayHasKey('max', $firstCost);
        }
    }

    /** @test */
    public function it_includes_ai_insights()
    {
        $data = [
            'car_brand' => 'Mercedes',
            'car_model' => 'C-Class',
            'car_year' => 2020,
            'mileage' => 45000,
            'engine_type' => '4 cylinder',
            'engine_size' => '2.0L',
            'symptoms' => ['Performance issues'],
            'problem_description' => 'Car has reduced performance',
            'user_currency_id' => 1,
            'user_language' => 'en'
        ];

        $result = $this->aiService->analyzeDiagnosis($data);

        $this->assertArrayHasKey('ai_insights', $result);
        $this->assertIsArray($result['ai_insights']);
        $this->assertNotEmpty($result['ai_insights']);
    }

    /** @test */
    public function it_handles_missing_data_gracefully()
    {
        $data = [
            'car_brand' => 'Unknown',
            'symptoms' => [],
            'problem_description' => '',
            'user_language' => 'en'
        ];

        $result = $this->aiService->analyzeDiagnosis($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('problem_title', $result);
        $this->assertNotEmpty($result['problem_title']);
    }

    /** @test */
    public function it_provides_urgency_levels()
    {
        $data = [
            'car_brand' => 'Audi',
            'car_model' => 'A4',
            'car_year' => 2019,
            'mileage' => 55000,
            'engine_type' => '4 cylinder',
            'engine_size' => '2.0L',
            'symptoms' => ['Brake issues'],
            'problem_description' => 'Brakes are not working properly',
            'user_currency_id' => 1,
            'user_language' => 'en'
        ];

        $result = $this->aiService->analyzeDiagnosis($data);

        $this->assertArrayHasKey('requires_immediate_attention', $result);
        $this->assertIsBool($result['requires_immediate_attention']);
    }
}
