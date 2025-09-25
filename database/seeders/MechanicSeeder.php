<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mechanic;
use App\Models\User;

class MechanicSeeder extends Seeder
{
    public function run(): void
    {
        $mechanics = [
            // London, UK
            [
                'name' => 'James Thompson',
                'phone' => '+44 20 7123 4567',
                'email' => 'james@londonautocare.co.uk',
                'address' => '123 Baker Street',
                'city' => 'London',
                'country' => 'United Kingdom',
                'lat' => 51.5074,
                'lng' => -0.1278,
                'services' => ['Engine Repair', 'Transmission', 'Brakes', 'Diagnostics'],
                'expertise' => ['Engine', 'Transmission', 'Diagnostics', 'Electrical'],
                'experience_years' => 15,
                'hourly_rate' => 45.00,
                'rating' => 4.8,
                'review_count' => 127,
                'availability' => 'available',
                'bio' => 'Certified mechanic with 15 years of experience in luxury and performance vehicles.',
                'is_verified' => true
            ],
            [
                'name' => 'Sarah Mitchell',
                'phone' => '+44 20 7987 6543',
                'email' => 'sarah@premiumautoservice.co.uk',
                'address' => '456 Oxford Street',
                'city' => 'London',
                'country' => 'United Kingdom',
                'lat' => 51.5154,
                'lng' => -0.1419,
                'services' => ['Bodywork', 'Paint', 'Detailing', 'Restoration'],
                'expertise' => ['Bodywork', 'Paint', 'Restoration'],
                'experience_years' => 12,
                'hourly_rate' => 40.00,
                'rating' => 4.9,
                'review_count' => 89,
                'availability' => 'available',
                'bio' => 'Specialist in classic car restoration and premium bodywork services.',
                'is_verified' => true
            ],

            // New York, USA
            [
                'name' => 'Michael Rodriguez',
                'phone' => '+1 212 555 0123',
                'email' => 'mike@nycautoservice.com',
                'address' => '789 Broadway',
                'city' => 'New York',
                'country' => 'United States',
                'lat' => 40.7589,
                'lng' => -73.9851,
                'services' => ['Engine Repair', 'Transmission', 'Suspension', 'Exhaust'],
                'expertise' => ['Engine', 'Transmission', 'Suspension'],
                'experience_years' => 18,
                'hourly_rate' => 65.00,
                'rating' => 4.7,
                'review_count' => 203,
                'availability' => 'available',
                'bio' => 'Master technician specializing in European and American vehicles.',
                'is_verified' => true
            ],
            [
                'name' => 'Jennifer Chen',
                'phone' => '+1 212 555 0456',
                'email' => 'jennifer@electricautoservice.com',
                'address' => '321 5th Avenue',
                'city' => 'New York',
                'country' => 'United States',
                'lat' => 40.7505,
                'lng' => -73.9934,
                'services' => ['Electric Vehicle Service', 'Hybrid Repair', 'Battery Service', 'Software Updates'],
                'expertise' => ['Electric Vehicles', 'Hybrid Systems', 'Battery Technology'],
                'experience_years' => 10,
                'hourly_rate' => 75.00,
                'rating' => 4.9,
                'review_count' => 156,
                'availability' => 'available',
                'bio' => 'Certified EV technician with expertise in Tesla, BMW i-series, and other electric vehicles.',
                'is_verified' => true
            ],

            // Paris, France
            [
                'name' => 'Pierre Dubois',
                'phone' => '+33 1 42 36 78 90',
                'email' => 'pierre@autoserviceparis.fr',
                'address' => '123 Champs-Élysées',
                'city' => 'Paris',
                'country' => 'France',
                'lat' => 48.8566,
                'lng' => 2.3522,
                'services' => ['French Cars', 'Luxury Vehicles', 'Classic Cars', 'Performance Tuning'],
                'expertise' => ['French Cars', 'Luxury Vehicles', 'Performance Tuning'],
                'experience_years' => 20,
                'hourly_rate' => 55.00,
                'rating' => 4.8,
                'review_count' => 178,
                'availability' => 'available',
                'bio' => 'Expert in French luxury vehicles including Peugeot, Renault, and Citroën.',
                'is_verified' => true
            ],

            // Tokyo, Japan
            [
                'name' => 'Hiroshi Tanaka',
                'phone' => '+81 3 1234 5678',
                'email' => 'hiroshi@tokyoautoservice.jp',
                'address' => '456 Ginza Street',
                'city' => 'Tokyo',
                'country' => 'Japan',
                'lat' => 35.6762,
                'lng' => 139.6503,
                'services' => ['Japanese Cars', 'Hybrid Systems', 'Performance Tuning', 'Import Services'],
                'expertise' => ['Japanese Cars', 'Hybrid Systems', 'Performance Tuning'],
                'experience_years' => 16,
                'hourly_rate' => 50.00,
                'rating' => 4.9,
                'review_count' => 234,
                'availability' => 'available',
                'bio' => 'Specialist in Japanese vehicles including Toyota, Honda, Nissan, and Mazda.',
                'is_verified' => true
            ],

            // Berlin, Germany
            [
                'name' => 'Klaus Weber',
                'phone' => '+49 30 1234 5678',
                'email' => 'klaus@berlinautoservice.de',
                'address' => '789 Unter den Linden',
                'city' => 'Berlin',
                'country' => 'Germany',
                'lat' => 52.5200,
                'lng' => 13.4050,
                'services' => ['German Cars', 'Luxury Vehicles', 'Performance Tuning', 'Classic Restoration'],
                'expertise' => ['German Cars', 'Luxury Vehicles', 'Performance Tuning'],
                'experience_years' => 22,
                'hourly_rate' => 60.00,
                'rating' => 4.8,
                'review_count' => 189,
                'availability' => 'available',
                'bio' => 'Master technician specializing in BMW, Mercedes-Benz, Audi, and Volkswagen.',
                'is_verified' => true
            ],

            // Sydney, Australia
            [
                'name' => 'David Wilson',
                'phone' => '+61 2 9876 5432',
                'email' => 'david@sydneyautoservice.com.au',
                'address' => '321 George Street',
                'city' => 'Sydney',
                'country' => 'Australia',
                'lat' => -33.8688,
                'lng' => 151.2093,
                'services' => ['General Repair', '4WD Service', 'Performance Tuning', 'Import Services'],
                'expertise' => ['General Repair', '4WD Systems', 'Performance Tuning'],
                'experience_years' => 14,
                'hourly_rate' => 45.00,
                'rating' => 4.7,
                'review_count' => 145,
                'availability' => 'available',
                'bio' => 'Experienced mechanic specializing in Australian and imported vehicles.',
                'is_verified' => true
            ],

            // Dubai, UAE
            [
                'name' => 'Ahmed Al-Rashid',
                'phone' => '+971 4 123 4567',
                'email' => 'ahmed@dubaiautoservice.ae',
                'address' => '654 Sheikh Zayed Road',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'lat' => 25.2048,
                'lng' => 55.2708,
                'services' => ['Luxury Cars', 'Supercars', 'Performance Tuning', 'Exotic Vehicles'],
                'expertise' => ['Luxury Cars', 'Supercars', 'Performance Tuning'],
                'experience_years' => 13,
                'hourly_rate' => 80.00,
                'rating' => 4.9,
                'review_count' => 167,
                'availability' => 'available',
                'bio' => 'Specialist in luxury and exotic vehicles including Ferrari, Lamborghini, and McLaren.',
                'is_verified' => true
            ],

            // Milan, Italy
            [
                'name' => 'Marco Rossi',
                'phone' => '+39 02 1234 5678',
                'email' => 'marco@milanoautoservice.it',
                'address' => '987 Via Montenapoleone',
                'city' => 'Milan',
                'country' => 'Italy',
                'lat' => 45.4642,
                'lng' => 9.1900,
                'services' => ['Italian Cars', 'Luxury Vehicles', 'Classic Restoration', 'Performance Tuning'],
                'expertise' => ['Italian Cars', 'Luxury Vehicles', 'Classic Restoration'],
                'experience_years' => 19,
                'hourly_rate' => 65.00,
                'rating' => 4.8,
                'review_count' => 198,
                'availability' => 'available',
                'bio' => 'Expert in Italian luxury vehicles including Ferrari, Lamborghini, Maserati, and Alfa Romeo.',
                'is_verified' => true
            ],

            // Toronto, Canada
            [
                'name' => 'Robert Johnson',
                'phone' => '+1 416 555 0789',
                'email' => 'robert@torontoautoservice.ca',
                'address' => '456 Bay Street',
                'city' => 'Toronto',
                'country' => 'Canada',
                'lat' => 43.6532,
                'lng' => -79.3832,
                'services' => ['General Repair', 'Winter Preparation', 'Performance Tuning', 'Import Services'],
                'expertise' => ['General Repair', 'Winter Systems', 'Performance Tuning'],
                'experience_years' => 17,
                'hourly_rate' => 50.00,
                'rating' => 4.7,
                'review_count' => 176,
                'availability' => 'available',
                'bio' => 'Experienced mechanic specializing in Canadian and imported vehicles with winter expertise.',
                'is_verified' => true
            ],

            // São Paulo, Brazil
            [
                'name' => 'Carlos Silva',
                'phone' => '+55 11 9876 5432',
                'email' => 'carlos@saopauloautoservice.com.br',
                'address' => '789 Avenida Paulista',
                'city' => 'São Paulo',
                'country' => 'Brazil',
                'lat' => -23.5505,
                'lng' => -46.6333,
                'services' => ['Brazilian Cars', 'General Repair', 'Performance Tuning', 'Import Services'],
                'expertise' => ['Brazilian Cars', 'General Repair', 'Performance Tuning'],
                'experience_years' => 15,
                'hourly_rate' => 35.00,
                'rating' => 4.6,
                'review_count' => 134,
                'availability' => 'available',
                'bio' => 'Specialist in Brazilian vehicles including Fiat, Volkswagen, and Chevrolet.',
                'is_verified' => true
            ],

            // Mumbai, India
            [
                'name' => 'Rajesh Patel',
                'phone' => '+91 22 1234 5678',
                'email' => 'rajesh@mumbaiautoservice.in',
                'address' => '321 Marine Drive',
                'city' => 'Mumbai',
                'country' => 'India',
                'lat' => 19.0760,
                'lng' => 72.8777,
                'services' => ['Indian Cars', 'General Repair', 'Performance Tuning', 'Import Services'],
                'expertise' => ['Indian Cars', 'General Repair', 'Performance Tuning'],
                'experience_years' => 12,
                'hourly_rate' => 25.00,
                'rating' => 4.5,
                'review_count' => 98,
                'availability' => 'available',
                'bio' => 'Expert in Indian vehicles including Maruti, Tata, and Mahindra.',
                'is_verified' => true
            ]
        ];

        foreach ($mechanics as $mechanicData) {
            // Create user first
            $user = User::create([
                'name' => $mechanicData['name'],
                'first_name' => explode(' ', $mechanicData['name'])[0],
                'last_name' => explode(' ', $mechanicData['name'], 2)[1] ?? '',
                'email' => $mechanicData['email'],
                'password' => bcrypt('password123'),
                'phone' => $mechanicData['phone'],
                'role' => 'mechanic',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // Create mechanic profile
            $mechanic = new Mechanic();
            $mechanic->user_id = $user->id;
            $mechanic->name = $mechanicData['name'];
            $mechanic->phone = $mechanicData['phone'];
            $mechanic->email = $mechanicData['email'];
            $mechanic->address = $mechanicData['address'];
            $mechanic->city = $mechanicData['city'];
            $mechanic->country = $mechanicData['country'];
            $mechanic->lat = $mechanicData['lat'];
            $mechanic->lng = $mechanicData['lng'];
            $mechanic->services = json_encode($mechanicData['services']);
            $mechanic->expertise = json_encode($mechanicData['expertise']);
            $mechanic->experience_years = $mechanicData['experience_years'];
            $mechanic->hourly_rate = $mechanicData['hourly_rate'];
            $mechanic->rating = $mechanicData['rating'];
            $mechanic->review_count = $mechanicData['review_count'];
            $mechanic->availability = $mechanicData['availability'];
            $mechanic->bio = $mechanicData['bio'];
            $mechanic->is_verified = $mechanicData['is_verified'];
            $mechanic->geohash = base64_encode(pack('dd', $mechanicData['lat'], $mechanicData['lng']));
            $mechanic->save();
        }
    }
}
