<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarImage;

class AdditionalCarImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedGolf7Images();
        $this->seedBMW3SeriesImages();
        $this->seedPorsche911Images();
    }

    private function seedGolf7Images()
    {
        $golf7Images = [
            [
                'brand' => 'Volkswagen',
                'model' => 'Golf 7',
                'year' => 2014,
                'body_type' => 'hatchback',
                'color' => 'white',
                'image_url' => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=400&h=300&fit=crop',
                'image_type' => 'exterior',
                'angle' => 'front',
                'width' => 800,
                'height' => 600,
                'source' => 'stock_photo',
                'is_primary' => true,
                'is_active' => true,
                'metadata' => ['generation' => '7', 'fuel_type' => 'diesel']
            ],
            [
                'brand' => 'Volkswagen',
                'model' => 'Golf 7',
                'year' => 2014,
                'body_type' => 'hatchback',
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
                'metadata' => ['generation' => '7', 'fuel_type' => 'diesel']
            ]
        ];

        foreach ($golf7Images as $imageData) {
            CarImage::create($imageData);
        }
    }

    private function seedBMW3SeriesImages()
    {
        $bmw3SeriesImages = [
            [
                'brand' => 'BMW',
                'model' => '3 Series',
                'year' => 2014,
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
                'metadata' => ['generation' => 'F30', 'fuel_type' => 'gasoline']
            ],
            [
                'brand' => 'BMW',
                'model' => '3 Series',
                'year' => 2014,
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
                'metadata' => ['generation' => 'F30', 'fuel_type' => 'gasoline']
            ]
        ];

        foreach ($bmw3SeriesImages as $imageData) {
            CarImage::create($imageData);
        }
    }

    private function seedPorsche911Images()
    {
        $porsche911Images = [
            [
                'brand' => 'Porsche',
                'model' => '911',
                'year' => 2014,
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
                'metadata' => ['generation' => '991', 'fuel_type' => 'gasoline']
            ],
            [
                'brand' => 'Porsche',
                'model' => '911',
                'year' => 2014,
                'body_type' => 'coupe',
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
                'metadata' => ['generation' => '991', 'fuel_type' => 'gasoline']
            ]
        ];

        foreach ($porsche911Images as $imageData) {
            CarImage::create($imageData);
        }
    }
}