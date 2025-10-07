<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\User;
use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'brand_id' => CarBrand::factory(),
            'model_id' => CarModel::factory(),
            'year' => $this->faker->numberBetween(1990, date('Y')),
            'license_plate' => strtoupper($this->faker->bothify('???-###')),
            'color' => $this->faker->randomElement(['Red', 'Blue', 'White', 'Black', 'Silver', 'Gray', 'Green']),
            'mileage' => $this->faker->numberBetween(0, 300000),
            'engine_type' => $this->faker->randomElement(['4 cylinder', '6 cylinder', '8 cylinder', 'V6', 'V8']),
            'engine_size' => $this->faker->randomElement(['1.0L', '1.5L', '2.0L', '2.5L', '3.0L', '3.5L', '4.0L']),
            'fuel_type' => $this->faker->randomElement(['Gasoline', 'Diesel', 'Hybrid', 'Electric']),
            'transmission' => $this->faker->randomElement(['Manual', 'Automatic', 'CVT']),
            'purchase_date' => $this->faker->dateTimeBetween('-10 years', 'now'),
            'purchase_price' => $this->faker->numberBetween(5000, 80000),
            'current_value' => $this->faker->numberBetween(3000, 70000),
            'vin' => strtoupper($this->faker->bothify('??########???????')),
            'insurance_company' => $this->faker->company(),
            'insurance_policy_number' => $this->faker->bothify('POL-#########'),
            'insurance_expiry' => $this->faker->dateTimeBetween('now', '+2 years'),
            'last_service_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'next_service_due' => $this->faker->dateTimeBetween('now', '+6 months'),
            'registration_expiry' => $this->faker->dateTimeBetween('now', '+2 years'),
            'notes' => $this->faker->optional()->sentence(),
            'is_primary' => false,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    public function withHighMileage(): static
    {
        return $this->state(fn (array $attributes) => [
            'mileage' => $this->faker->numberBetween(200000, 400000),
        ]);
    }

    public function newCar(): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => $this->faker->numberBetween(date('Y') - 2, date('Y')),
            'mileage' => $this->faker->numberBetween(0, 25000),
        ]);
    }

    public function oldCar(): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => $this->faker->numberBetween(1990, 2010),
            'mileage' => $this->faker->numberBetween(100000, 350000),
        ]);
    }
}



