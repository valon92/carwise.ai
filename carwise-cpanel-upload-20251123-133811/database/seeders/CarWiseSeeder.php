<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Car;
use App\Models\Mechanic;
use App\Models\Diagnosis;
use Illuminate\Support\Facades\Hash;

class CarWiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo customer
        $customer = User::create([
            'name' => 'Demo User',
            'email' => 'demo@carwise.ai',
            'password' => Hash::make('password123'),
            'phone' => '+383 44 123 456',
            'role' => 'customer',
        ]);

        // Create demo cars
        $car1 = Car::create([
            'user_id' => $customer->id,
            'brand' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
            'vin' => '1HGBH41JXMN109186',
            'color' => 'Silver',
            'license_plate' => 'PRI-1234',
        ]);

        $car2 = Car::create([
            'user_id' => $customer->id,
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2018,
            'vin' => '2HGBH41JXMN109187',
            'color' => 'Blue',
            'license_plate' => 'PRI-5678',
        ]);

        // Create demo mechanics
        $mechanic1 = User::create([
            'name' => 'Arben Krasniqi',
            'email' => 'arben@carwise.ai',
            'password' => Hash::make('password123'),
            'phone' => '+383 44 234 567',
            'role' => 'mechanic',
        ]);

        Mechanic::create([
            'user_id' => $mechanic1->id,
            'experience_years' => 15,
            'expertise' => ['Engine', 'Transmission', 'Diagnostics'],
            'location' => 'Prishtina',
            'hourly_rate' => 25.00,
            'rating' => 4.8,
            'review_count' => 127,
            'availability' => 'available',
            'bio' => 'Experienced mechanic with 15 years in automotive repair. Specialized in engine and transmission work.',
            'is_verified' => true,
        ]);

        $mechanic2 = User::create([
            'name' => 'Blerim Gashi',
            'email' => 'blerim@carwise.ai',
            'password' => Hash::make('password123'),
            'phone' => '+383 44 345 678',
            'role' => 'mechanic',
        ]);

        Mechanic::create([
            'user_id' => $mechanic2->id,
            'experience_years' => 12,
            'expertise' => ['Brakes', 'Electrical', 'Diagnostics'],
            'location' => 'Peja',
            'hourly_rate' => 22.00,
            'rating' => 4.9,
            'review_count' => 89,
            'availability' => 'available',
            'bio' => 'Expert in brake systems and electrical diagnostics. Quick and reliable service.',
            'is_verified' => true,
        ]);

        // Create demo diagnoses
        Diagnosis::create([
            'car_id' => $car1->id,
            'user_id' => $customer->id,
            'media_file' => 'diagnosis_media/sample_oil_leak.jpg',
            'media_type' => 'image',
            'description' => 'Noticed oil spots under the car',
            'ai_analysis' => [
                'problem' => 'Engine oil leak detected near the oil pan gasket',
                'confidence' => 85,
                'solutions' => [
                    'Check oil pan gasket for damage or wear',
                    'Replace oil pan gasket if necessary',
                    'Check oil level and top up if needed',
                    'Monitor for continued leaks after repair'
                ],
                'next_steps' => 'This appears to be a minor oil leak. We recommend having a mechanic inspect the oil pan gasket and replace it if necessary.'
            ],
            'problem' => 'Engine oil leak detected near the oil pan gasket',
            'confidence' => 85,
            'solutions' => [
                'Check oil pan gasket for damage or wear',
                'Replace oil pan gasket if necessary',
                'Check oil level and top up if needed',
                'Monitor for continued leaks after repair'
            ],
            'next_steps' => 'This appears to be a minor oil leak. We recommend having a mechanic inspect the oil pan gasket and replace it if necessary.',
            'status' => 'completed',
        ]);

        Diagnosis::create([
            'car_id' => $car2->id,
            'user_id' => $customer->id,
            'media_file' => 'diagnosis_media/sample_brake_noise.mp3',
            'media_type' => 'audio',
            'description' => 'Squeaking noise when braking',
            'ai_analysis' => [
                'problem' => 'Brake pad wear detected - squeaking indicates worn pads',
                'confidence' => 92,
                'solutions' => [
                    'Replace brake pads',
                    'Check brake rotors for damage',
                    'Inspect brake fluid level',
                    'Test brake system after repair'
                ],
                'next_steps' => 'Brake pads need immediate replacement. This is a safety issue and should be addressed promptly.'
            ],
            'problem' => 'Brake pad wear detected - squeaking indicates worn pads',
            'confidence' => 92,
            'solutions' => [
                'Replace brake pads',
                'Check brake rotors for damage',
                'Inspect brake fluid level',
                'Test brake system after repair'
            ],
            'next_steps' => 'Brake pads need immediate replacement. This is a safety issue and should be addressed promptly.',
            'status' => 'completed',
        ]);
    }
}
