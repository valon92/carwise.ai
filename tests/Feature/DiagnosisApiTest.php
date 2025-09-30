<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\DiagnosisSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class DiagnosisApiTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'role' => 'customer',
            'preferred_currency_id' => 1,
            'language' => 'en'
        ]);
    }

    /** @test */
    public function it_requires_authentication_to_start_diagnosis()
    {
        $response = $this->postJson('/api/diagnosis/start', [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
            'description' => 'Engine problems'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_starts_diagnosis_with_valid_data()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/diagnosis/start', [
            'make' => 'Toyota',
            'model' => 'Camry', 
            'year' => 2020,
            'mileage' => 50000,
            'engine_type' => '4 cylinder',
            'engine_size' => '2.5L',
            'description' => 'Engine makes strange noises',
            'symptoms' => ['Strange noises', 'Engine stops']
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'session_id',
                        'status'
                    ]
                ]);

        $this->assertDatabaseHas('diagnosis_sessions', [
            'user_id' => $this->user->id,
            'make' => 'Toyota',
            'model' => 'Camry',
            'description' => 'Engine makes strange noises'
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/diagnosis/start', [
            // Missing required fields
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['make', 'model', 'year', 'description']);
    }

    /** @test */
    public function it_validates_year_range()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/diagnosis/start', [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 1800, // Invalid year
            'description' => 'Engine problems'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['year']);
    }

    /** @test */
    public function it_retrieves_diagnosis_result()
    {
        Sanctum::actingAs($this->user);

        // Create a diagnosis session
        $session = DiagnosisSession::create([
            'user_id' => $this->user->id,
            'session_id' => 'test-session-123',
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
            'mileage' => 50000,
            'engine_type' => '4 cylinder',
            'engine_size' => '2.5L',
            'description' => 'Engine problems',
            'symptoms' => ['Strange noises'],
            'status' => 'completed',
            'confidence_score' => 85,
            'severity' => 'medium'
        ]);

        $response = $this->getJson("/api/diagnosis/result/{$session->session_id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'session' => [
                            'id',
                            'session_id',
                            'make',
                            'model',
                            'status'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_session()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/diagnosis/result/nonexistent-session');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_prevents_access_to_other_users_sessions()
    {
        $otherUser = User::factory()->create(['role' => 'customer']);
        
        $session = DiagnosisSession::create([
            'user_id' => $otherUser->id,
            'session_id' => 'other-user-session',
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => 2019,
            'description' => 'Brake problems',
            'symptoms' => ['Brake noise'],
            'status' => 'completed'
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/diagnosis/result/{$session->session_id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function it_handles_albanian_language_input()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/diagnosis/start', [
            'make' => 'Volkswagen',
            'model' => 'Golf',
            'year' => 2021,
            'mileage' => 25000,
            'engine_type' => '4 cylinder',
            'engine_size' => '1.5L',
            'description' => 'Motori bën zhurma të çuditshme',
            'symptoms' => ['Zhurma të forta', 'Ndalesa e motori']
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('diagnosis_sessions', [
            'user_id' => $this->user->id,
            'description' => 'Motori bën zhurma të çuditshme'
        ]);
    }

    /** @test */
    public function it_handles_portuguese_language_input()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/diagnosis/start', [
            'make' => 'Fiat',
            'model' => 'Uno',
            'year' => 2018,
            'mileage' => 80000,
            'engine_type' => '1.0',
            'engine_size' => '1.0L',
            'description' => 'O motor está fazendo barulhos estranhos',
            'symptoms' => ['Barulhos estranhos']
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('diagnosis_sessions', [
            'user_id' => $this->user->id,
            'description' => 'O motor está fazendo barulhos estranhos'
        ]);
    }

    /** @test */
    public function it_lists_user_diagnosis_history()
    {
        Sanctum::actingAs($this->user);

        // Create multiple diagnosis sessions
        DiagnosisSession::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->getJson('/api/diagnosis/history');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'session_id',
                            'make',
                            'model',
                            'status',
                            'created_at'
                        ]
                    ]
                ]);

        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_deletes_diagnosis_session()
    {
        Sanctum::actingAs($this->user);

        $session = DiagnosisSession::create([
            'user_id' => $this->user->id,
            'session_id' => 'delete-test-session',
            'make' => 'Toyota',
            'model' => 'Prius',
            'year' => 2020,
            'description' => 'Battery problems',
            'symptoms' => ['Battery dead'],
            'status' => 'completed'
        ]);

        $response = $this->deleteJson("/api/diagnosis/{$session->session_id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('diagnosis_sessions', [
            'session_id' => 'delete-test-session'
        ]);
    }
}

