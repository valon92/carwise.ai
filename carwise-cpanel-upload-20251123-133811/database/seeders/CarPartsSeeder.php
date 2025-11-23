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
        
        if (!$company) {
            $this->command->warn('No authorized company found. Creating a default one...');
            $company = AuthorizedCompany::create([
                'name' => 'AutoParts International',
                'slug' => 'autoparts-international',
                'description' => 'Leading supplier of automotive parts worldwide',
                'website' => 'https://autoparts-international.com',
                'email' => 'info@autoparts-international.com',
                'phone' => '+1-555-0123',
                'address' => '123 Auto Parts Ave',
                'city' => 'Detroit',
                'country' => 'USA',
                'postal_code' => '48201',
                'is_active' => true,
            ]);
        }
        
        // Only seed new parts if they don't exist
        if (CarPart::where('part_number', 'TOY-CAM-AF-001')->doesntExist()) {
            $this->seedToyotaParts($company);
        }
        if (CarPart::where('part_number', 'HON-CIV-OF-003')->doesntExist()) {
            $this->seedHondaParts($company);
        }
        if (CarPart::where('part_number', 'FOR-F150-AF-005')->doesntExist()) {
            $this->seedFordParts($company);
        }
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
            $partData['company_part_number'] = 'autoparts-international-' . $partData['part_number'];
            $partData['slug'] = $partData['slug'] . '-' . time() . '-' . rand(1000, 9999);
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'];
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
            $partData['company_part_number'] = 'autoparts-international-' . $partData['part_number'];
            $partData['slug'] = $partData['slug'] . '-' . time() . '-' . rand(1000, 9999);
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'];
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
            $partData['company_part_number'] = 'autoparts-international-' . $partData['part_number'];
            $partData['slug'] = $partData['slug'] . '-' . time() . '-' . rand(1000, 9999);
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'];
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
            $partData['company_part_number'] = 'autoparts-international-' . $partData['part_number'];
            $partData['slug'] = $partData['slug'] . '-' . time() . '-' . rand(1000, 9999);
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'];
            CarPart::create($partData);
        }
    }

    private function seedTransmissionParts($company): void
    {
        $parts = [
            [
                'name' => 'Transmission Filter',
                'part_number' => 'TF-009',
                'description' => 'High-efficiency transmission filter for smooth gear shifting and extended transmission life.',
                'category' => 'transmission',
                'subcategory' => 'filters',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-24118616021',
                'aftermarket_brand' => 'Mann-Filter',
                'aftermarket_number' => 'HU7002z',
                'price' => 45.99,
                'currency' => 'USD',
                'stock_quantity' => 25,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'oem',
                'is_oem' => true,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'OEM Certified'],
                'weight' => '0.8 kg',
                'material' => 'Steel, Paper',
                'installation_time_minutes' => 45,
                'difficulty_level' => 'medium',
                'installation_notes' => 'Drain transmission fluid, remove pan, replace filter, reinstall pan.',
                'warranty_months' => 12,
                'image_url' => '/images/parts/transmission-filter.jpg',
                'slug' => 'transmission-filter-tf-009',
                'search_keywords' => ['transmission', 'filter', 'gearbox', 'fluid'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 9,
                'supplier_name' => 'Transmission Pro',
                'view_count' => 95,
                'purchase_count' => 12,
                'rating' => 4.4,
                'review_count' => 7,
            ],
            [
                'name' => 'CV Joint Boot Kit',
                'part_number' => 'CVB-010',
                'description' => 'Complete CV joint boot kit with grease for protecting drive axle joints.',
                'category' => 'transmission',
                'subcategory' => 'drivetrain',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-31216785021',
                'aftermarket_brand' => 'Febi',
                'aftermarket_number' => '31216',
                'price' => 32.50,
                'currency' => 'USD',
                'stock_quantity' => 40,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'standard',
                'is_oem' => false,
                'is_certified' => true,
                'certifications' => ['ISO 9001'],
                'weight' => '0.6 kg',
                'material' => 'Rubber, Grease',
                'installation_time_minutes' => 90,
                'difficulty_level' => 'medium',
                'installation_notes' => 'Remove wheel, disconnect CV joint, replace boot, repack with grease.',
                'warranty_months' => 12,
                'image_url' => '/images/parts/cv-boot-kit.jpg',
                'slug' => 'cv-joint-boot-kit-cvb-010',
                'search_keywords' => ['cv', 'joint', 'boot', 'axle'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 10,
                'supplier_name' => 'Drivetrain Solutions',
                'view_count' => 78,
                'purchase_count' => 15,
                'rating' => 4.2,
                'review_count' => 6,
            ],
        ];

        foreach ($parts as $partData) {
            $partData['authorized_company_id'] = $company->id;
            $partData['company_part_number'] = 'autoparts-international-' . $partData['part_number'];
            $partData['slug'] = $partData['slug'] . '-' . time() . '-' . rand(1000, 9999);
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'];
            CarPart::create($partData);
        }
    }

    private function seedExhaustParts($company): void
    {
        $parts = [
            [
                'name' => 'Catalytic Converter',
                'part_number' => 'CC-011',
                'description' => 'High-efficiency catalytic converter for reduced emissions and improved performance.',
                'category' => 'exhaust',
                'subcategory' => 'emissions',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-18307560123',
                'aftermarket_brand' => 'Bosal',
                'aftermarket_number' => '099-456',
                'price' => 299.99,
                'currency' => 'USD',
                'stock_quantity' => 6,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'oem',
                'is_oem' => true,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'EPA Certified'],
                'weight' => '8.5 kg',
                'material' => 'Stainless Steel, Ceramic',
                'installation_time_minutes' => 120,
                'difficulty_level' => 'hard',
                'installation_notes' => 'Remove exhaust system, disconnect oxygen sensors, install new converter.',
                'warranty_months' => 24,
                'image_url' => '/images/parts/catalytic-converter.jpg',
                'slug' => 'catalytic-converter-cc-011',
                'search_keywords' => ['catalytic', 'converter', 'emissions', 'exhaust'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 11,
                'supplier_name' => 'Emission Control Systems',
                'view_count' => 145,
                'purchase_count' => 8,
                'rating' => 4.7,
                'review_count' => 12,
            ],
            [
                'name' => 'Muffler',
                'part_number' => 'MUF-012',
                'description' => 'Performance muffler for reduced noise and improved exhaust flow.',
                'category' => 'exhaust',
                'subcategory' => 'silencing',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-18307560124',
                'aftermarket_brand' => 'MagnaFlow',
                'aftermarket_number' => '11226',
                'price' => 189.99,
                'currency' => 'USD',
                'stock_quantity' => 12,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'premium',
                'is_oem' => false,
                'is_certified' => true,
                'certifications' => ['ISO 9001'],
                'weight' => '6.2 kg',
                'material' => 'Stainless Steel',
                'installation_time_minutes' => 60,
                'difficulty_level' => 'medium',
                'installation_notes' => 'Remove old muffler, install new muffler, check for leaks.',
                'warranty_months' => 12,
                'image_url' => '/images/parts/muffler.jpg',
                'slug' => 'muffler-muf-012',
                'search_keywords' => ['muffler', 'exhaust', 'silencer', 'performance'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 12,
                'supplier_name' => 'Exhaust Specialists',
                'view_count' => 112,
                'purchase_count' => 18,
                'rating' => 4.5,
                'review_count' => 9,
            ],
        ];

        foreach ($parts as $partData) {
            $partData['authorized_company_id'] = $company->id;
            $partData['company_part_number'] = 'autoparts-international-' . $partData['part_number'];
            $partData['slug'] = $partData['slug'] . '-' . time() . '-' . rand(1000, 9999);
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'];
            CarPart::create($partData);
        }
    }

    private function seedCoolingParts($company): void
    {
        $parts = [
            [
                'name' => 'Radiator',
                'part_number' => 'RAD-013',
                'description' => 'High-efficiency aluminum radiator for optimal engine cooling performance.',
                'category' => 'cooling',
                'subcategory' => 'radiator',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-17117560123',
                'aftermarket_brand' => 'Nissens',
                'aftermarket_number' => '60601A',
                'price' => 249.99,
                'currency' => 'USD',
                'stock_quantity' => 8,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'oem',
                'is_oem' => true,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'OEM Certified'],
                'weight' => '4.8 kg',
                'material' => 'Aluminum, Plastic',
                'installation_time_minutes' => 180,
                'difficulty_level' => 'hard',
                'installation_notes' => 'Drain coolant, remove hoses, remove radiator, install new radiator.',
                'warranty_months' => 24,
                'image_url' => '/images/parts/radiator.jpg',
                'slug' => 'radiator-rad-013',
                'search_keywords' => ['radiator', 'cooling', 'engine', 'aluminum'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 13,
                'supplier_name' => 'Cooling Systems Inc',
                'view_count' => 168,
                'purchase_count' => 5,
                'rating' => 4.6,
                'review_count' => 11,
            ],
            [
                'name' => 'Thermostat',
                'part_number' => 'THM-014',
                'description' => 'Precision thermostat for optimal engine temperature control.',
                'category' => 'cooling',
                'subcategory' => 'thermostat',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-11537560123',
                'aftermarket_brand' => 'Wahler',
                'aftermarket_number' => '3092.87D',
                'price' => 28.99,
                'currency' => 'USD',
                'stock_quantity' => 35,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'oem',
                'is_oem' => true,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'OEM Certified'],
                'weight' => '0.3 kg',
                'material' => 'Brass, Wax',
                'installation_time_minutes' => 30,
                'difficulty_level' => 'medium',
                'installation_notes' => 'Drain coolant, remove thermostat housing, replace thermostat.',
                'warranty_months' => 12,
                'image_url' => '/images/parts/thermostat.jpg',
                'slug' => 'thermostat-thm-014',
                'search_keywords' => ['thermostat', 'cooling', 'temperature', 'control'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 14,
                'supplier_name' => 'Temperature Control',
                'view_count' => 89,
                'purchase_count' => 22,
                'rating' => 4.3,
                'review_count' => 8,
            ],
        ];

        foreach ($parts as $partData) {
            $partData['authorized_company_id'] = $company->id;
            $partData['company_part_number'] = 'autoparts-international-' . $partData['part_number'];
            $partData['slug'] = $partData['slug'] . '-' . time() . '-' . rand(1000, 9999);
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'];
            CarPart::create($partData);
        }
    }

    private function seedLightingParts($company): void
    {
        $parts = [
            [
                'name' => 'LED Headlight Bulb',
                'part_number' => 'LED-015',
                'description' => 'High-performance LED headlight bulb for improved visibility and energy efficiency.',
                'category' => 'lighting',
                'subcategory' => 'headlights',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-63117160123',
                'aftermarket_brand' => 'Philips',
                'aftermarket_number' => 'H7-LED',
                'price' => 89.99,
                'currency' => 'USD',
                'stock_quantity' => 50,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'premium',
                'is_oem' => false,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'DOT Approved'],
                'weight' => '0.2 kg',
                'material' => 'LED, Aluminum',
                'installation_time_minutes' => 15,
                'difficulty_level' => 'easy',
                'installation_notes' => 'Remove old bulb, install new LED bulb, test operation.',
                'warranty_months' => 24,
                'image_url' => '/images/parts/led-headlight.jpg',
                'slug' => 'led-headlight-bulb-led-015',
                'search_keywords' => ['led', 'headlight', 'bulb', 'lighting'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 15,
                'supplier_name' => 'Lighting Solutions',
                'view_count' => 234,
                'purchase_count' => 45,
                'rating' => 4.8,
                'review_count' => 28,
            ],
            [
                'name' => 'Brake Light Bulb',
                'part_number' => 'BLB-016',
                'description' => 'High-visibility brake light bulb for enhanced safety.',
                'category' => 'lighting',
                'subcategory' => 'taillights',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-63217160123',
                'aftermarket_brand' => 'Osram',
                'aftermarket_number' => 'P21W',
                'price' => 12.99,
                'currency' => 'USD',
                'stock_quantity' => 100,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'standard',
                'is_oem' => false,
                'is_certified' => true,
                'certifications' => ['ISO 9001'],
                'weight' => '0.05 kg',
                'material' => 'Glass, Tungsten',
                'installation_time_minutes' => 5,
                'difficulty_level' => 'easy',
                'installation_notes' => 'Remove old bulb, install new bulb, test brake lights.',
                'warranty_months' => 6,
                'image_url' => '/images/parts/brake-light-bulb.jpg',
                'slug' => 'brake-light-bulb-blb-016',
                'search_keywords' => ['brake', 'light', 'bulb', 'safety'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 16,
                'supplier_name' => 'Auto Lighting',
                'view_count' => 156,
                'purchase_count' => 78,
                'rating' => 4.2,
                'review_count' => 15,
            ],
        ];

        foreach ($parts as $partData) {
            $partData['authorized_company_id'] = $company->id;
            $partData['company_part_number'] = 'autoparts-international-' . $partData['part_number'];
            $partData['slug'] = $partData['slug'] . '-' . time() . '-' . rand(1000, 9999);
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'];
            CarPart::create($partData);
        }
    }

    private function seedInteriorParts($company): void
    {
        $parts = [
            [
                'name' => 'Cabin Air Filter',
                'part_number' => 'CAF-017',
                'description' => 'High-efficiency cabin air filter for clean interior air and improved HVAC performance.',
                'category' => 'interior',
                'subcategory' => 'air_quality',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-64319260123',
                'aftermarket_brand' => 'Mann-Filter',
                'aftermarket_number' => 'CU24007',
                'price' => 24.99,
                'currency' => 'USD',
                'stock_quantity' => 60,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'premium',
                'is_oem' => false,
                'is_certified' => true,
                'certifications' => ['ISO 9001'],
                'weight' => '0.4 kg',
                'material' => 'Paper, Activated Carbon',
                'installation_time_minutes' => 20,
                'difficulty_level' => 'easy',
                'installation_notes' => 'Remove glove box, replace filter, reinstall glove box.',
                'warranty_months' => 12,
                'image_url' => '/images/parts/cabin-air-filter.jpg',
                'slug' => 'cabin-air-filter-caf-017',
                'search_keywords' => ['cabin', 'air', 'filter', 'interior'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 17,
                'supplier_name' => 'Interior Solutions',
                'view_count' => 198,
                'purchase_count' => 52,
                'rating' => 4.4,
                'review_count' => 19,
            ],
            [
                'name' => 'Floor Mats Set',
                'part_number' => 'FMS-018',
                'description' => 'Premium all-weather floor mats for complete interior protection.',
                'category' => 'interior',
                'subcategory' => 'protection',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-51477160123',
                'aftermarket_brand' => 'WeatherTech',
                'aftermarket_number' => 'WT-456789',
                'price' => 149.99,
                'currency' => 'USD',
                'stock_quantity' => 20,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'premium',
                'is_oem' => false,
                'is_certified' => true,
                'certifications' => ['ISO 9001'],
                'weight' => '3.2 kg',
                'material' => 'Rubber, Plastic',
                'installation_time_minutes' => 10,
                'difficulty_level' => 'easy',
                'installation_notes' => 'Remove old mats, install new floor mats.',
                'warranty_months' => 12,
                'image_url' => '/images/parts/floor-mats.jpg',
                'slug' => 'floor-mats-set-fms-018',
                'search_keywords' => ['floor', 'mats', 'interior', 'protection'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 18,
                'supplier_name' => 'Interior Protection',
                'view_count' => 167,
                'purchase_count' => 31,
                'rating' => 4.6,
                'review_count' => 14,
            ],
        ];

        foreach ($parts as $partData) {
            $partData['authorized_company_id'] = $company->id;
            $partData['company_part_number'] = 'autoparts-international-' . $partData['part_number'];
            $partData['slug'] = $partData['slug'] . '-' . time() . '-' . rand(1000, 9999);
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'];
            CarPart::create($partData);
        }
    }

    private function seedExteriorParts($company): void
    {
        $parts = [
            [
                'name' => 'Windshield Wipers',
                'part_number' => 'WW-019',
                'description' => 'High-performance windshield wipers for clear visibility in all weather conditions.',
                'category' => 'exterior',
                'subcategory' => 'visibility',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-61617160123',
                'aftermarket_brand' => 'Bosch',
                'aftermarket_number' => 'AEROTWIN',
                'price' => 34.99,
                'currency' => 'USD',
                'stock_quantity' => 80,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'premium',
                'is_oem' => false,
                'is_certified' => true,
                'certifications' => ['ISO 9001'],
                'weight' => '0.8 kg',
                'material' => 'Rubber, Steel',
                'installation_time_minutes' => 10,
                'difficulty_level' => 'easy',
                'installation_notes' => 'Remove old wipers, install new wipers, test operation.',
                'warranty_months' => 6,
                'image_url' => '/images/parts/windshield-wipers.jpg',
                'slug' => 'windshield-wipers-ww-019',
                'search_keywords' => ['windshield', 'wipers', 'visibility', 'weather'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 19,
                'supplier_name' => 'Visibility Solutions',
                'view_count' => 289,
                'purchase_count' => 67,
                'rating' => 4.5,
                'review_count' => 23,
            ],
            [
                'name' => 'Side Mirror Glass',
                'part_number' => 'SMG-020',
                'description' => 'Heated side mirror glass with anti-glare coating for improved visibility.',
                'category' => 'exterior',
                'subcategory' => 'mirrors',
                'compatible_brands' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen'],
                'compatible_models' => ['3 Series', 'C-Class', 'A4', 'Golf'],
                'compatible_years' => [2020, 2021, 2022, 2023],
                'engine_type' => 'gasoline',
                'manufacturer' => 'BMW',
                'oem_number' => 'BMW-51167160123',
                'aftermarket_brand' => 'Febi',
                'aftermarket_number' => '51167',
                'price' => 89.99,
                'currency' => 'USD',
                'stock_quantity' => 15,
                'in_stock' => true,
                'availability_status' => 'available',
                'quality_grade' => 'oem',
                'is_oem' => true,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'OEM Certified'],
                'weight' => '0.6 kg',
                'material' => 'Glass, Heating Element',
                'installation_time_minutes' => 45,
                'difficulty_level' => 'medium',
                'installation_notes' => 'Remove mirror housing, disconnect heating wires, replace glass.',
                'warranty_months' => 12,
                'image_url' => '/images/parts/side-mirror-glass.jpg',
                'slug' => 'side-mirror-glass-smg-020',
                'search_keywords' => ['side', 'mirror', 'glass', 'heated'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 20,
                'supplier_name' => 'Mirror Solutions',
                'view_count' => 123,
                'purchase_count' => 9,
                'rating' => 4.3,
                'review_count' => 6,
            ],
        ];

        foreach ($parts as $partData) {
            $partData['authorized_company_id'] = $company->id;
            $partData['company_part_number'] = 'autoparts-international-' . $partData['part_number'];
            $partData['slug'] = $partData['slug'] . '-' . time() . '-' . rand(1000, 9999);
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'];
            CarPart::create($partData);
        }
    }

    private function seedToyotaParts($company): void
    {
        $parts = [
            [
                'name' => 'Toyota Camry Air Filter',
                'part_number' => 'TOY-CAM-AF-001',
                'description' => 'High-quality air filter for Toyota Camry 2018-2024',
                'category' => 'engine',
                'subcategory' => 'air_filter',
                'compatible_brands' => ['Toyota'],
                'compatible_models' => ['Camry'],
                'compatible_years' => ['2018', '2019', '2020', '2021', '2022', '2023', '2024'],
                'manufacturer' => 'Toyota',
                'price' => 18.99,
                'currency' => 'USD',
                'stock_quantity' => 45,
                'in_stock' => true,
                'availability_status' => 'in_stock',
                'quality_grade' => 'OEM',
                'is_oem' => true,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'Toyota Certified'],
                'weight' => 0.5,
                'dimensions' => '8" x 6" x 2"',
                'material' => 'Paper',
                'color' => 'White',
                'installation_time_minutes' => 15,
                'difficulty_level' => 'Easy',
                'installation_notes' => 'Easy installation, no tools required',
                'warranty_months' => 12,
                'image_url' => 'https://picsum.photos/400/300?random=101',
                'slug' => 'toyota-camry-air-filter',
                'search_keywords' => 'toyota camry air filter oem',
                'meta_description' => 'Toyota Camry air filter - OEM quality',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
                'supplier_name' => 'Toyota Parts Direct',
                'supplier_contact' => 'parts@toyota.com',
                'supplier_website' => 'https://toyota.com/parts',
                'view_count' => 0,
                'purchase_count' => 0,
                'rating' => 4.8,
                'review_count' => 127,
            ],
            [
                'name' => 'Toyota Corolla Brake Pads',
                'part_number' => 'TOY-COR-BP-002',
                'description' => 'Premium brake pads for Toyota Corolla 2019-2024',
                'category' => 'brakes',
                'subcategory' => 'brake_pads',
                'compatible_brands' => ['Toyota'],
                'compatible_models' => ['Corolla'],
                'compatible_years' => ['2019', '2020', '2021', '2022', '2023', '2024'],
                'manufacturer' => 'Toyota',
                'price' => 65.99,
                'currency' => 'USD',
                'stock_quantity' => 32,
                'in_stock' => true,
                'availability_status' => 'in_stock',
                'quality_grade' => 'OEM',
                'is_oem' => true,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'Toyota Certified'],
                'weight' => 2.5,
                'dimensions' => '6" x 4" x 1"',
                'material' => 'Ceramic',
                'color' => 'Black',
                'installation_time_minutes' => 60,
                'difficulty_level' => 'Medium',
                'installation_notes' => 'Requires brake fluid change',
                'warranty_months' => 24,
                'image_url' => 'https://picsum.photos/400/300?random=102',
                'slug' => 'toyota-corolla-brake-pads',
                'search_keywords' => 'toyota corolla brake pads ceramic',
                'meta_description' => 'Toyota Corolla brake pads - Ceramic quality',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'supplier_name' => 'Toyota Parts Direct',
                'supplier_contact' => 'parts@toyota.com',
                'supplier_website' => 'https://toyota.com/parts',
                'view_count' => 0,
                'purchase_count' => 0,
                'rating' => 4.9,
                'review_count' => 89,
            ],
        ];

        foreach ($parts as $partData) {
            $partData['authorized_company_id'] = $company->id;
            $partData['company_part_number'] = 'toyota-parts-' . $partData['part_number'];
            $partData['slug'] = $partData['slug'] . '-' . time() . '-' . rand(1000, 9999);
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'];
            CarPart::create($partData);
        }
    }

    private function seedHondaParts($company): void
    {
        $parts = [
            [
                'name' => 'Honda Civic Oil Filter',
                'part_number' => 'HON-CIV-OF-003',
                'description' => 'Genuine Honda oil filter for Civic 2020-2024',
                'category' => 'engine',
                'subcategory' => 'oil_filter',
                'compatible_brands' => ['Honda'],
                'compatible_models' => ['Civic'],
                'compatible_years' => ['2020', '2021', '2022', '2023', '2024'],
                'manufacturer' => 'Honda',
                'price' => 12.99,
                'currency' => 'USD',
                'stock_quantity' => 67,
                'in_stock' => true,
                'availability_status' => 'in_stock',
                'quality_grade' => 'OEM',
                'is_oem' => true,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'Honda Certified'],
                'weight' => 0.3,
                'dimensions' => '3" x 3" x 2"',
                'material' => 'Metal',
                'color' => 'Silver',
                'installation_time_minutes' => 20,
                'difficulty_level' => 'Easy',
                'installation_notes' => 'Standard oil change procedure',
                'warranty_months' => 12,
                'image_url' => 'https://picsum.photos/400/300?random=103',
                'slug' => 'honda-civic-oil-filter',
                'search_keywords' => 'honda civic oil filter genuine',
                'meta_description' => 'Honda Civic oil filter - Genuine Honda part',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
                'supplier_name' => 'Honda Parts Direct',
                'supplier_contact' => 'parts@honda.com',
                'supplier_website' => 'https://honda.com/parts',
                'view_count' => 0,
                'purchase_count' => 0,
                'rating' => 4.7,
                'review_count' => 156,
            ],
            [
                'name' => 'Honda Accord Spark Plugs',
                'part_number' => 'HON-ACC-SP-004',
                'description' => 'Iridium spark plugs for Honda Accord 2019-2024',
                'category' => 'engine',
                'subcategory' => 'spark_plugs',
                'compatible_brands' => ['Honda'],
                'compatible_models' => ['Accord'],
                'compatible_years' => ['2019', '2020', '2021', '2022', '2023', '2024'],
                'manufacturer' => 'Honda',
                'price' => 45.99,
                'currency' => 'USD',
                'stock_quantity' => 28,
                'in_stock' => true,
                'availability_status' => 'in_stock',
                'quality_grade' => 'OEM',
                'is_oem' => true,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'Honda Certified'],
                'weight' => 0.8,
                'dimensions' => '4" x 0.5" x 0.5"',
                'material' => 'Iridium',
                'color' => 'Silver',
                'installation_time_minutes' => 45,
                'difficulty_level' => 'Medium',
                'installation_notes' => 'Requires spark plug socket',
                'warranty_months' => 36,
                'image_url' => 'https://picsum.photos/400/300?random=104',
                'slug' => 'honda-accord-spark-plugs',
                'search_keywords' => 'honda accord spark plugs iridium',
                'meta_description' => 'Honda Accord spark plugs - Iridium quality',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 4,
                'supplier_name' => 'Honda Parts Direct',
                'supplier_contact' => 'parts@honda.com',
                'supplier_website' => 'https://honda.com/parts',
                'view_count' => 0,
                'purchase_count' => 0,
                'rating' => 4.8,
                'review_count' => 203,
            ],
        ];

        foreach ($parts as $partData) {
            $partData['authorized_company_id'] = $company->id;
            $partData['company_part_number'] = 'honda-parts-' . $partData['part_number'];
            $partData['slug'] = $partData['slug'] . '-' . time() . '-' . rand(1000, 9999);
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'];
            CarPart::create($partData);
        }
    }

    private function seedFordParts($company): void
    {
        $parts = [
            [
                'name' => 'Ford F-150 Air Filter',
                'part_number' => 'FOR-F150-AF-005',
                'description' => 'Heavy-duty air filter for Ford F-150 2020-2024',
                'category' => 'engine',
                'subcategory' => 'air_filter',
                'compatible_brands' => ['Ford'],
                'compatible_models' => ['F-150'],
                'compatible_years' => ['2020', '2021', '2022', '2023', '2024'],
                'manufacturer' => 'Ford',
                'price' => 24.99,
                'currency' => 'USD',
                'stock_quantity' => 41,
                'in_stock' => true,
                'availability_status' => 'in_stock',
                'quality_grade' => 'OEM',
                'is_oem' => true,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'Ford Certified'],
                'weight' => 0.7,
                'dimensions' => '10" x 8" x 3"',
                'material' => 'Paper',
                'color' => 'White',
                'installation_time_minutes' => 25,
                'difficulty_level' => 'Easy',
                'installation_notes' => 'Heavy-duty filter for truck engines',
                'warranty_months' => 12,
                'image_url' => 'https://picsum.photos/400/300?random=105',
                'slug' => 'ford-f150-air-filter',
                'search_keywords' => 'ford f150 air filter heavy duty',
                'meta_description' => 'Ford F-150 air filter - Heavy-duty quality',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 5,
                'supplier_name' => 'Ford Parts Direct',
                'supplier_contact' => 'parts@ford.com',
                'supplier_website' => 'https://ford.com/parts',
                'view_count' => 0,
                'purchase_count' => 0,
                'rating' => 4.6,
                'review_count' => 94,
            ],
            [
                'name' => 'Ford Mustang Brake Rotors',
                'part_number' => 'FOR-MUS-BR-006',
                'description' => 'Performance brake rotors for Ford Mustang 2019-2024',
                'category' => 'brakes',
                'subcategory' => 'brake_rotors',
                'compatible_brands' => ['Ford'],
                'compatible_models' => ['Mustang'],
                'compatible_years' => ['2019', '2020', '2021', '2022', '2023', '2024'],
                'manufacturer' => 'Ford',
                'price' => 189.99,
                'currency' => 'USD',
                'stock_quantity' => 15,
                'in_stock' => true,
                'availability_status' => 'in_stock',
                'quality_grade' => 'OEM',
                'is_oem' => true,
                'is_certified' => true,
                'certifications' => ['ISO 9001', 'Ford Certified'],
                'weight' => 8.5,
                'dimensions' => '12" x 12" x 1"',
                'material' => 'Cast Iron',
                'color' => 'Black',
                'installation_time_minutes' => 90,
                'difficulty_level' => 'Hard',
                'installation_notes' => 'Performance rotors, requires professional installation',
                'warranty_months' => 24,
                'image_url' => 'https://picsum.photos/400/300?random=106',
                'slug' => 'ford-mustang-brake-rotors',
                'search_keywords' => 'ford mustang brake rotors performance',
                'meta_description' => 'Ford Mustang brake rotors - Performance quality',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 6,
                'supplier_name' => 'Ford Parts Direct',
                'supplier_contact' => 'parts@ford.com',
                'supplier_website' => 'https://ford.com/parts',
                'view_count' => 0,
                'purchase_count' => 0,
                'rating' => 4.9,
                'review_count' => 67,
            ],
        ];

        foreach ($parts as $partData) {
            $partData['authorized_company_id'] = $company->id;
            $partData['company_part_number'] = 'ford-parts-' . $partData['part_number'];
            $partData['slug'] = $partData['slug'] . '-' . time() . '-' . rand(1000, 9999);
            $partData['international_pricing'] = [
                'USD' => $partData['price'],
                'EUR' => round($partData['price'] * 0.85, 2),
                'GBP' => round($partData['price'] * 0.75, 2),
            ];
            $partData['is_international_shipping'] = true;
            $partData['available_countries'] = ['US', 'CA', 'DE', 'FR', 'GB', 'IT', 'ES'];
            CarPart::create($partData);
        }
    }
}