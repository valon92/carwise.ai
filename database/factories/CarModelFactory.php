<?php

namespace Database\Factories;

use App\Models\CarModel;
use App\Models\CarBrand;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarModelFactory extends Factory
{
    protected $model = CarModel::class;

    public function definition(): array
    {
        $models = [
            'Camry', 'Corolla', 'RAV4', 'Prius', 'Highlander', // Toyota
            'Civic', 'Accord', 'CR-V', 'Pilot', 'Fit', // Honda
            'F-150', 'Explorer', 'Focus', 'Mustang', 'Escape', // Ford
            'Silverado', 'Equinox', 'Malibu', 'Tahoe', 'Cruze', // Chevrolet
            'Altima', 'Sentra', 'Rogue', 'Pathfinder', 'Maxima', // Nissan
            '3 Series', '5 Series', 'X3', 'X5', 'Z4', // BMW
            'C-Class', 'E-Class', 'GLE', 'GLC', 'A-Class', // Mercedes
            'A4', 'Q5', 'A3', 'Q7', 'A6', // Audi
            'Golf', 'Passat', 'Tiguan', 'Jetta', 'Atlas' // Volkswagen
        ];

        $name = $this->faker->randomElement($models);
        
        return [
            'brand_id' => CarBrand::factory(),
            'name' => $name,
            'slug' => strtolower(str_replace([' ', '-'], '-', $name)),
            'body_type' => $this->faker->randomElement(['Sedan', 'SUV', 'Hatchback', 'Coupe', 'Convertible', 'Wagon', 'Truck']),
            'fuel_type' => $this->faker->randomElement(['Gasoline', 'Diesel', 'Hybrid', 'Electric']),
            'engine_size' => $this->faker->randomElement(['1.0L', '1.5L', '2.0L', '2.5L', '3.0L', '3.5L', '4.0L']),
            'transmission' => $this->faker->randomElement(['Manual', 'Automatic', 'CVT']),
            'drivetrain' => $this->faker->randomElement(['FWD', 'RWD', 'AWD', '4WD']),
            'seating_capacity' => $this->faker->numberBetween(2, 8),
            'year_start' => $this->faker->numberBetween(1990, 2010),
            'year_end' => $this->faker->optional(0.3)->numberBetween(2015, date('Y')), // 30% chance of having an end year
            'msrp_min' => $this->faker->numberBetween(15000, 30000),
            'msrp_max' => $this->faker->numberBetween(35000, 80000),
            'is_popular' => $this->faker->boolean(25), // 25% chance of being popular
            'is_active' => true,
        ];
    }

    public function forBrand(CarBrand $brand): static
    {
        return $this->state(fn (array $attributes) => [
            'brand_id' => $brand->id,
        ]);
    }

    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_popular' => true,
        ]);
    }

    public function suv(): static
    {
        return $this->state(fn (array $attributes) => [
            'body_type' => 'SUV',
            'seating_capacity' => $this->faker->numberBetween(5, 8),
            'drivetrain' => $this->faker->randomElement(['AWD', '4WD']),
        ]);
    }

    public function sedan(): static
    {
        return $this->state(fn (array $attributes) => [
            'body_type' => 'Sedan',
            'seating_capacity' => $this->faker->numberBetween(4, 5),
            'drivetrain' => $this->faker->randomElement(['FWD', 'RWD']),
        ]);
    }

    public function electric(): static
    {
        return $this->state(fn (array $attributes) => [
            'fuel_type' => 'Electric',
            'engine_size' => null,
            'transmission' => 'Automatic',
        ]);
    }

    public function discontinued(): static
    {
        return $this->state(fn (array $attributes) => [
            'year_end' => $this->faker->numberBetween(2010, date('Y') - 1),
            'is_active' => false,
        ]);
    }
}

