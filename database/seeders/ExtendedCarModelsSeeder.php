<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarBrand;
use App\Models\CarModel;

class ExtendedCarModelsSeeder extends Seeder
{
    public function run()
    {
        $brands = CarBrand::all()->keyBy('name');
        
        $models = [
            // Porsche Models
            'Porsche' => [
                ['name' => '911', 'generation' => '992', 'start_year' => 2019, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Sports Car', 'fuel_types' => ['Gasoline', 'Hybrid']],
                ['name' => '911', 'generation' => '991.2', 'start_year' => 2016, 'end_year' => 2019, 'body_type' => 'Coupe', 'segment' => 'Sports Car', 'fuel_types' => ['Gasoline']],
                ['name' => 'Cayenne', 'generation' => '3G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Luxury SUV', 'fuel_types' => ['Gasoline', 'Hybrid', 'Electric']],
                ['name' => 'Macan', 'generation' => '2G', 'start_year' => 2021, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Compact SUV', 'fuel_types' => ['Gasoline', 'Electric']],
                ['name' => 'Panamera', 'generation' => '2G', 'start_year' => 2016, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Luxury Sedan', 'fuel_types' => ['Gasoline', 'Hybrid', 'Electric']],
                ['name' => 'Taycan', 'generation' => '1G', 'start_year' => 2019, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Electric Sedan', 'fuel_types' => ['Electric']],
                ['name' => 'Boxster', 'generation' => '982', 'start_year' => 2016, 'end_year' => null, 'body_type' => 'Convertible', 'segment' => 'Sports Car', 'fuel_types' => ['Gasoline']],
                ['name' => 'Cayman', 'generation' => '982', 'start_year' => 2016, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Sports Car', 'fuel_types' => ['Gasoline']],
            ],
            
            // Ferrari Models
            'Ferrari' => [
                ['name' => '488 GTB', 'generation' => '1G', 'start_year' => 2015, 'end_year' => 2019, 'body_type' => 'Coupe', 'segment' => 'Supercar', 'fuel_types' => ['Gasoline']],
                ['name' => 'F8 Tributo', 'generation' => '1G', 'start_year' => 2019, 'end_year' => 2023, 'body_type' => 'Coupe', 'segment' => 'Supercar', 'fuel_types' => ['Gasoline']],
                ['name' => 'SF90 Stradale', 'generation' => '1G', 'start_year' => 2019, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Supercar', 'fuel_types' => ['Hybrid']],
                ['name' => 'Roma', 'generation' => '1G', 'start_year' => 2019, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Grand Tourer', 'fuel_types' => ['Gasoline']],
                ['name' => 'Portofino', 'generation' => '1G', 'start_year' => 2017, 'end_year' => null, 'body_type' => 'Convertible', 'segment' => 'Grand Tourer', 'fuel_types' => ['Gasoline']],
                ['name' => 'Purosangue', 'generation' => '1G', 'start_year' => 2022, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Luxury SUV', 'fuel_types' => ['Gasoline', 'Hybrid']],
            ],
            
            // Lamborghini Models
            'Lamborghini' => [
                ['name' => 'Huracán', 'generation' => '2G', 'start_year' => 2014, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Supercar', 'fuel_types' => ['Gasoline']],
                ['name' => 'Aventador', 'generation' => '1G', 'start_year' => 2011, 'end_year' => 2022, 'body_type' => 'Coupe', 'segment' => 'Supercar', 'fuel_types' => ['Gasoline']],
                ['name' => 'Urus', 'generation' => '1G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Luxury SUV', 'fuel_types' => ['Gasoline']],
                ['name' => 'Revuelto', 'generation' => '1G', 'start_year' => 2023, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Supercar', 'fuel_types' => ['Hybrid']],
            ],
            
            // Maserati Models
            'Maserati' => [
                ['name' => 'Ghibli', 'generation' => '3G', 'start_year' => 2013, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Luxury Sedan', 'fuel_types' => ['Gasoline', 'Hybrid']],
                ['name' => 'Quattroporte', 'generation' => '6G', 'start_year' => 2013, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Luxury Sedan', 'fuel_types' => ['Gasoline', 'Hybrid']],
                ['name' => 'Levante', 'generation' => '1G', 'start_year' => 2016, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Luxury SUV', 'fuel_types' => ['Gasoline', 'Hybrid']],
                ['name' => 'MC20', 'generation' => '1G', 'start_year' => 2020, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Supercar', 'fuel_types' => ['Gasoline']],
                ['name' => 'GranTurismo', 'generation' => '2G', 'start_year' => 2022, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Grand Tourer', 'fuel_types' => ['Gasoline', 'Electric']],
            ],
            
            // Bentley Models
            'Bentley' => [
                ['name' => 'Continental GT', 'generation' => '3G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Luxury Coupe', 'fuel_types' => ['Gasoline', 'Hybrid']],
                ['name' => 'Bentayga', 'generation' => '1G', 'start_year' => 2015, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Luxury SUV', 'fuel_types' => ['Gasoline', 'Hybrid']],
                ['name' => 'Flying Spur', 'generation' => '3G', 'start_year' => 2019, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Luxury Sedan', 'fuel_types' => ['Gasoline', 'Hybrid']],
                ['name' => 'Mulsanne', 'generation' => '1G', 'start_year' => 2010, 'end_year' => 2020, 'body_type' => 'Sedan', 'segment' => 'Luxury Sedan', 'fuel_types' => ['Gasoline']],
            ],
            
            // Rolls-Royce Models
            'Rolls-Royce' => [
                ['name' => 'Phantom', 'generation' => '8G', 'start_year' => 2017, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Ultra Luxury Sedan', 'fuel_types' => ['Gasoline']],
                ['name' => 'Ghost', 'generation' => '2G', 'start_year' => 2020, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Luxury Sedan', 'fuel_types' => ['Gasoline']],
                ['name' => 'Cullinan', 'generation' => '1G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Ultra Luxury SUV', 'fuel_types' => ['Gasoline']],
                ['name' => 'Wraith', 'generation' => '1G', 'start_year' => 2013, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Luxury Coupe', 'fuel_types' => ['Gasoline']],
                ['name' => 'Dawn', 'generation' => '1G', 'start_year' => 2015, 'end_year' => null, 'body_type' => 'Convertible', 'segment' => 'Luxury Convertible', 'fuel_types' => ['Gasoline']],
            ],
            
            // Aston Martin Models
            'Aston Martin' => [
                ['name' => 'DB11', 'generation' => '1G', 'start_year' => 2016, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Grand Tourer', 'fuel_types' => ['Gasoline']],
                ['name' => 'Vantage', 'generation' => '2G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Sports Car', 'fuel_types' => ['Gasoline']],
                ['name' => 'DBS', 'generation' => '2G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Grand Tourer', 'fuel_types' => ['Gasoline']],
                ['name' => 'DBX', 'generation' => '1G', 'start_year' => 2020, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Luxury SUV', 'fuel_types' => ['Gasoline']],
                ['name' => 'Valhalla', 'generation' => '1G', 'start_year' => 2024, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Supercar', 'fuel_types' => ['Hybrid']],
            ],
            
            // McLaren Models
            'McLaren' => [
                ['name' => '720S', 'generation' => '1G', 'start_year' => 2017, 'end_year' => 2023, 'body_type' => 'Coupe', 'segment' => 'Supercar', 'fuel_types' => ['Gasoline']],
                ['name' => '765LT', 'generation' => '1G', 'start_year' => 2020, 'end_year' => 2021, 'body_type' => 'Coupe', 'segment' => 'Supercar', 'fuel_types' => ['Gasoline']],
                ['name' => 'Artura', 'generation' => '1G', 'start_year' => 2021, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Supercar', 'fuel_types' => ['Hybrid']],
                ['name' => 'GT', 'generation' => '1G', 'start_year' => 2019, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Grand Tourer', 'fuel_types' => ['Gasoline']],
                ['name' => 'Senna', 'generation' => '1G', 'start_year' => 2018, 'end_year' => 2019, 'body_type' => 'Coupe', 'segment' => 'Hypercar', 'fuel_types' => ['Gasoline']],
            ],
            
            // Bugatti Models
            'Bugatti' => [
                ['name' => 'Chiron', 'generation' => '1G', 'start_year' => 2016, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Hypercar', 'fuel_types' => ['Gasoline']],
                ['name' => 'Veyron', 'generation' => '1G', 'start_year' => 2005, 'end_year' => 2015, 'body_type' => 'Coupe', 'segment' => 'Hypercar', 'fuel_types' => ['Gasoline']],
                ['name' => 'Divo', 'generation' => '1G', 'start_year' => 2018, 'end_year' => 2020, 'body_type' => 'Coupe', 'segment' => 'Hypercar', 'fuel_types' => ['Gasoline']],
                ['name' => 'Centodieci', 'generation' => '1G', 'start_year' => 2020, 'end_year' => 2022, 'body_type' => 'Coupe', 'segment' => 'Hypercar', 'fuel_types' => ['Gasoline']],
            ],
            
            // Koenigsegg Models
            'Koenigsegg' => [
                ['name' => 'Regera', 'generation' => '1G', 'start_year' => 2015, 'end_year' => 2022, 'body_type' => 'Coupe', 'segment' => 'Hypercar', 'fuel_types' => ['Hybrid']],
                ['name' => 'Jesko', 'generation' => '1G', 'start_year' => 2019, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Hypercar', 'fuel_types' => ['Gasoline']],
                ['name' => 'Gemera', 'generation' => '1G', 'start_year' => 2024, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Hypercar', 'fuel_types' => ['Hybrid']],
                ['name' => 'CC850', 'generation' => '1G', 'start_year' => 2022, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Hypercar', 'fuel_types' => ['Gasoline']],
            ],
            
            // Pagani Models (if exists)
            'Pagani' => [
                ['name' => 'Huayra', 'generation' => '1G', 'start_year' => 2011, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Hypercar', 'fuel_types' => ['Gasoline']],
                ['name' => 'Zonda', 'generation' => '1G', 'start_year' => 1999, 'end_year' => 2011, 'body_type' => 'Coupe', 'segment' => 'Hypercar', 'fuel_types' => ['Gasoline']],
                ['name' => 'Utopia', 'generation' => '1G', 'start_year' => 2022, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Hypercar', 'fuel_types' => ['Gasoline']],
            ],
            
            // More BMW Models
            'BMW' => [
                ['name' => 'i3', 'generation' => '1G', 'start_year' => 2013, 'end_year' => 2022, 'body_type' => 'Hatchback', 'segment' => 'Electric', 'fuel_types' => ['Electric']],
                ['name' => 'i8', 'generation' => '1G', 'start_year' => 2014, 'end_year' => 2020, 'body_type' => 'Coupe', 'segment' => 'Hybrid Sports', 'fuel_types' => ['Hybrid']],
                ['name' => 'iX', 'generation' => '1G', 'start_year' => 2021, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Electric SUV', 'fuel_types' => ['Electric']],
                ['name' => 'i4', 'generation' => '1G', 'start_year' => 2021, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Electric Sedan', 'fuel_types' => ['Electric']],
                ['name' => 'M2', 'generation' => '2G', 'start_year' => 2022, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Sports Car', 'fuel_types' => ['Gasoline']],
                ['name' => 'M3', 'generation' => '6G', 'start_year' => 2020, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Sports Sedan', 'fuel_types' => ['Gasoline']],
                ['name' => 'M4', 'generation' => '2G', 'start_year' => 2020, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Sports Car', 'fuel_types' => ['Gasoline']],
                ['name' => 'M5', 'generation' => '6G', 'start_year' => 2017, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Sports Sedan', 'fuel_types' => ['Gasoline']],
                ['name' => 'X1', 'generation' => '3G', 'start_year' => 2022, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Compact SUV', 'fuel_types' => ['Gasoline', 'Electric']],
                ['name' => 'X2', 'generation' => '2G', 'start_year' => 2022, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Compact SUV', 'fuel_types' => ['Gasoline', 'Electric']],
                ['name' => 'X4', 'generation' => '2G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Midsize SUV', 'fuel_types' => ['Gasoline']],
                ['name' => 'X6', 'generation' => '3G', 'start_year' => 2019, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Midsize SUV', 'fuel_types' => ['Gasoline']],
                ['name' => 'X7', 'generation' => '1G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Full-size SUV', 'fuel_types' => ['Gasoline']],
                ['name' => 'Z4', 'generation' => '3G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'Convertible', 'segment' => 'Sports Car', 'fuel_types' => ['Gasoline']],
                ['name' => '8 Series', 'generation' => '2G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Luxury Coupe', 'fuel_types' => ['Gasoline']],
            ],
            
            // More Mercedes-Benz Models
            'Mercedes-Benz' => [
                ['name' => 'A-Class', 'generation' => '4G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'Hatchback', 'segment' => 'Compact', 'fuel_types' => ['Gasoline', 'Electric']],
                ['name' => 'B-Class', 'generation' => '2G', 'start_year' => 2019, 'end_year' => null, 'body_type' => 'Hatchback', 'segment' => 'Compact', 'fuel_types' => ['Gasoline', 'Electric']],
                ['name' => 'CLA', 'generation' => '2G', 'start_year' => 2019, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Compact', 'fuel_types' => ['Gasoline']],
                ['name' => 'CLS', 'generation' => '3G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Luxury Sedan', 'fuel_types' => ['Gasoline']],
                ['name' => 'G-Class', 'generation' => '2G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Luxury SUV', 'fuel_types' => ['Gasoline', 'Electric']],
                ['name' => 'GLA', 'generation' => '2G', 'start_year' => 2020, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Compact SUV', 'fuel_types' => ['Gasoline', 'Electric']],
                ['name' => 'GLB', 'generation' => '1G', 'start_year' => 2019, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Compact SUV', 'fuel_types' => ['Gasoline', 'Electric']],
                ['name' => 'GLE', 'generation' => '2G', 'start_year' => 2019, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Midsize SUV', 'fuel_types' => ['Gasoline', 'Hybrid', 'Electric']],
                ['name' => 'GLS', 'generation' => '2G', 'start_year' => 2019, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Full-size SUV', 'fuel_types' => ['Gasoline', 'Hybrid']],
                ['name' => 'AMG GT', 'generation' => '1G', 'start_year' => 2014, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Sports Car', 'fuel_types' => ['Gasoline']],
                ['name' => 'SL', 'generation' => '7G', 'start_year' => 2021, 'end_year' => null, 'body_type' => 'Convertible', 'segment' => 'Sports Car', 'fuel_types' => ['Gasoline']],
                ['name' => 'EQS', 'generation' => '1G', 'start_year' => 2021, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Electric Sedan', 'fuel_types' => ['Electric']],
                ['name' => 'EQC', 'generation' => '1G', 'start_year' => 2019, 'end_year' => 2023, 'body_type' => 'SUV', 'segment' => 'Electric SUV', 'fuel_types' => ['Electric']],
                ['name' => 'EQE', 'generation' => '1G', 'start_year' => 2022, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Electric Sedan', 'fuel_types' => ['Electric']],
                ['name' => 'EQS SUV', 'generation' => '1G', 'start_year' => 2022, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Electric SUV', 'fuel_types' => ['Electric']],
            ],
            
            // More Audi Models
            'Audi' => [
                ['name' => 'A1', 'generation' => '2G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'Hatchback', 'segment' => 'Compact', 'fuel_types' => ['Gasoline']],
                ['name' => 'A5', 'generation' => '2G', 'start_year' => 2016, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Luxury Coupe', 'fuel_types' => ['Gasoline']],
                ['name' => 'A7', 'generation' => '2G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Luxury Sedan', 'fuel_types' => ['Gasoline', 'Hybrid']],
                ['name' => 'A8', 'generation' => '4G', 'start_year' => 2017, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Luxury Sedan', 'fuel_types' => ['Gasoline', 'Hybrid']],
                ['name' => 'Q2', 'generation' => '1G', 'start_year' => 2016, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Compact SUV', 'fuel_types' => ['Gasoline']],
                ['name' => 'Q3', 'generation' => '2G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Compact SUV', 'fuel_types' => ['Gasoline']],
                ['name' => 'Q7', 'generation' => '2G', 'start_year' => 2015, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Midsize SUV', 'fuel_types' => ['Gasoline', 'Hybrid']],
                ['name' => 'Q8', 'generation' => '1G', 'start_year' => 2018, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Midsize SUV', 'fuel_types' => ['Gasoline']],
                ['name' => 'TT', 'generation' => '3G', 'start_year' => 2014, 'end_year' => 2023, 'body_type' => 'Coupe', 'segment' => 'Sports Car', 'fuel_types' => ['Gasoline']],
                ['name' => 'R8', 'generation' => '2G', 'start_year' => 2015, 'end_year' => null, 'body_type' => 'Coupe', 'segment' => 'Supercar', 'fuel_types' => ['Gasoline']],
                ['name' => 'e-tron', 'generation' => '1G', 'start_year' => 2018, 'end_year' => 2023, 'body_type' => 'SUV', 'segment' => 'Electric SUV', 'fuel_types' => ['Electric']],
                ['name' => 'e-tron GT', 'generation' => '1G', 'start_year' => 2021, 'end_year' => null, 'body_type' => 'Sedan', 'segment' => 'Electric Sedan', 'fuel_types' => ['Electric']],
                ['name' => 'Q4 e-tron', 'generation' => '1G', 'start_year' => 2021, 'end_year' => null, 'body_type' => 'SUV', 'segment' => 'Electric SUV', 'fuel_types' => ['Electric']],
            ],
        ];
        
        foreach ($models as $brandName => $brandModels) {
            if (isset($brands[$brandName])) {
                $brand = $brands[$brandName];
                
                foreach ($brandModels as $modelData) {
                    CarModel::updateOrCreate(
                        [
                            'car_brand_id' => $brand->id,
                            'name' => $modelData['name'],
                            'generation' => $modelData['generation']
                        ],
                        [
                            'start_year' => $modelData['start_year'],
                            'end_year' => $modelData['end_year'],
                            'body_type' => $modelData['body_type'],
                            'segment' => $modelData['segment'],
                            'fuel_types' => json_encode($modelData['fuel_types']),
                            'is_active' => true,
                            'is_popular' => true,
                            'sort_order' => 1
                        ]
                    );
                }
            }
        }
        
        $this->command->info('Extended car models seeded successfully!');
    }
}
