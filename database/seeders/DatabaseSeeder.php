<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed car brands first (required for other data)
        $this->call(GlobalCarBrandsSeeder::class);
        
        // Seed car models (depends on brands)
        $this->call(CarModelsSeeder::class);
        
        // Seed car parts
        $this->call(CarPartsSeeder::class);
        
        // Seed authorized companies
        $this->call(AuthorizedCompaniesSeeder::class);

        // Seed mechanics from major cities worldwide
        $this->call(MechanicSeeder::class);
        
        // Seed currencies
        $this->call(CurrencySeeder::class);

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed demo data
        $this->call(CarWiseSeeder::class);
    }
}
