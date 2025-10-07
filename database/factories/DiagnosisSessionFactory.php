<?php

namespace Database\Factories;

use App\Models\DiagnosisSession;
use App\Models\User;
use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DiagnosisSessionFactory extends Factory
{
    protected $model = DiagnosisSession::class;

    public function definition(): array
    {
        $makes = ['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes', 'Audi', 'Volkswagen'];
        $models = ['Camry', 'Civic', 'Focus', '3 Series', 'C-Class', 'A4', 'Golf'];
        $engineTypes = ['4 cylinder', '6 cylinder', 'V6', 'V8', 'Hybrid'];
        $engineSizes = ['1.5L', '2.0L', '2.5L', '3.0L', '3.5L'];
        $statuses = ['pending', 'processing', 'completed', 'failed'];
        $severities = ['low', 'medium', 'high', 'critical'];

        $symptoms = [
            'Strange noises',
            'Engine misfiring', 
            'Brake problems',
            'Transmission issues',
            'Electrical problems',
            'Overheating',
            'Vibration',
            'Poor fuel economy'
        ];

        $descriptions = [
            'Engine makes unusual grinding noises when starting',
            'Car pulls to the right when braking',
            'Transmission slips between gears',
            'Engine overheats in traffic',
            'Strange vibration at highway speeds',
            'Battery drains overnight',
            'Air conditioning not working properly',
            'Fuel consumption has increased significantly'
        ];

        return [
            'user_id' => User::factory(),
            'car_id' => null, // Will be set if needed
            'session_id' => Str::uuid()->toString(),
            'make' => $this->faker->randomElement($makes),
            'model' => $this->faker->randomElement($models),
            'year' => $this->faker->numberBetween(2000, date('Y')),
            'mileage' => $this->faker->numberBetween(5000, 200000),
            'engine_type' => $this->faker->randomElement($engineTypes),
            'engine_size' => $this->faker->randomElement($engineSizes),
            'description' => $this->faker->randomElement($descriptions),
            'symptoms' => $this->faker->randomElements($symptoms, $this->faker->numberBetween(1, 3)),
            'status' => $this->faker->randomElement($statuses),
            'ai_response' => null,
            'confidence_score' => $this->faker->numberBetween(60, 95),
            'severity' => $this->faker->randomElement($severities),
            'processed_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'processed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'confidence_score' => $this->faker->numberBetween(80, 95),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'processed_at' => null,
        ]);
    }

    public function highSeverity(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'high',
            'confidence_score' => $this->faker->numberBetween(85, 95),
        ]);
    }

    public function engineIssue(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => 'Engine has performance issues and makes strange noises',
            'symptoms' => ['Engine misfiring', 'Strange noises', 'Poor performance'],
            'severity' => 'medium',
        ]);
    }

    public function brakeIssue(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => 'Brakes are making grinding noises and car pulls to one side',
            'symptoms' => ['Brake noise', 'Car pulling', 'Grinding sounds'],
            'severity' => 'high',
        ]);
    }

    public function withCar(Car $car): static
    {
        return $this->state(fn (array $attributes) => [
            'car_id' => $car->id,
            'user_id' => $car->user_id,
            'make' => $car->brand->name ?? $attributes['make'],
            'model' => $car->model->name ?? $attributes['model'],
            'year' => $car->year,
            'mileage' => $car->mileage,
            'engine_type' => $car->engine_type ?? $attributes['engine_type'],
            'engine_size' => $car->engine_size ?? $attributes['engine_size'],
        ]);
    }

    public function multilingual(): static
    {
        $languages = [
            'sq' => [
                'description' => 'Motori bën zhurma të çuditshme dhe nuk fillon mirë',
                'symptoms' => ['Zhurma të forta', 'Ndalesa e motori', 'Probleme me fillimin']
            ],
            'es' => [
                'description' => 'El motor hace ruidos extraños y no arranca bien',
                'symptoms' => ['Ruidos raros', 'Motor no arranca', 'Problemas de arranque']
            ],
            'pt' => [
                'description' => 'O motor está fazendo barulhos estranhos e não liga bem',
                'symptoms' => ['Barulhos estranhos', 'Motor não liga', 'Problemas de ignição']
            ],
            'fr' => [
                'description' => 'Le moteur fait des bruits bizarres et ne démarre pas bien',
                'symptoms' => ['Bruits étranges', 'Problème de démarrage', 'Motor défaillant']
            ]
        ];

        $lang = $this->faker->randomKey($languages);
        $content = $languages[$lang];

        return $this->state(fn (array $attributes) => [
            'description' => $content['description'],
            'symptoms' => $content['symptoms'],
        ]);
    }
}



