<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarImage;

class CarImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedPopularCars();
        $this->seedLuxuryCars();
        $this->seedElectricCars();
    }

    private function seedPopularCars()
    {
        $popularCars = [
            // Volkswagen Golf
            [
                'brand' => 'Volkswagen',
                'model' => 'Golf',
                'year' => 2023,
                'body_type' => 'hatchback',
                'color' => 'white',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&h=300&fit=crop',
                'image_type' => 'exterior',
                'angle' => 'front',
                'width' => 800,
                'height' => 600,
                'source' => 'stock_photo',
                'is_primary' => true,
                'is_active' => true,
                'metadata' => ['generation' => '8', 'fuel_type' => 'gasoline']
            ],
            [
                'brand' => 'Volkswagen',
                'model' => 'Golf',
                'year' => 2023,
                'body_type' => 'hatchback',
                'color' => 'blue',
                'image_url' => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=400&h=300&fit=crop',
                'image_type' => 'exterior',
                'angle' => 'front',
                'width' => 800,
                'height' => 600,
                'source' => 'stock_photo',
                'is_primary' => true,
                'is_active' => true,
                'metadata' => ['generation' => '8', 'fuel_type' => 'gasoline']
            ],

            // BMW 3 Series
            [
                'brand' => 'BMW',
                'model' => '3 Series',
                'year' => 2023,
                'body_type' => 'sedan',
                'color' => 'black',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&h=300&fit=crop',
                'image_type' => 'exterior',
                'angle' => 'front',
                'width' => 800,
                'height' => 600,
                'source' => 'stock_photo',
                'is_primary' => true,
                'is_active' => true,
                'metadata' => ['generation' => 'G20', 'fuel_type' => 'gasoline']
            ],

            // Mercedes-Benz C-Class
            [
                'brand' => 'Mercedes-Benz',
                'model' => 'C-Class',
                'year' => 2023,
                'body_type' => 'sedan',
                'color' => 'silver',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&h=300&fit=crop',
                'image_type' => 'exterior',
                'angle' => 'front',
                'width' => 800,
                'height' => 600,
                'source' => 'stock_photo',
                'is_primary' => true,
                'is_active' => true,
                'metadata' => ['generation' => 'W206', 'fuel_type' => 'gasoline']
            ],

            // Audi A4
            [
                'brand' => 'Audi',
                'model' => 'A4',
                'year' => 2023,
                'body_type' => 'sedan',
                'color' => 'gray',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&h=300&fit=crop',
                'image_type' => 'exterior',
                'angle' => 'front',
                'width' => 800,
                'height' => 600,
                'source' => 'stock_photo',
                'is_primary' => true,
                'is_active' => true,
                'metadata' => ['generation' => 'B9', 'fuel_type' => 'gasoline']
            ],

            // Toyota Camry
            [
                'brand' => 'Toyota',
                'model' => 'Camry',
                'year' => 2023,
                'body_type' => 'sedan',
                'color' => 'red',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&h=300&fit=crop',
                'image_type' => 'exterior',
                'angle' => 'front',
                'width' => 800,
                'height' => 600,
                'source' => 'stock_photo',
                'is_primary' => true,
                'is_active' => true,
                'metadata' => ['generation' => 'XV70', 'fuel_type' => 'hybrid']
            ],

            // Honda Civic
            [
                'brand' => 'Honda',
                'model' => 'Civic',
                'year' => 2023,
                'body_type' => 'sedan',
                'color' => 'white',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&h=300&fit=crop',
                'image_type' => 'exterior',
                'angle' => 'front',
                'width' => 800,
                'height' => 600,
                'source' => 'stock_photo',
                'is_primary' => true,
                'is_active' => true,
                'metadata' => ['generation' => '11', 'fuel_type' => 'gasoline']
            ]
        ];

        foreach ($popularCars as $imageData) {
            CarImage::create($imageData);
        }
    }

    private function seedLuxuryCars()
    {
        $luxuryCars = [
            // Porsche 911
            [
                'brand' => 'Porsche',
                'model' => '911',
                'year' => 2023,
                'body_type' => 'coupe',
                'color' => 'red',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&h=300&fit=crop',
                'image_type' => 'exterior',
                'angle' => 'front',
                'width' => 800,
                'height' => 600,
                'source' => 'stock_photo',
                'is_primary' => true,
                'is_active' => true,
                'metadata' => ['generation' => '992', 'fuel_type' => 'gasoline']
            ],

            // Ferrari 488 GTB
            [
                'brand' => 'Ferrari',
                'model' => '488 GTB',
                'year' => 2023,
                'body_type' => 'coupe',
                'color' => 'red',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&h=300&fit=crop',
                'image_type' => 'exterior',
                'angle' => 'front',
                'width' => 800,
                'height' => 600,
                'source' => 'stock_photo',
                'is_primary' => true,
                'is_active' => true,
                'metadata' => ['generation' => '1', 'fuel_type' => 'gasoline']
            ],

            // Lamborghini Huracán
            [
                'brand' => 'Lamborghini',
                'model' => 'Huracán',
                'year' => 2023,
                'body_type' => 'coupe',
                'color' => 'yellow',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&h=300&fit=crop',
                'image_type' => 'exterior',
                'angle' => 'front',
                'width' => 800,
                'height' => 600,
                'source' => 'stock_photo',
                'is_primary' => true,
                'is_active' => true,
                'metadata' => ['generation' => '1', 'fuel_type' => 'gasoline']
            ]
        ];

        foreach ($luxuryCars as $imageData) {
            CarImage::create($imageData);
        }
    }

    private function seedElectricCars()
    {
        $electricCars = [
            // Tesla Model 3
            [
                'brand' => 'Tesla',
                'model' => 'Model 3',
                'year' => 2023,
                'body_type' => 'sedan',
                'color' => 'white',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&h=300&fit=crop',
                'image_type' => 'exterior',
                'angle' => 'front',
                'width' => 800,
                'height' => 600,
                'source' => 'stock_photo',
                'is_primary' => true,
                'is_active' => true,
                'metadata' => ['generation' => '1', 'fuel_type' => 'electric']
            ],

            // Tesla Model Y
            [
                'brand' => 'Tesla',
                'model' => 'Model Y',
                'year' => 2023,
                'body_type' => 'suv',
                'color' => 'blue',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&h=300&fit=crop',
                'image_type' => 'exterior',
                'angle' => 'front',
                'width' => 800,
                'height' => 600,
                'source' => 'stock_photo',
                'is_primary' => true,
                'is_active' => true,
                'metadata' => ['generation' => '1', 'fuel_type' => 'electric']
            ]
        ];

        foreach ($electricCars as $imageData) {
            CarImage::create($imageData);
        }
    }
}