<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlobalCarBrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedCarBrands();
    }

    private function seedCarBrands(): void
    {
        $brands = [
            // German Brands
            ['name' => 'BMW', 'country' => 'Germany', 'logo' => 'bmw.png', 'founded_year' => 1916, 'is_popular' => true],
            ['name' => 'Mercedes-Benz', 'country' => 'Germany', 'logo' => 'mercedes.png', 'founded_year' => 1926, 'is_popular' => true],
            ['name' => 'Audi', 'country' => 'Germany', 'logo' => 'audi.png', 'founded_year' => 1909, 'is_popular' => true],
            ['name' => 'Volkswagen', 'country' => 'Germany', 'logo' => 'volkswagen.png', 'founded_year' => 1937, 'is_popular' => true],
            ['name' => 'Porsche', 'country' => 'Germany', 'logo' => 'porsche.png', 'founded_year' => 1931, 'is_popular' => true],
            ['name' => 'Opel', 'country' => 'Germany', 'logo' => 'opel.png', 'founded_year' => 1862, 'is_popular' => false],

            // Japanese Brands
            ['name' => 'Toyota', 'country' => 'Japan', 'logo' => 'toyota.png', 'founded_year' => 1937, 'is_popular' => true],
            ['name' => 'Honda', 'country' => 'Japan', 'logo' => 'honda.png', 'founded_year' => 1948, 'is_popular' => true],
            ['name' => 'Nissan', 'country' => 'Japan', 'logo' => 'nissan.png', 'founded_year' => 1933, 'is_popular' => true],
            ['name' => 'Mazda', 'country' => 'Japan', 'logo' => 'mazda.png', 'founded_year' => 1920, 'is_popular' => true],
            ['name' => 'Subaru', 'country' => 'Japan', 'logo' => 'subaru.png', 'founded_year' => 1953, 'is_popular' => true],
            ['name' => 'Mitsubishi', 'country' => 'Japan', 'logo' => 'mitsubishi.png', 'founded_year' => 1870, 'is_popular' => false],
            ['name' => 'Lexus', 'country' => 'Japan', 'logo' => 'lexus.png', 'founded_year' => 1989, 'is_popular' => true],
            ['name' => 'Infiniti', 'country' => 'Japan', 'logo' => 'infiniti.png', 'founded_year' => 1989, 'is_popular' => false],
            ['name' => 'Acura', 'country' => 'Japan', 'logo' => 'acura.png', 'founded_year' => 1986, 'is_popular' => false],

            // American Brands
            ['name' => 'Ford', 'country' => 'USA', 'logo' => 'ford.png', 'founded_year' => 1903, 'is_popular' => true],
            ['name' => 'Chevrolet', 'country' => 'USA', 'logo' => 'chevrolet.png', 'founded_year' => 1911, 'is_popular' => true],
            ['name' => 'Chrysler', 'country' => 'USA', 'logo' => 'chrysler.png', 'founded_year' => 1925, 'is_popular' => false],
            ['name' => 'Dodge', 'country' => 'USA', 'logo' => 'dodge.png', 'founded_year' => 1900, 'is_popular' => true],
            ['name' => 'Jeep', 'country' => 'USA', 'logo' => 'jeep.png', 'founded_year' => 1941, 'is_popular' => true],
            ['name' => 'Cadillac', 'country' => 'USA', 'logo' => 'cadillac.png', 'founded_year' => 1902, 'is_popular' => false],
            ['name' => 'GMC', 'country' => 'USA', 'logo' => 'gmc.png', 'founded_year' => 1911, 'is_popular' => false],
            ['name' => 'Lincoln', 'country' => 'USA', 'logo' => 'lincoln.png', 'founded_year' => 1917, 'is_popular' => false],
            ['name' => 'Tesla', 'country' => 'USA', 'logo' => 'tesla.png', 'founded_year' => 2003, 'is_popular' => true],

            // Italian Brands
            ['name' => 'Ferrari', 'country' => 'Italy', 'logo' => 'ferrari.png', 'founded_year' => 1947, 'is_popular' => true],
            ['name' => 'Lamborghini', 'country' => 'Italy', 'logo' => 'lamborghini.png', 'founded_year' => 1963, 'is_popular' => true],
            ['name' => 'Fiat', 'country' => 'Italy', 'logo' => 'fiat.png', 'founded_year' => 1899, 'is_popular' => true],
            ['name' => 'Alfa Romeo', 'country' => 'Italy', 'logo' => 'alfa_romeo.png', 'founded_year' => 1910, 'is_popular' => false],
            ['name' => 'Maserati', 'country' => 'Italy', 'logo' => 'maserati.png', 'founded_year' => 1914, 'is_popular' => false],

            // British Brands
            ['name' => 'Aston Martin', 'country' => 'UK', 'logo' => 'aston_martin.png', 'founded_year' => 1913, 'is_popular' => false],
            ['name' => 'Bentley', 'country' => 'UK', 'logo' => 'bentley.png', 'founded_year' => 1919, 'is_popular' => false],
            ['name' => 'Jaguar', 'country' => 'UK', 'logo' => 'jaguar.png', 'founded_year' => 1922, 'is_popular' => true],
            ['name' => 'Land Rover', 'country' => 'UK', 'logo' => 'land_rover.png', 'founded_year' => 1948, 'is_popular' => true],
            ['name' => 'Rolls-Royce', 'country' => 'UK', 'logo' => 'rolls_royce.png', 'founded_year' => 1904, 'is_popular' => false],
            ['name' => 'McLaren', 'country' => 'UK', 'logo' => 'mclaren.png', 'founded_year' => 1963, 'is_popular' => false],

            // French Brands
            ['name' => 'Peugeot', 'country' => 'France', 'logo' => 'peugeot.png', 'founded_year' => 1882, 'is_popular' => true],
            ['name' => 'Citroën', 'country' => 'France', 'logo' => 'citroen.png', 'founded_year' => 1919, 'is_popular' => true],
            ['name' => 'Renault', 'country' => 'France', 'logo' => 'renault.png', 'founded_year' => 1898, 'is_popular' => true],
            ['name' => 'Bugatti', 'country' => 'France', 'logo' => 'bugatti.png', 'founded_year' => 1909, 'is_popular' => false],

            // Korean Brands
            ['name' => 'Hyundai', 'country' => 'South Korea', 'logo' => 'hyundai.png', 'founded_year' => 1967, 'is_popular' => true],
            ['name' => 'Kia', 'country' => 'South Korea', 'logo' => 'kia.png', 'founded_year' => 1944, 'is_popular' => true],
            ['name' => 'Genesis', 'country' => 'South Korea', 'logo' => 'genesis.png', 'founded_year' => 2015, 'is_popular' => false],

            // Swedish Brands
            ['name' => 'Volvo', 'country' => 'Sweden', 'logo' => 'volvo.png', 'founded_year' => 1927, 'is_popular' => true],
            ['name' => 'Saab', 'country' => 'Sweden', 'logo' => 'saab.png', 'founded_year' => 1945, 'is_popular' => false],

            // Chinese Brands
            ['name' => 'BYD', 'country' => 'China', 'logo' => 'byd.png', 'founded_year' => 1995, 'is_popular' => true],
            ['name' => 'Geely', 'country' => 'China', 'logo' => 'geely.png', 'founded_year' => 1986, 'is_popular' => true],
            ['name' => 'Great Wall', 'country' => 'China', 'logo' => 'great_wall.png', 'founded_year' => 1984, 'is_popular' => false],
            ['name' => 'Chery', 'country' => 'China', 'logo' => 'chery.png', 'founded_year' => 1997, 'is_popular' => false],
            ['name' => 'NIO', 'country' => 'China', 'logo' => 'nio.png', 'founded_year' => 2014, 'is_popular' => true],
            ['name' => 'Xpeng', 'country' => 'China', 'logo' => 'xpeng.png', 'founded_year' => 2014, 'is_popular' => false],
            ['name' => 'Li Auto', 'country' => 'China', 'logo' => 'li_auto.png', 'founded_year' => 2015, 'is_popular' => false],
            
            // Indian Brands
            ['name' => 'Tata', 'country' => 'India', 'logo' => 'tata.png', 'founded_year' => 1945, 'is_popular' => true],
            ['name' => 'Mahindra', 'country' => 'India', 'logo' => 'mahindra.png', 'founded_year' => 1945, 'is_popular' => false],
            ['name' => 'Maruti Suzuki', 'country' => 'India', 'logo' => 'maruti_suzuki.png', 'founded_year' => 1981, 'is_popular' => true],
            
            // Russian Brands
            ['name' => 'Lada', 'country' => 'Russia', 'logo' => 'lada.png', 'founded_year' => 1973, 'is_popular' => true],
            ['name' => 'UAZ', 'country' => 'Russia', 'logo' => 'uaz.png', 'founded_year' => 1941, 'is_popular' => false],
            
            // European Brands
            ['name' => 'Škoda', 'country' => 'Czech Republic', 'logo' => 'skoda.png', 'founded_year' => 1895, 'is_popular' => true],
            ['name' => 'SEAT', 'country' => 'Spain', 'logo' => 'seat.png', 'founded_year' => 1950, 'is_popular' => false],
            ['name' => 'Dacia', 'country' => 'Romania', 'logo' => 'dacia.png', 'founded_year' => 1966, 'is_popular' => true],
            
            // Canadian/Australian Brands
            ['name' => 'Holden', 'country' => 'Australia', 'logo' => 'holden.png', 'founded_year' => 1856, 'is_popular' => false],
            
            // Luxury/Specialty Brands
            ['name' => 'Maybach', 'country' => 'Germany', 'logo' => 'maybach.png', 'founded_year' => 1909, 'is_popular' => false],
            ['name' => 'Koenigsegg', 'country' => 'Sweden', 'logo' => 'koenigsegg.png', 'founded_year' => 1994, 'is_popular' => false],
            ['name' => 'Pagani', 'country' => 'Italy', 'logo' => 'pagani.png', 'founded_year' => 1992, 'is_popular' => false],
            
            // Electric Vehicle Brands
            ['name' => 'Rivian', 'country' => 'USA', 'logo' => 'rivian.png', 'founded_year' => 2009, 'is_popular' => true],
            ['name' => 'Lucid', 'country' => 'USA', 'logo' => 'lucid.png', 'founded_year' => 2007, 'is_popular' => false],
            ['name' => 'Polestar', 'country' => 'Sweden', 'logo' => 'polestar.png', 'founded_year' => 2017, 'is_popular' => true],
        ];

        foreach ($brands as $brand) {
            \App\Models\CarBrand::updateOrCreate(
                ['name' => $brand['name']],
                [
                    'slug' => \Illuminate\Support\Str::slug($brand['name']),
                    'country' => $brand['country'],
                    'logo_url' => 'images/brands/' . $brand['logo'],
                    'founded_year' => $brand['founded_year'],
                    'is_popular' => $brand['is_popular'],
                    'is_active' => true,
                    'sort_order' => $brand['is_popular'] ? 1 : 2,
                    'description' => $this->getDescription($brand['name'], $brand['country'])
                ]
            );
        }
    }

    private function getDescription(string $name, string $country): string
    {
        $descriptions = [
            'BMW' => 'Bavarian Motor Works - Premium German automotive manufacturer known for luxury vehicles and motorcycles.',
            'Mercedes-Benz' => 'German luxury automotive brand known for innovation, safety, and premium vehicles.',
            'Audi' => 'German luxury automotive manufacturer known for quattro all-wheel drive and advanced technology.',
            'Toyota' => 'Japanese automotive manufacturer known for reliability, fuel efficiency, and innovation.',
            'Honda' => 'Japanese automotive and motorcycle manufacturer known for quality and reliability.',
            'Ford' => 'American multinational automaker known for trucks, SUVs, and innovative automotive technology.',
            'Volkswagen' => 'German automotive manufacturer known for the Beetle and Golf, now focusing on electric vehicles.',
            'Tesla' => 'American electric vehicle and clean energy company leading the EV revolution.',
        ];

        return $descriptions[$name] ?? "{$name} is a {$country}-based automotive manufacturer.";
    }
}
