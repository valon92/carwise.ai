<?php

namespace Database\Factories;

use App\Models\CarBrand;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarBrandFactory extends Factory
{
    protected $model = CarBrand::class;

    public function definition(): array
    {
        $brands = [
            'Toyota', 'Honda', 'Ford', 'Chevrolet', 'Nissan', 'BMW', 'Mercedes-Benz', 
            'Audi', 'Volkswagen', 'Hyundai', 'Kia', 'Mazda', 'Subaru', 'Lexus',
            'Acura', 'Infiniti', 'Cadillac', 'Lincoln', 'Buick', 'GMC'
        ];

        $name = $this->faker->unique()->randomElement($brands);
        
        return [
            'name' => $name,
            'slug' => strtolower(str_replace([' ', '-'], '-', $name)),
            'logo_url' => null,
            'country' => $this->faker->country(),
            'founded_year' => $this->faker->numberBetween(1900, 2000),
            'website' => 'https://www.' . strtolower(str_replace([' ', '-'], '', $name)) . '.com',
            'is_popular' => $this->faker->boolean(30), // 30% chance of being popular
            'is_active' => true,
        ];
    }

    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_popular' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}



