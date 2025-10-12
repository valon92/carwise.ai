<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'id' => 'basic',
                'name' => 'Basic',
                'description' => 'Perfect for occasional car owners',
                'price' => 4.99,
                'currency' => 'EUR',
                'billing_cycle' => 'monthly',
                'diagnoses_per_month' => 1,
                'features' => [
                    'basic_diagnosis',
                    'email_support',
                    'basic_reports',
                    'vehicle_management',
                ],
                'limits' => [
                    'vehicles' => 1,
                    'diagnoses_per_month' => 1,
                    'storage' => '100MB',
                    'api_calls_per_day' => 10,
                ],
                'is_active' => true,
                'is_popular' => false,
            ],
            [
                'id' => 'pro',
                'name' => 'Pro',
                'description' => 'Ideal for car enthusiasts and regular users',
                'price' => 9.99,
                'currency' => 'EUR',
                'billing_cycle' => 'monthly',
                'diagnoses_per_month' => 3,
                'features' => [
                    'ai_reports',
                    'service_offers',
                    'priority_support',
                    'advanced_analytics',
                    'parts_recommendations',
                    'maintenance_reminders',
                ],
                'limits' => [
                    'vehicles' => 3,
                    'diagnoses_per_month' => 3,
                    'storage' => '1GB',
                    'api_calls_per_day' => 50,
                ],
                'is_active' => true,
                'is_popular' => true,
            ],
            [
                'id' => 'elite',
                'name' => 'Elite',
                'description' => 'For professionals and fleet managers',
                'price' => 19.99,
                'currency' => 'EUR',
                'billing_cycle' => 'monthly',
                'diagnoses_per_month' => null, // unlimited
                'features' => [
                    'continuous_monitoring',
                    'ai_advice',
                    'preventive_care',
                    'white_label_reports',
                    'api_access',
                    'custom_integrations',
                    'dedicated_support',
                ],
                'limits' => [
                    'vehicles' => 'unlimited',
                    'diagnoses_per_month' => 'unlimited',
                    'storage' => '10GB',
                    'api_calls_per_day' => 'unlimited',
                ],
                'is_active' => true,
                'is_popular' => false,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['id' => $plan['id']],
                $plan
            );
        }
    }
}

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'id' => 'basic',
                'name' => 'Basic',
                'description' => 'Perfect for occasional car owners',
                'price' => 4.99,
                'currency' => 'EUR',
                'billing_cycle' => 'monthly',
                'diagnoses_per_month' => 1,
                'features' => [
                    'basic_diagnosis',
                    'email_support',
                    'basic_reports',
                    'vehicle_management',
                ],
                'limits' => [
                    'vehicles' => 1,
                    'diagnoses_per_month' => 1,
                    'storage' => '100MB',
                    'api_calls_per_day' => 10,
                ],
                'is_active' => true,
                'is_popular' => false,
            ],
            [
                'id' => 'pro',
                'name' => 'Pro',
                'description' => 'Ideal for car enthusiasts and regular users',
                'price' => 9.99,
                'currency' => 'EUR',
                'billing_cycle' => 'monthly',
                'diagnoses_per_month' => 3,
                'features' => [
                    'ai_reports',
                    'service_offers',
                    'priority_support',
                    'advanced_analytics',
                    'parts_recommendations',
                    'maintenance_reminders',
                ],
                'limits' => [
                    'vehicles' => 3,
                    'diagnoses_per_month' => 3,
                    'storage' => '1GB',
                    'api_calls_per_day' => 50,
                ],
                'is_active' => true,
                'is_popular' => true,
            ],
            [
                'id' => 'elite',
                'name' => 'Elite',
                'description' => 'For professionals and fleet managers',
                'price' => 19.99,
                'currency' => 'EUR',
                'billing_cycle' => 'monthly',
                'diagnoses_per_month' => null, // unlimited
                'features' => [
                    'continuous_monitoring',
                    'ai_advice',
                    'preventive_care',
                    'white_label_reports',
                    'api_access',
                    'custom_integrations',
                    'dedicated_support',
                ],
                'limits' => [
                    'vehicles' => 'unlimited',
                    'diagnoses_per_month' => 'unlimited',
                    'storage' => '10GB',
                    'api_calls_per_day' => 'unlimited',
                ],
                'is_active' => true,
                'is_popular' => false,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['id' => $plan['id']],
                $plan
            );
        }
    }
}






