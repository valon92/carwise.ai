<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class CarApiTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $brand;
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'role' => 'customer'
        ]);

        $this->brand = CarBrand::factory()->create([
            'name' => 'Toyota',
            'slug' => 'toyota'
        ]);

        $this->model = CarModel::factory()->create([
            'brand_id' => $this->brand->id,
            'name' => 'Camry',
            'slug' => 'camry'
        ]);
    }

    /** @test */
    public function it_requires_authentication_for_car_operations()
    {
        $response = $this->getJson('/api/cars');
        $response->assertStatus(401);

        $response = $this->postJson('/api/cars', []);
        $response->assertStatus(401);
    }

    /** @test */
    public function it_lists_user_cars()
    {
        Sanctum::actingAs($this->user);

        // Create cars for the user
        Car::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id
        ]);

        // Create car for another user (should not appear)
        $otherUser = User::factory()->create(['role' => 'customer']);
        Car::factory()->create([
            'user_id' => $otherUser->id,
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id
        ]);

        $response = $this->getJson('/api/cars');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'brand',
                            'model',
                            'year',
                            'license_plate',
                            'diagnosis_count',
                            'last_diagnosis'
                        ]
                    ]
                ]);

        $this->assertCount(2, $response->json('data'));
    }

    /** @test */
    public function it_creates_a_new_car()
    {
        Sanctum::actingAs($this->user);

        $carData = [
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id,
            'year' => 2020,
            'license_plate' => 'ABC-123',
            'color' => 'Blue',
            'mileage' => 50000,
            'engine_type' => '4 cylinder',
            'engine_size' => '2.5L',
            'fuel_type' => 'Gasoline',
            'transmission' => 'Automatic'
        ];

        $response = $this->postJson('/api/cars', $carData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'brand',
                        'model',
                        'year',
                        'license_plate'
                    ]
                ]);

        $this->assertDatabaseHas('cars', [
            'user_id' => $this->user->id,
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id,
            'license_plate' => 'ABC-123'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_for_car_creation()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/cars', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'brand_id',
                    'model_id', 
                    'year',
                    'license_plate'
                ]);
    }

    /** @test */
    public function it_validates_unique_license_plate_per_user()
    {
        Sanctum::actingAs($this->user);

        // Create first car
        Car::factory()->create([
            'user_id' => $this->user->id,
            'license_plate' => 'UNIQUE-123'
        ]);

        // Try to create another car with same license plate
        $response = $this->postJson('/api/cars', [
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id,
            'year' => 2021,
            'license_plate' => 'UNIQUE-123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['license_plate']);
    }

    /** @test */
    public function it_allows_same_license_plate_for_different_users()
    {
        $otherUser = User::factory()->create(['role' => 'customer']);
        
        // Create car for first user
        Car::factory()->create([
            'user_id' => $otherUser->id,
            'license_plate' => 'SHARED-123'
        ]);

        Sanctum::actingAs($this->user);

        // Create car for second user with same license plate
        $response = $this->postJson('/api/cars', [
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id,
            'year' => 2021,
            'license_plate' => 'SHARED-123'
        ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function it_shows_specific_car_details()
    {
        Sanctum::actingAs($this->user);

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id
        ]);

        $response = $this->getJson("/api/cars/{$car->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'brand',
                        'model', 
                        'year',
                        'license_plate',
                        'diagnosis_sessions',
                        'maintenance_history'
                    ]
                ]);
    }

    /** @test */
    public function it_prevents_access_to_other_users_cars()
    {
        $otherUser = User::factory()->create(['role' => 'customer']);
        $otherCar = Car::factory()->create([
            'user_id' => $otherUser->id
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/cars/{$otherCar->id}");
        $response->assertStatus(404);
    }

    /** @test */
    public function it_updates_car_information()
    {
        Sanctum::actingAs($this->user);

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id,
            'mileage' => 50000
        ]);

        $response = $this->putJson("/api/cars/{$car->id}", [
            'mileage' => 55000,
            'color' => 'Red'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'mileage' => 55000,
            'color' => 'Red'
        ]);
    }

    /** @test */
    public function it_deletes_a_car()
    {
        Sanctum::actingAs($this->user);

        $car = Car::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->deleteJson("/api/cars/{$car->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('cars', [
            'id' => $car->id
        ]);
    }

    /** @test */
    public function it_provides_car_statistics()
    {
        Sanctum::actingAs($this->user);

        // Create cars for the user
        Car::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->getJson('/api/cars/statistics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'total_cars',
                        'total_diagnoses',
                        'avg_mileage',
                        'most_common_brand'
                    ]
                ]);

        $this->assertEquals(3, $response->json('data.total_cars'));
    }

    /** @test */
    public function it_validates_year_range()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/cars', [
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id,
            'year' => 1800, // Invalid year
            'license_plate' => 'TEST-123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['year']);
    }

    /** @test */
    public function it_validates_mileage_is_positive()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/cars', [
            'brand_id' => $this->brand->id,
            'model_id' => $this->model->id,
            'year' => 2020,
            'license_plate' => 'TEST-123',
            'mileage' => -1000 // Invalid mileage
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['mileage']);
    }
}



