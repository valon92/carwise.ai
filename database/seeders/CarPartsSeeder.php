<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarPart;
use App\Models\AuthorizedCompany;

class CarPartsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first authorized company
        $company = AuthorizedCompany::first();
        
        $this->seedEngineParts($company);
        $this->seedBrakeParts($company);
        $this->seedElectricalParts($company);
        $this->seedSuspensionParts($company);
    }

    private function seedEngineParts($company): void
    {
        $parts = [
            [
                'name' => 'Oil Filter',
                'part_number' => 'OF-001',
                'description' => 'High-quality oil filter for engine protection. Removes contaminants from engine oil.',
                'category' => 'engine',
                'subcategory' => 'filters',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-11427566321',
                'aftermarket_brand' => 'Bosch',
                'aftermarket_number' => 'F026400323',
                'price' => 25.99,
                'currency' => 'USD',
                'stock_quantity' => 50,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'oem',
                'is_oem' => true,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'OEM Certified'],
                'weight' => '0.5 kg',
                'material' => 'Steel, Paper',
                'installation_time_minutes' => 15,
                'difficulty_level' => 'easy',
                'installation_notes' => 'Remove old filter, apply oil to gasket, install new filter hand-tight.',
                'warranty_months' => 12,
                'image_url' => '/images/parts/oil-filter.jpg',
                'slug' => 'oil-filter-of-001',
                'search_keywords' => ['oil', 'filter', 'engine', 'maintenance'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'supplier_name' => 'AutoParts Direct',
                'view_count' => 150,
                'purchase_count' => 25,
                'rating' => 4.5,
                'review_count' => 12,
            ],
            [
                'name' => 'Air Filter',
                'part_number' => 'AF-002',
                'description' => 'Premium air filter for improved engine breathing and fuel efficiency.',
                'category' => 'engine',
                'subcategory' => 'filters',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'Mercedes-Benz',
                'oem_number' => 'MB-A2710940100',
                'aftermarket_brand' => 'Mann-Filter',
                'aftermarket_number' => 'C3698/3',
                'price' => 18.50,
                'currency' => 'USD',
                'stock_quantity' => 75,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'premium',
                'is_oem' => false,
                'is_certified' => true,
                'certifications' => ['ISO 9001'],
                'weight' => '0.3 kg',
                'material' => 'Paper, Rubber',
                'installation_time_minutes' => 10,
                'difficulty_level' => 'easy',
                'installation_notes' => 'Remove air filter housing, replace filter, ensure proper seating.',
                'warranty_months' => 12,
                'image_url' => '/images/parts/air-filter.jpg',
                'slug' => 'air-filter-af-002',
                'search_keywords' => ['air', 'filter', 'engine', 'breathing'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 2,
                'supplier_name' => 'Filter World',
                'view_count' => 89,
                'purchase_count' => 18,
                'rating' => 4.3,
                'review_count' => 8,
            ],
        ];

        foreach ($parts as $partData) {
            $partData['authorized_company_id'] = $company->id;
            $partData['company_part_number'] = $company->slug . '-' . $partData['part_number'];
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = $company->countries_served;
            CarPart::create($partData);
        }
    }

    private function seedBrakeParts($company): void
    {
        $parts = [
            [
                'name' => 'Brake Pads (Front)',
                'part_number' => 'BP-004',
                'description' => 'High-performance ceramic brake pads for superior stopping power and reduced brake dust.',
                'category' => 'brakes',
                'subcategory' => 'pads',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-34116798611',
                'aftermarket_brand' => 'Brembo',
                'aftermarket_number' => 'P85077',
                'price' => 89.99,
                'currency' => 'USD',
                'stock_quantity' => 20,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'premium',
                'is_oem' => false,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'E-Mark'],
                'weight' => '1.2 kg',
                'material' => 'Ceramic, Steel',
                'installation_time_minutes' => 60,
                'difficulty_level' => 'medium',
                'installation_notes' => 'Remove wheel, caliper, old pads, install new pads, reassemble.',
                'warranty_months' => 12,
                'image_url' => '/images/parts/brake-pads.jpg',
                'slug' => 'brake-pads-front-bp-004',
                'search_keywords' => ['brake', 'pads', 'front', 'ceramic'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 4,
                'supplier_name' => 'Brake Masters',
                'view_count' => 200,
                'purchase_count' => 35,
                'rating' => 4.6,
                'review_count' => 18,
            ],
        ];

        foreach ($parts as $partData) {
            $partData['authorized_company_id'] = $company->id;
            $partData['company_part_number'] = $company->slug . '-' . $partData['part_number'];
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = $company->countries_served;
            CarPart::create($partData);
        }
    }

    private function seedElectricalParts($company): void
    {
        $parts = [
            [
                'name' => 'Battery',
                'part_number' => 'BAT-006',
                'description' => 'High-capacity AGM battery for reliable starting power. Maintenance-free design.',
                'category' => 'electrical',
                'subcategory' => 'battery',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-61219177500',
                'aftermarket_brand' => 'Varta',
                'aftermarket_number' => 'E11',
                'price' => 189.99,
                'currency' => 'USD',
                'stock_quantity' => 10,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'oem',
                'is_oem' => true,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'OEM Certified'],
                'weight' => '18.5 kg',
                'material' => 'Lead-Acid, AGM',
                'installation_time_minutes' => 30,
                'difficulty_level' => 'medium',
                'installation_notes' => 'Disconnect terminals, remove old battery, install new battery, connect terminals.',
                'warranty_months' => 36,
                'image_url' => '/images/parts/battery.jpg',
                'slug' => 'battery-bat-006',
                'search_keywords' => ['battery', 'electrical', 'starting', 'power'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 6,
                'supplier_name' => 'Battery Central',
                'view_count' => 180,
                'purchase_count' => 8,
                'rating' => 4.8,
                'review_count' => 15,
            ],
        ];

        foreach ($parts as $partData) {
            $partData['authorized_company_id'] = $company->id;
            $partData['company_part_number'] = $company->slug . '-' . $partData['part_number'];
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = $company->countries_served;
            CarPart::create($partData);
        }
    }

    private function seedSuspensionParts($company): void
    {
        $parts = [
            [
                'name' => 'Shock Absorbers (Front)',
                'part_number' => 'SA-008',
                'description' => 'Gas-filled shock absorbers for improved ride comfort and handling.',
                'category' => 'suspension',
                'subcategory' => 'shocks',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-31306775064',
                'aftermarket_brand' => 'Bilstein',
                'aftermarket_number' => 'B4-22-065680',
                'price' => 159.99,
                'currency' => 'USD',
                'stock_quantity' => 8,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'premium',
                'is_oem' => false,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'TÜV Approved'],
                'weight' => '3.8 kg',
                'material' => 'Steel, Oil',
                'installation_time_minutes' => 150,
                'difficulty_level' => 'hard',
                'installation_notes' => 'Remove wheel, disconnect suspension components, remove old shock, install new shock.',
                'warranty_months' => 24,
                'image_url' => '/images/parts/shock-absorbers.jpg',
                'slug' => 'shock-absorbers-front-sa-008',
                'search_keywords' => ['shock', 'absorbers', 'suspension', 'damping'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 8,
                'supplier_name' => 'Suspension Specialists',
                'view_count' => 110,
                'purchase_count' => 6,
                'rating' => 4.6,
                'review_count' => 9,
            ],
        ];

        foreach ($parts as $partData) {
            $partData['authorized_company_id'] = $company->id;
            $partData['company_part_number'] = $company->slug . '-' . $partData['part_number'];
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = $company->countries_served;
            CarPart::create($partData);
        }
    }
}