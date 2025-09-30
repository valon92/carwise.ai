<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuthorizedCompany;

class AuthorizedCompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'AutoParts International',
                'slug' => 'autoparts-international',
                'description' => 'Leading international supplier of high-quality automotive parts with over 30 years of experience.',
                'logo_url' => '/images/companies/autoparts-international-logo.png',
                'website' => 'https://www.autoparts-int.com',
                'email' => 'sales@autoparts-int.com',
                'phone' => '+1-555-0123',
                'address' => '1234 Industrial Blvd',
                'city' => 'Detroit',
                'country' => 'USA',
                'postal_code' => '48201',
                'languages_supported' => ['en', 'de', 'fr', 'es'],
                'currencies_supported' => ['USD', 'EUR', 'GBP', 'CAD'],
                'countries_served' => ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'],
                'specializations' => ['engine', 'brakes', 'electrical', 'suspension', 'transmission'],
                'brands_authorized' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen', 'Toyota', 'Honda', 'Ford', 'Chevrolet'],
                'certification_body' => 'ISO 9001:2015',
                'certification_number' => 'ISO-9001-2023-001',
                'certification_date' => '2023-01-15',
                'certification_expiry' => '2026-01-15',
                'is_verified' => true,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'rating' => 4.8,
                'review_count' => 1250,
                'parts_count' => 15000,
                'orders_count' => 8500,
                'total_sales' => 2500000.00,
                'payment_methods' => ['credit_card', 'paypal', 'bank_transfer'],
                'shipping_methods' => ['standard', 'express', 'overnight'],
                'shipping_time_days' => 3,
                'shipping_cost_base' => 15.99,
                'shipping_cost_currency' => 'USD',
                'offers_warranty' => true,
                'warranty_months' => 24,
                'offers_installation' => true,
                'installation_cost_base' => 75.00,
                'installation_cost_currency' => 'USD',
                'return_policy' => '30-day return policy for unused parts in original packaging.',
                'terms_conditions' => 'Standard terms and conditions apply.',
                'social_media' => [
                    'facebook' => 'https://facebook.com/autopartsint',
                    'twitter' => 'https://twitter.com/autopartsint',
                    'linkedin' => 'https://linkedin.com/company/autopartsint'
                ],
                'contact_hours' => [
                    'monday' => '8:00 AM - 6:00 PM',
                    'tuesday' => '8:00 AM - 6:00 PM',
                    'wednesday' => '8:00 AM - 6:00 PM',
                    'thursday' => '8:00 AM - 6:00 PM',
                    'friday' => '8:00 AM - 5:00 PM',
                    'saturday' => '9:00 AM - 2:00 PM',
                    'sunday' => 'Closed'
                ],
                'timezone' => 'America/Detroit',
                'last_activity' => now(),
            ],
        ];

        foreach ($companies as $companyData) {
            AuthorizedCompany::create($companyData);
        }
    }
}