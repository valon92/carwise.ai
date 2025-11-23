<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mechanic;
use App\Models\User;

class MoreMechanicsSeeder extends Seeder
{
    public function run(): void
    {
        $mechanics = [
            // Europe - Additional mechanics
            [
                'name' => 'London Premium Auto',
                'phone' => '+44 20 7234 5678',
                'email' => 'info@londonpremiumauto.co.uk',
                'address' => '456 King\'s Road',
                'city' => 'London',
                'country' => 'United Kingdom',
                'lat' => 51.5074,
                'lng' => -0.1278,
                'services' => ['Engine Repair', 'Transmission', 'Brakes', 'Diagnostics', 'AC Service'],
                'expertise' => ['Engine', 'Transmission', 'Diagnostics', 'AC Systems'],
                'experience_years' => 25,
                'hourly_rate' => 50.00,
                'rating' => 4.9,
                'review_count' => 312,
                'availability' => 'available',
                'bio' => 'Premium automotive service center with 25 years of excellence in London.',
                'is_verified' => true
            ],

            [
                'name' => 'Manchester Auto Services',
                'phone' => '+44 161 234 5678',
                'email' => 'contact@manchesterautos.co.uk',
                'address' => '789 Oxford Road',
                'city' => 'Manchester',
                'country' => 'United Kingdom',
                'lat' => 53.4808,
                'lng' => -2.2426,
                'services' => ['General Repair', 'MOT Testing', 'Engine Service', 'Bodywork'],
                'expertise' => ['General Repair', 'MOT Testing', 'Engine Service'],
                'experience_years' => 18,
                'hourly_rate' => 42.00,
                'rating' => 4.7,
                'review_count' => 189,
                'availability' => 'available',
                'bio' => 'Trusted automotive service provider in Manchester for over 18 years.',
                'is_verified' => true
            ],

            [
                'name' => 'Birmingham Car Care',
                'phone' => '+44 121 345 6789',
                'email' => 'service@birminghamcarcare.co.uk',
                'address' => '321 Bull Street',
                'city' => 'Birmingham',
                'country' => 'United Kingdom',
                'lat' => 52.4862,
                'lng' => -1.8904,
                'services' => ['Engine Repair', 'Transmission', 'Suspension', 'Exhaust'],
                'expertise' => ['Engine', 'Transmission', 'Suspension'],
                'experience_years' => 20,
                'hourly_rate' => 45.00,
                'rating' => 4.8,
                'review_count' => 234,
                'availability' => 'available',
                'bio' => 'Professional automotive repair services in Birmingham city center.',
                'is_verified' => true
            ],

            [
                'name' => 'Garage de Paris',
                'phone' => '+33 1 45 67 89 01',
                'email' => 'contact@garagedeparis.fr',
                'address' => '456 Rue de Rivoli',
                'city' => 'Paris',
                'country' => 'France',
                'lat' => 48.8566,
                'lng' => 2.3522,
                'services' => ['French Cars', 'Luxury Vehicles', 'Classic Restoration', 'Performance'],
                'expertise' => ['French Cars', 'Luxury Vehicles', 'Classic Restoration'],
                'experience_years' => 30,
                'hourly_rate' => 60.00,
                'rating' => 4.9,
                'review_count' => 267,
                'availability' => 'available',
                'bio' => 'Premier garage specializing in French luxury and classic vehicles.',
                'is_verified' => true
            ],

            [
                'name' => 'Auto Service Lyon',
                'phone' => '+33 4 78 12 34 56',
                'email' => 'info@autoservicelyon.fr',
                'address' => '789 Rue de la République',
                'city' => 'Lyon',
                'country' => 'France',
                'lat' => 45.7640,
                'lng' => 4.8357,
                'services' => ['General Repair', 'Engine Service', 'Transmission', 'Brakes'],
                'expertise' => ['General Repair', 'Engine Service', 'Transmission'],
                'experience_years' => 22,
                'hourly_rate' => 48.00,
                'rating' => 4.6,
                'review_count' => 156,
                'availability' => 'available',
                'bio' => 'Reliable automotive service in the heart of Lyon.',
                'is_verified' => true
            ],

            [
                'name' => 'Berlin Auto Werkstatt',
                'phone' => '+49 30 9876 5432',
                'email' => 'service@berlinautowerkstatt.de',
                'address' => '123 Friedrichstraße',
                'city' => 'Berlin',
                'country' => 'Germany',
                'lat' => 52.5200,
                'lng' => 13.4050,
                'services' => ['German Cars', 'Luxury Vehicles', 'Performance Tuning', 'Classic Cars'],
                'expertise' => ['German Cars', 'Luxury Vehicles', 'Performance Tuning'],
                'experience_years' => 28,
                'hourly_rate' => 65.00,
                'rating' => 4.8,
                'review_count' => 298,
                'availability' => 'available',
                'bio' => 'Expert service for German luxury and performance vehicles.',
                'is_verified' => true
            ],

            [
                'name' => 'München Auto Service',
                'phone' => '+49 89 1234 5678',
                'email' => 'info@muenchenautoservice.de',
                'address' => '456 Marienplatz',
                'city' => 'Munich',
                'country' => 'Germany',
                'lat' => 48.1351,
                'lng' => 11.5820,
                'services' => ['BMW Service', 'Mercedes Service', 'Audi Service', 'General Repair'],
                'expertise' => ['BMW', 'Mercedes', 'Audi', 'General Repair'],
                'experience_years' => 24,
                'hourly_rate' => 58.00,
                'rating' => 4.7,
                'review_count' => 187,
                'availability' => 'available',
                'bio' => 'Specialized service for premium German vehicles in Munich.',
                'is_verified' => true
            ],

            [
                'name' => 'Auto Roma Service',
                'phone' => '+39 06 1234 5678',
                'email' => 'info@autoromaservice.it',
                'address' => '789 Via del Corso',
                'city' => 'Rome',
                'country' => 'Italy',
                'lat' => 41.9028,
                'lng' => 12.4964,
                'services' => ['Italian Cars', 'Luxury Vehicles', 'Classic Restoration', 'Performance'],
                'expertise' => ['Italian Cars', 'Luxury Vehicles', 'Classic Restoration'],
                'experience_years' => 26,
                'hourly_rate' => 55.00,
                'rating' => 4.8,
                'review_count' => 245,
                'availability' => 'available',
                'bio' => 'Premier automotive service for Italian luxury vehicles in Rome.',
                'is_verified' => true
            ],

            [
                'name' => 'Taller Madrid Auto',
                'phone' => '+34 91 123 45 67',
                'email' => 'contacto@tallermadridauto.es',
                'address' => '321 Gran Vía',
                'city' => 'Madrid',
                'country' => 'Spain',
                'lat' => 40.4168,
                'lng' => -3.7038,
                'services' => ['General Repair', 'Engine Service', 'Transmission', 'Bodywork'],
                'expertise' => ['General Repair', 'Engine Service', 'Transmission'],
                'experience_years' => 19,
                'hourly_rate' => 35.00,
                'rating' => 4.6,
                'review_count' => 178,
                'availability' => 'available',
                'bio' => 'Professional automotive repair services in Madrid city center.',
                'is_verified' => true
            ],

            [
                'name' => 'Amsterdam Auto Service',
                'phone' => '+31 20 123 4567',
                'email' => 'info@amsterdamautoservice.nl',
                'address' => '654 Damrak',
                'city' => 'Amsterdam',
                'country' => 'Netherlands',
                'lat' => 52.3676,
                'lng' => 4.9041,
                'services' => ['General Repair', 'Engine Service', 'Transmission', 'Brakes'],
                'expertise' => ['General Repair', 'Engine Service', 'Transmission'],
                'experience_years' => 21,
                'hourly_rate' => 52.00,
                'rating' => 4.7,
                'review_count' => 203,
                'availability' => 'available',
                'bio' => 'Reliable automotive service in the heart of Amsterdam.',
                'is_verified' => true
            ],

            [
                'name' => 'Stockholm Bil Service',
                'phone' => '+46 8 123 456 78',
                'email' => 'info@stockholmbilservice.se',
                'address' => '987 Drottninggatan',
                'city' => 'Stockholm',
                'country' => 'Sweden',
                'lat' => 59.3293,
                'lng' => 18.0686,
                'services' => ['Swedish Cars', 'General Repair', 'Engine Service', 'Winter Prep'],
                'expertise' => ['Swedish Cars', 'General Repair', 'Winter Systems'],
                'experience_years' => 23,
                'hourly_rate' => 48.00,
                'rating' => 4.8,
                'review_count' => 189,
                'availability' => 'available',
                'bio' => 'Expert service for Swedish vehicles with winter expertise.',
                'is_verified' => true
            ],

            // North America
            [
                'name' => 'LA Auto Service Center',
                'phone' => '+1 323 555 0123',
                'email' => 'info@laautoservice.com',
                'address' => '123 Sunset Boulevard',
                'city' => 'Los Angeles',
                'country' => 'United States',
                'lat' => 34.0522,
                'lng' => -118.2437,
                'services' => ['Luxury Cars', 'Performance Tuning', 'Exotic Vehicles', 'General Repair'],
                'expertise' => ['Luxury Cars', 'Performance Tuning', 'Exotic Vehicles'],
                'experience_years' => 16,
                'hourly_rate' => 70.00,
                'rating' => 4.8,
                'review_count' => 267,
                'availability' => 'available',
                'bio' => 'Premium automotive service for luxury and exotic vehicles in LA.',
                'is_verified' => true
            ],

            [
                'name' => 'Chicago Auto Repair',
                'phone' => '+1 312 555 0456',
                'email' => 'service@chicagoautorepair.com',
                'address' => '456 Michigan Avenue',
                'city' => 'Chicago',
                'country' => 'United States',
                'lat' => 41.8781,
                'lng' => -87.6298,
                'services' => ['General Repair', 'Engine Service', 'Transmission', 'Brakes'],
                'expertise' => ['General Repair', 'Engine Service', 'Transmission'],
                'experience_years' => 20,
                'hourly_rate' => 55.00,
                'rating' => 4.7,
                'review_count' => 198,
                'availability' => 'available',
                'bio' => 'Trusted automotive repair services in downtown Chicago.',
                'is_verified' => true
            ],

            [
                'name' => 'Miami Auto Care',
                'phone' => '+1 305 555 0789',
                'email' => 'info@miamiautocare.com',
                'address' => '789 Ocean Drive',
                'city' => 'Miami',
                'country' => 'United States',
                'lat' => 25.7617,
                'lng' => -80.1918,
                'services' => ['Luxury Cars', 'Exotic Vehicles', 'Performance Tuning', 'AC Service'],
                'expertise' => ['Luxury Cars', 'Exotic Vehicles', 'Performance Tuning'],
                'experience_years' => 14,
                'hourly_rate' => 65.00,
                'rating' => 4.9,
                'review_count' => 234,
                'availability' => 'available',
                'bio' => 'Premium service for luxury and exotic vehicles in Miami.',
                'is_verified' => true
            ],

            [
                'name' => 'Vancouver Auto Service',
                'phone' => '+1 604 555 0123',
                'email' => 'info@vancouverautoservice.ca',
                'address' => '321 Robson Street',
                'city' => 'Vancouver',
                'country' => 'Canada',
                'lat' => 49.2827,
                'lng' => -123.1207,
                'services' => ['General Repair', 'Engine Service', 'Transmission', 'Winter Prep'],
                'expertise' => ['General Repair', 'Engine Service', 'Winter Systems'],
                'experience_years' => 18,
                'hourly_rate' => 48.00,
                'rating' => 4.6,
                'review_count' => 167,
                'availability' => 'available',
                'bio' => 'Professional automotive service with winter expertise in Vancouver.',
                'is_verified' => true
            ],

            [
                'name' => 'Montreal Auto Care',
                'phone' => '+1 514 555 0456',
                'email' => 'service@montrealautocare.ca',
                'address' => '654 Saint-Catherine Street',
                'city' => 'Montreal',
                'country' => 'Canada',
                'lat' => 45.5017,
                'lng' => -73.5673,
                'services' => ['General Repair', 'Engine Service', 'Transmission', 'Brakes'],
                'expertise' => ['General Repair', 'Engine Service', 'Transmission'],
                'experience_years' => 22,
                'hourly_rate' => 45.00,
                'rating' => 4.7,
                'review_count' => 189,
                'availability' => 'available',
                'bio' => 'Bilingual automotive service in Montreal city center.',
                'is_verified' => true
            ],

            // Asia
            [
                'name' => 'Shanghai Auto Service',
                'phone' => '+86 21 1234 5678',
                'email' => 'info@shanghaiautoservice.cn',
                'address' => '123 Nanjing Road',
                'city' => 'Shanghai',
                'country' => 'China',
                'lat' => 31.2304,
                'lng' => 121.4737,
                'services' => ['Chinese Cars', 'General Repair', 'Engine Service', 'Transmission'],
                'expertise' => ['Chinese Cars', 'General Repair', 'Engine Service'],
                'experience_years' => 15,
                'hourly_rate' => 30.00,
                'rating' => 4.5,
                'review_count' => 145,
                'availability' => 'available',
                'bio' => 'Professional automotive service for Chinese and imported vehicles.',
                'is_verified' => true
            ],

            [
                'name' => 'Seoul Auto Care',
                'phone' => '+82 2 1234 5678',
                'email' => 'info@seoulautocare.kr',
                'address' => '456 Gangnam-gu',
                'city' => 'Seoul',
                'country' => 'South Korea',
                'lat' => 37.5665,
                'lng' => 126.9780,
                'services' => ['Korean Cars', 'Hybrid Systems', 'General Repair', 'Performance'],
                'expertise' => ['Korean Cars', 'Hybrid Systems', 'General Repair'],
                'experience_years' => 17,
                'hourly_rate' => 35.00,
                'rating' => 4.6,
                'review_count' => 178,
                'availability' => 'available',
                'bio' => 'Expert service for Korean vehicles including Hyundai and Kia.',
                'is_verified' => true
            ],

            [
                'name' => 'Singapore Auto Service',
                'phone' => '+65 6123 4567',
                'email' => 'info@singaporeautoservice.sg',
                'address' => '789 Orchard Road',
                'city' => 'Singapore',
                'country' => 'Singapore',
                'lat' => 1.3521,
                'lng' => 103.8198,
                'services' => ['Luxury Cars', 'General Repair', 'Engine Service', 'Performance'],
                'expertise' => ['Luxury Cars', 'General Repair', 'Engine Service'],
                'experience_years' => 19,
                'hourly_rate' => 55.00,
                'rating' => 4.8,
                'review_count' => 203,
                'availability' => 'available',
                'bio' => 'Premium automotive service in Singapore city center.',
                'is_verified' => true
            ],

            [
                'name' => 'Bangkok Auto Care',
                'phone' => '+66 2 123 4567',
                'email' => 'info@bangkokautocare.th',
                'address' => '321 Sukhumvit Road',
                'city' => 'Bangkok',
                'country' => 'Thailand',
                'lat' => 13.7563,
                'lng' => 100.5018,
                'services' => ['General Repair', 'Engine Service', 'Transmission', 'AC Service'],
                'expertise' => ['General Repair', 'Engine Service', 'AC Systems'],
                'experience_years' => 16,
                'hourly_rate' => 25.00,
                'rating' => 4.5,
                'review_count' => 134,
                'availability' => 'available',
                'bio' => 'Reliable automotive service in Bangkok with AC expertise.',
                'is_verified' => true
            ],

            [
                'name' => 'KL Auto Service',
                'phone' => '+60 3 1234 5678',
                'email' => 'info@klautoservice.my',
                'address' => '654 Jalan Bukit Bintang',
                'city' => 'Kuala Lumpur',
                'country' => 'Malaysia',
                'lat' => 3.1390,
                'lng' => 101.6869,
                'services' => ['General Repair', 'Engine Service', 'Transmission', 'Brakes'],
                'expertise' => ['General Repair', 'Engine Service', 'Transmission'],
                'experience_years' => 18,
                'hourly_rate' => 28.00,
                'rating' => 4.6,
                'review_count' => 156,
                'availability' => 'available',
                'bio' => 'Professional automotive service in Kuala Lumpur city center.',
                'is_verified' => true
            ],

            // Africa
            [
                'name' => 'Johannesburg Auto Care',
                'phone' => '+27 11 123 4567',
                'email' => 'info@joburgautocare.co.za',
                'address' => '321 Sandton City',
                'city' => 'Johannesburg',
                'country' => 'South Africa',
                'lat' => -26.2041,
                'lng' => 28.0473,
                'services' => ['General Repair', 'Engine Service', 'Transmission', '4WD Service'],
                'expertise' => ['General Repair', 'Engine Service', '4WD Systems'],
                'experience_years' => 20,
                'hourly_rate' => 22.00,
                'rating' => 4.7,
                'review_count' => 189,
                'availability' => 'available',
                'bio' => 'Expert automotive service with 4WD expertise in Johannesburg.',
                'is_verified' => true
            ],

            [
                'name' => 'Cairo Auto Service',
                'phone' => '+20 2 1234 5678',
                'email' => 'info@cairoautoservice.eg',
                'address' => '654 Tahrir Square',
                'city' => 'Cairo',
                'country' => 'Egypt',
                'lat' => 30.0444,
                'lng' => 31.2357,
                'services' => ['General Repair', 'Engine Service', 'Transmission', 'AC Service'],
                'expertise' => ['General Repair', 'Engine Service', 'AC Systems'],
                'experience_years' => 17,
                'hourly_rate' => 18.00,
                'rating' => 4.5,
                'review_count' => 123,
                'availability' => 'available',
                'bio' => 'Reliable automotive service in Cairo with AC expertise.',
                'is_verified' => true
            ],

            [
                'name' => 'Lagos Auto Care',
                'phone' => '+234 1 234 5678',
                'email' => 'info@lagosautocare.ng',
                'address' => '987 Victoria Island',
                'city' => 'Lagos',
                'country' => 'Nigeria',
                'lat' => 6.5244,
                'lng' => 3.3792,
                'services' => ['General Repair', 'Engine Service', 'Transmission', 'Brakes'],
                'expertise' => ['General Repair', 'Engine Service', 'Transmission'],
                'experience_years' => 15,
                'hourly_rate' => 15.00,
                'rating' => 4.4,
                'review_count' => 98,
                'availability' => 'available',
                'bio' => 'Professional automotive service in Lagos city center.',
                'is_verified' => true
            ],

            // South America
            [
                'name' => 'Buenos Aires Auto Service',
                'phone' => '+54 11 1234 5678',
                'email' => 'info@baautoservice.com.ar',
                'address' => '321 Avenida Corrientes',
                'city' => 'Buenos Aires',
                'country' => 'Argentina',
                'lat' => -34.6118,
                'lng' => -58.3960,
                'services' => ['General Repair', 'Engine Service', 'Transmission', 'Brakes'],
                'expertise' => ['General Repair', 'Engine Service', 'Transmission'],
                'experience_years' => 21,
                'hourly_rate' => 20.00,
                'rating' => 4.6,
                'review_count' => 167,
                'availability' => 'available',
                'bio' => 'Trusted automotive service in Buenos Aires city center.',
                'is_verified' => true
            ],

            [
                'name' => 'Santiago Auto Care',
                'phone' => '+56 2 1234 5678',
                'email' => 'info@santiagoautocare.cl',
                'address' => '654 Avenida Libertador',
                'city' => 'Santiago',
                'country' => 'Chile',
                'lat' => -33.4489,
                'lng' => -70.6693,
                'services' => ['General Repair', 'Engine Service', 'Transmission', 'Brakes'],
                'expertise' => ['General Repair', 'Engine Service', 'Transmission'],
                'experience_years' => 19,
                'hourly_rate' => 22.00,
                'rating' => 4.7,
                'review_count' => 145,
                'availability' => 'available',
                'bio' => 'Professional automotive service in Santiago city center.',
                'is_verified' => true
            ],

            [
                'name' => 'Lima Auto Service',
                'phone' => '+51 1 234 5678',
                'email' => 'info@limaautoservice.pe',
                'address' => '987 Avenida Javier Prado',
                'city' => 'Lima',
                'country' => 'Peru',
                'lat' => -12.0464,
                'lng' => -77.0428,
                'services' => ['General Repair', 'Engine Service', 'Transmission', 'AC Service'],
                'expertise' => ['General Repair', 'Engine Service', 'AC Systems'],
                'experience_years' => 16,
                'hourly_rate' => 18.00,
                'rating' => 4.5,
                'review_count' => 112,
                'availability' => 'available',
                'bio' => 'Reliable automotive service in Lima with AC expertise.',
                'is_verified' => true
            ],

            // Oceania
            [
                'name' => 'Melbourne Auto Care',
                'phone' => '+61 3 9876 5432',
                'email' => 'info@melbourneautocare.com.au',
                'address' => '321 Collins Street',
                'city' => 'Melbourne',
                'country' => 'Australia',
                'lat' => -37.8136,
                'lng' => 144.9631,
                'services' => ['General Repair', '4WD Service', 'Performance Tuning', 'Import Services'],
                'expertise' => ['General Repair', '4WD Systems', 'Performance Tuning'],
                'experience_years' => 17,
                'hourly_rate' => 42.00,
                'rating' => 4.7,
                'review_count' => 189,
                'availability' => 'available',
                'bio' => 'Expert automotive service with 4WD expertise in Melbourne.',
                'is_verified' => true
            ],

            [
                'name' => 'Auckland Auto Service',
                'phone' => '+64 9 123 4567',
                'email' => 'info@aucklandautoservice.nz',
                'address' => '654 Queen Street',
                'city' => 'Auckland',
                'country' => 'New Zealand',
                'lat' => -36.8485,
                'lng' => 174.7633,
                'services' => ['General Repair', 'Engine Service', 'Transmission', '4WD Service'],
                'expertise' => ['General Repair', 'Engine Service', '4WD Systems'],
                'experience_years' => 20,
                'hourly_rate' => 38.00,
                'rating' => 4.6,
                'review_count' => 156,
                'availability' => 'available',
                'bio' => 'Professional automotive service with 4WD expertise in Auckland.',
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
            $mechanic->location = $mechanicData['city'] . ', ' . $mechanicData['country'];
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
