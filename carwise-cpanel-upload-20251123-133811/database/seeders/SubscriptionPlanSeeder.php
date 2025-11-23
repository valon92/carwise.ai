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
                'description' => 'Perfect for new users who want to try the service',
                'price' => 0.00,
                'currency' => 'EUR',
                'billing_cycle' => 'monthly',
                'diagnoses_per_month' => 1,
                'features' => [
                    '1 manual diagnosis per month (no API connection)',
                    'Access to car articles library',
                    'Email notifications',
                    'Basic support'
                ],
                'limits' => [
                    'vehicles' => 1,
                    'diagnoses_per_month' => 1,
                    'api_access' => false,
                    'priority_support' => false
                ],
                'is_active' => true,
                'is_popular' => false
            ],
            [
                'id' => 'smart-ai',
                'name' => 'Smart AI',
                'description' => 'Pay-per-use detailed AI diagnosis with repair recommendations',
                'price' => 7.99,
                'currency' => 'EUR',
                'billing_cycle' => 'monthly',
                'diagnoses_per_month' => null, // pay-per-use
                'features' => [
                    'Detailed AI diagnosis + repair recommendations',
                    'Temporary API access for DTC retrieval',
                    'PDF report for diagnosis',
                    'Multi-brand API connection support'
                ],
                'limits' => [
                    'vehicles' => 1,
                    'diagnoses_per_month' => 'pay_per_use',
                    'api_access' => true,
                    'priority_support' => false
                ],
                'is_active' => true,
                'is_popular' => false
            ],
            [
                'id' => 'care-plus',
                'name' => 'Care+',
                'description' => 'Monthly coverage with preventive suggestions',
                'price' => 9.99,
                'currency' => 'EUR',
                'billing_cycle' => 'monthly',
                'diagnoses_per_month' => 3,
                'features' => [
                    '3 diagnoses per month (with API)',
                    'Repair history tracking',
                    'Priority support',
                    '10% discount coupon for recommended parts',
                    'Preventive maintenance suggestions'
                ],
                'limits' => [
                    'vehicles' => 3,
                    'diagnoses_per_month' => 3,
                    'api_access' => true,
                    'priority_support' => true
                ],
                'is_active' => true,
                'is_popular' => true
            ],
            [
                'id' => 'pro-garage',
                'name' => 'Pro Garage',
                'description' => 'For services & mechanics (B2B)',
                'price' => 99.00,
                'currency' => 'EUR',
                'billing_cycle' => 'monthly',
                'diagnoses_per_month' => null, // unlimited for B2B
                'features' => [
                    'Multi-user access',
                    'Detailed technical reports',
                    'Integration with work system',
                    'Reduced commission for parts distributed through platform',
                    'User licenses + dedicated API keys',
                    'Priority B2B support'
                ],
                'limits' => [
                    'vehicles' => 'unlimited',
                    'diagnoses_per_month' => 'unlimited',
                    'api_access' => true,
                    'priority_support' => true
                ],
                'is_active' => true,
                'is_popular' => false
            ]
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['id' => $plan['id']],
                $plan
            );
        }
    }
}