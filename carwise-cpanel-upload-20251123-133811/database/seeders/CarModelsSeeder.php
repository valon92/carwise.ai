<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Support\Str;

class CarModelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedGermanBrands();
        $this->seedJapaneseBrands();
        $this->seedAmericanBrands();
        $this->seedItalianBrands();
        $this->seedBritishBrands();
        $this->seedFrenchBrands();
        $this->seedKoreanBrands();
        $this->seedChineseBrands();
        $this->seedIndianBrands();
        $this->seedRussianBrands();
        $this->seedElectricBrands();
        $this->seedOtherBrands();
    }

    private function seedGermanBrands(): void
    {
        // BMW Models
        $bmw = CarBrand::where('name', 'BMW')->first();
        if ($bmw) {
            $this->createModels($bmw, [
                // Sedan Series
                ['name' => '1 Series', 'generation' => 'F40', 'start_year' => 2019, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => '2 Series', 'generation' => 'F44', 'start_year' => 2019, 'body_type' => 'coupe', 'segment' => 'compact'],
                ['name' => '3 Series', 'generation' => 'G20', 'start_year' => 2019, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => '4 Series', 'generation' => 'G22', 'start_year' => 2020, 'body_type' => 'coupe', 'segment' => 'compact'],
                ['name' => '5 Series', 'generation' => 'G30', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => '6 Series', 'generation' => 'G32', 'start_year' => 2017, 'body_type' => 'coupe', 'segment' => 'luxury'],
                ['name' => '7 Series', 'generation' => 'G11', 'start_year' => 2015, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => '8 Series', 'generation' => 'G15', 'start_year' => 2018, 'body_type' => 'coupe', 'segment' => 'luxury'],
                
                // SUV X Series
                ['name' => 'X1', 'generation' => 'F48', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'X2', 'generation' => 'F39', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'X3', 'generation' => 'G01', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'X4', 'generation' => 'G02', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'X5', 'generation' => 'G05', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'X6', 'generation' => 'G06', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'X7', 'generation' => 'G07', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'fullsize'],
                
                // Electric i Series
                ['name' => 'i3', 'generation' => 'I01', 'start_year' => 2013, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'i4', 'generation' => 'G26', 'start_year' => 2021, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'i7', 'generation' => 'G70', 'start_year' => 2022, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'i8', 'generation' => 'I12', 'start_year' => 2014, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'iX', 'generation' => 'I20', 'start_year' => 2021, 'body_type' => 'suv', 'segment' => 'luxury'],
                ['name' => 'iX3', 'generation' => 'G08', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'midsize'],
                
                // Performance M Series
                ['name' => 'M2', 'generation' => 'F87', 'start_year' => 2016, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'M3', 'generation' => 'G80', 'start_year' => 2020, 'body_type' => 'sedan', 'segment' => 'sports'],
                ['name' => 'M4', 'generation' => 'G82', 'start_year' => 2020, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'M5', 'generation' => 'F90', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'sports'],
                ['name' => 'M8', 'generation' => 'F91', 'start_year' => 2019, 'body_type' => 'coupe', 'segment' => 'sports'],
            ]);
        }

        // Mercedes-Benz Models
        $mercedes = CarBrand::where('name', 'Mercedes-Benz')->first();
        if ($mercedes) {
            $this->createModels($mercedes, [
                ['name' => 'C-Class', 'generation' => 'W205', 'start_year' => 2014, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'E-Class', 'generation' => 'W213', 'start_year' => 2016, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'S-Class', 'generation' => 'W223', 'start_year' => 2020, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'A-Class', 'generation' => 'W177', 'start_year' => 2018, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'GLA', 'generation' => 'H247', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'GLC', 'generation' => 'X253', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'GLE', 'generation' => 'W167', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'GLS', 'generation' => 'X167', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'fullsize'],
                ['name' => 'AMG GT', 'generation' => 'C190', 'start_year' => 2014, 'body_type' => 'coupe', 'segment' => 'sports'],
            ]);
        }

        // Audi Models
        $audi = CarBrand::where('name', 'Audi')->first();
        if ($audi) {
            $this->createModels($audi, [
                ['name' => 'A3', 'generation' => '8Y', 'start_year' => 2020, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'A4', 'generation' => 'B9', 'start_year' => 2016, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'A6', 'generation' => 'C8', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'A8', 'generation' => 'D5', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'Q3', 'generation' => 'F3', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Q5', 'generation' => 'FY', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Q7', 'generation' => '4M', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Q8', 'generation' => '4M', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'TT', 'generation' => '8S', 'start_year' => 2014, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'R8', 'generation' => '4S', 'start_year' => 2015, 'body_type' => 'coupe', 'segment' => 'sports'],
            ]);
        }

        // Volkswagen Models
        $vw = CarBrand::where('name', 'Volkswagen')->first();
        if ($vw) {
            $this->createModels($vw, [
                ['name' => 'Golf', 'generation' => 'Mk8', 'start_year' => 2019, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'Passat', 'generation' => 'B8', 'start_year' => 2014, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'Polo', 'generation' => '6R', 'start_year' => 2017, 'body_type' => 'hatchback', 'segment' => 'subcompact'],
                ['name' => 'Tiguan', 'generation' => '5N', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Touareg', 'generation' => '3', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Arteon', 'generation' => '3H', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'ID.3', 'generation' => '1', 'start_year' => 2020, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'ID.4', 'generation' => '1', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'compact'],
            ]);
        }

        // Porsche Models
        $porsche = CarBrand::where('name', 'Porsche')->first();
        if ($porsche) {
            $this->createModels($porsche, [
                ['name' => '911', 'generation' => '992', 'start_year' => 2019, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'Cayenne', 'generation' => 'E3', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Macan', 'generation' => '95B', 'start_year' => 2014, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Panamera', 'generation' => '971', 'start_year' => 2016, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'Taycan', 'generation' => '9J1', 'start_year' => 2019, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => '718 Boxster', 'generation' => '982', 'start_year' => 2016, 'body_type' => 'convertible', 'segment' => 'sports'],
                ['name' => '718 Cayman', 'generation' => '982', 'start_year' => 2016, 'body_type' => 'coupe', 'segment' => 'sports'],
            ]);
        }
    }

    private function seedJapaneseBrands(): void
    {
        // Toyota Models
        $toyota = CarBrand::where('name', 'Toyota')->first();
        if ($toyota) {
            $this->createModels($toyota, [
                ['name' => 'Camry', 'generation' => 'XV70', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'Corolla', 'generation' => 'E210', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'RAV4', 'generation' => 'XA50', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Highlander', 'generation' => 'XU70', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Prius', 'generation' => 'XW50', 'start_year' => 2015, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'Land Cruiser', 'generation' => 'J200', 'start_year' => 2007, 'body_type' => 'suv', 'segment' => 'fullsize'],
                ['name' => '4Runner', 'generation' => 'N280', 'start_year' => 2009, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Tacoma', 'generation' => 'N300', 'start_year' => 2015, 'body_type' => 'pickup', 'segment' => 'midsize'],
                ['name' => 'Tundra', 'generation' => 'XK70', 'start_year' => 2021, 'body_type' => 'pickup', 'segment' => 'fullsize'],
            ]);
        }

        // Honda Models
        $honda = CarBrand::where('name', 'Honda')->first();
        if ($honda) {
            $this->createModels($honda, [
                ['name' => 'Civic', 'generation' => '11th', 'start_year' => 2021, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Accord', 'generation' => '10th', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'CR-V', 'generation' => '5th', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Pilot', 'generation' => '3rd', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'HR-V', 'generation' => '2nd', 'start_year' => 2014, 'body_type' => 'suv', 'segment' => 'subcompact'],
                ['name' => 'Passport', 'generation' => '2nd', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Ridgeline', 'generation' => '2nd', 'start_year' => 2016, 'body_type' => 'pickup', 'segment' => 'midsize'],
                ['name' => 'Insight', 'generation' => '3rd', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'compact'],
            ]);
        }

        // Nissan Models
        $nissan = CarBrand::where('name', 'Nissan')->first();
        if ($nissan) {
            $this->createModels($nissan, [
                ['name' => 'Altima', 'generation' => 'L34', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'Sentra', 'generation' => 'B18', 'start_year' => 2019, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Rogue', 'generation' => 'T33', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Murano', 'generation' => 'Z52', 'start_year' => 2014, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Pathfinder', 'generation' => 'R53', 'start_year' => 2021, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Armada', 'generation' => 'Y62', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'fullsize'],
                ['name' => 'Frontier', 'generation' => 'D23', 'start_year' => 2021, 'body_type' => 'pickup', 'segment' => 'midsize'],
                ['name' => 'Titan', 'generation' => 'A61', 'start_year' => 2016, 'body_type' => 'pickup', 'segment' => 'fullsize'],
                ['name' => '370Z', 'generation' => 'Z34', 'start_year' => 2008, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'GT-R', 'generation' => 'R35', 'start_year' => 2007, 'body_type' => 'coupe', 'segment' => 'sports'],
            ]);
        }
    }

    private function seedAmericanBrands(): void
    {
        // Ford Models
        $ford = CarBrand::where('name', 'Ford')->first();
        if ($ford) {
            $this->createModels($ford, [
                ['name' => 'F-150', 'generation' => '14th', 'start_year' => 2020, 'body_type' => 'pickup', 'segment' => 'fullsize'],
                ['name' => 'Mustang', 'generation' => 'S550', 'start_year' => 2014, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'Explorer', 'generation' => '6th', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Escape', 'generation' => '4th', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Edge', 'generation' => '2nd', 'start_year' => 2014, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Expedition', 'generation' => '4th', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'fullsize'],
                ['name' => 'Ranger', 'generation' => 'T6', 'start_year' => 2018, 'body_type' => 'pickup', 'segment' => 'midsize'],
                ['name' => 'Bronco', 'generation' => '6th', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'midsize'],
            ]);
        }

        // Chevrolet Models
        $chevrolet = CarBrand::where('name', 'Chevrolet')->first();
        if ($chevrolet) {
            $this->createModels($chevrolet, [
                ['name' => 'Silverado', 'generation' => '4th', 'start_year' => 2018, 'body_type' => 'pickup', 'segment' => 'fullsize'],
                ['name' => 'Equinox', 'generation' => '3rd', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Traverse', 'generation' => '2nd', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Tahoe', 'generation' => '5th', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'fullsize'],
                ['name' => 'Suburban', 'generation' => '12th', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'fullsize'],
                ['name' => 'Camaro', 'generation' => '6th', 'start_year' => 2015, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'Corvette', 'generation' => 'C8', 'start_year' => 2020, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'Malibu', 'generation' => '9th', 'start_year' => 2015, 'body_type' => 'sedan', 'segment' => 'midsize'],
            ]);
        }

        // Tesla Models
        $tesla = CarBrand::where('name', 'Tesla')->first();
        if ($tesla) {
            $this->createModels($tesla, [
                ['name' => 'Model S', 'generation' => '2nd', 'start_year' => 2012, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'Model 3', 'generation' => '1st', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Model X', 'generation' => '1st', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Model Y', 'generation' => '1st', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Cybertruck', 'generation' => '1st', 'start_year' => 2023, 'body_type' => 'pickup', 'segment' => 'fullsize'],
            ]);
        }
    }

    private function seedItalianBrands(): void
    {
        // Ferrari Models
        $ferrari = CarBrand::where('name', 'Ferrari')->first();
        if ($ferrari) {
            $this->createModels($ferrari, [
                ['name' => '488 GTB', 'generation' => 'F142M', 'start_year' => 2015, 'body_type' => 'coupe', 'segment' => 'supercar'],
                ['name' => 'F8 Tributo', 'generation' => 'F142M', 'start_year' => 2019, 'body_type' => 'coupe', 'segment' => 'supercar'],
                ['name' => 'SF90 Stradale', 'generation' => 'F173', 'start_year' => 2019, 'body_type' => 'coupe', 'segment' => 'supercar'],
                ['name' => 'Roma', 'generation' => 'F169', 'start_year' => 2019, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'Portofino', 'generation' => 'F164', 'start_year' => 2017, 'body_type' => 'convertible', 'segment' => 'sports'],
                ['name' => '812 Superfast', 'generation' => 'F152M', 'start_year' => 2017, 'body_type' => 'coupe', 'segment' => 'supercar'],
            ]);
        }

        // Lamborghini Models
        $lamborghini = CarBrand::where('name', 'Lamborghini')->first();
        if ($lamborghini) {
            $this->createModels($lamborghini, [
                ['name' => 'Huracán', 'generation' => 'LB724', 'start_year' => 2014, 'body_type' => 'coupe', 'segment' => 'supercar'],
                ['name' => 'Aventador', 'generation' => 'LB834', 'start_year' => 2011, 'body_type' => 'coupe', 'segment' => 'supercar'],
                ['name' => 'Urus', 'generation' => 'LB715', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'luxury'],
                ['name' => 'Gallardo', 'generation' => 'LB560', 'start_year' => 2003, 'body_type' => 'coupe', 'segment' => 'supercar'],
            ]);
        }
    }

    private function seedBritishBrands(): void
    {
        // Land Rover Models
        $landRover = CarBrand::where('name', 'Land Rover')->first();
        if ($landRover) {
            $this->createModels($landRover, [
                ['name' => 'Range Rover', 'generation' => 'L405', 'start_year' => 2012, 'body_type' => 'suv', 'segment' => 'luxury'],
                ['name' => 'Range Rover Sport', 'generation' => 'L494', 'start_year' => 2013, 'body_type' => 'suv', 'segment' => 'luxury'],
                ['name' => 'Range Rover Evoque', 'generation' => 'L551', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Range Rover Velar', 'generation' => 'L560', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Discovery', 'generation' => 'L462', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Discovery Sport', 'generation' => 'L550', 'start_year' => 2014, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Defender', 'generation' => 'L663', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'midsize'],
            ]);
        }

        // Jaguar Models
        $jaguar = CarBrand::where('name', 'Jaguar')->first();
        if ($jaguar) {
            $this->createModels($jaguar, [
                ['name' => 'XF', 'generation' => 'X260', 'start_year' => 2015, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'XE', 'generation' => 'X760', 'start_year' => 2015, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'XJ', 'generation' => 'X351', 'start_year' => 2009, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'F-PACE', 'generation' => 'X761', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'E-PACE', 'generation' => 'X540', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'I-PACE', 'generation' => 'X590', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'F-TYPE', 'generation' => 'X152', 'start_year' => 2013, 'body_type' => 'coupe', 'segment' => 'sports'],
            ]);
        }
    }

    private function seedFrenchBrands(): void
    {
        // Peugeot Models
        $peugeot = CarBrand::where('name', 'Peugeot')->first();
        if ($peugeot) {
            $this->createModels($peugeot, [
                ['name' => '208', 'generation' => '2nd', 'start_year' => 2019, 'body_type' => 'hatchback', 'segment' => 'subcompact'],
                ['name' => '308', 'generation' => '3rd', 'start_year' => 2021, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => '508', 'generation' => '2nd', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => '2008', 'generation' => '2nd', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'subcompact'],
                ['name' => '3008', 'generation' => '2nd', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => '5008', 'generation' => '2nd', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'midsize'],
            ]);
        }

        // Renault Models
        $renault = CarBrand::where('name', 'Renault')->first();
        if ($renault) {
            $this->createModels($renault, [
                ['name' => 'Clio', 'generation' => '5th', 'start_year' => 2019, 'body_type' => 'hatchback', 'segment' => 'subcompact'],
                ['name' => 'Megane', 'generation' => '4th', 'start_year' => 2016, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'Talisman', 'generation' => '1st', 'start_year' => 2015, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'Captur', 'generation' => '2nd', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'subcompact'],
                ['name' => 'Kadjar', 'generation' => '1st', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Koleos', 'generation' => '2nd', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'midsize'],
            ]);
        }
    }

    private function seedKoreanBrands(): void
    {
        // Hyundai Models
        $hyundai = CarBrand::where('name', 'Hyundai')->first();
        if ($hyundai) {
            $this->createModels($hyundai, [
                ['name' => 'Elantra', 'generation' => '7th', 'start_year' => 2020, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Sonata', 'generation' => '8th', 'start_year' => 2019, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'Tucson', 'generation' => '4th', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Santa Fe', 'generation' => '4th', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Palisade', 'generation' => '1st', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'fullsize'],
                ['name' => 'Kona', 'generation' => '1st', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'subcompact'],
                ['name' => 'Veloster', 'generation' => '2nd', 'start_year' => 2018, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'Genesis G90', 'generation' => '2nd', 'start_year' => 2016, 'body_type' => 'sedan', 'segment' => 'luxury'],
            ]);
        }

        // Kia Models
        $kia = CarBrand::where('name', 'Kia')->first();
        if ($kia) {
            $this->createModels($kia, [
                ['name' => 'Forte', 'generation' => '3rd', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Optima', 'generation' => '4th', 'start_year' => 2015, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'Sportage', 'generation' => '4th', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Sorento', 'generation' => '4th', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Telluride', 'generation' => '1st', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'fullsize'],
                ['name' => 'Soul', 'generation' => '3rd', 'start_year' => 2019, 'body_type' => 'hatchback', 'segment' => 'subcompact'],
                ['name' => 'Stinger', 'generation' => '1st', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'sports'],
            ]);
        }
    }

    private function seedOtherBrands(): void
    {
        // Volvo Models
        $volvo = CarBrand::where('name', 'Volvo')->first();
        if ($volvo) {
            $this->createModels($volvo, [
                ['name' => 'S60', 'generation' => '3rd', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'S90', 'generation' => '2nd', 'start_year' => 2016, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'XC40', 'generation' => '1st', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'XC60', 'generation' => '2nd', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'XC90', 'generation' => '2nd', 'start_year' => 2014, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'V60', 'generation' => '2nd', 'start_year' => 2018, 'body_type' => 'wagon', 'segment' => 'compact'],
                ['name' => 'V90', 'generation' => '2nd', 'start_year' => 2016, 'body_type' => 'wagon', 'segment' => 'midsize'],
            ]);
        }

        // Mazda Models
        $mazda = CarBrand::where('name', 'Mazda')->first();
        if ($mazda) {
            $this->createModels($mazda, [
                ['name' => 'Mazda3', 'generation' => '4th', 'start_year' => 2019, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Mazda6', 'generation' => '3rd', 'start_year' => 2012, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'CX-3', 'generation' => '1st', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'subcompact'],
                ['name' => 'CX-5', 'generation' => '2nd', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'CX-9', 'generation' => '2nd', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'MX-5 Miata', 'generation' => '4th', 'start_year' => 2015, 'body_type' => 'convertible', 'segment' => 'sports'],
            ]);
        }
    }

    private function createModels(CarBrand $brand, array $models): void
    {
        foreach ($models as $modelData) {
            // Check if model already exists
            $existingModel = CarModel::where('car_brand_id', $brand->id)
                ->where('name', $modelData['name'])
                ->where('generation', $modelData['generation'] ?? null)
                ->first();
            
            if (!$existingModel) {
                CarModel::create([
                    'car_brand_id' => $brand->id,
                    'name' => $modelData['name'],
                    'slug' => Str::slug($brand->name . ' ' . $modelData['name']),
                    'generation' => $modelData['generation'] ?? null,
                    'start_year' => $modelData['start_year'] ?? null,
                    'end_year' => $modelData['end_year'] ?? null,
                    'body_type' => $modelData['body_type'] ?? null,
                    'segment' => $modelData['segment'] ?? null,
                    'engine_options' => $this->getEngineOptions($modelData['body_type'] ?? 'sedan'),
                    'transmission_options' => ['manual', 'automatic', 'cvt'],
                    'fuel_types' => $this->getFuelTypes($modelData['body_type'] ?? 'sedan'),
                    'specifications' => $this->getSpecifications($modelData['body_type'] ?? 'sedan'),
                    'description' => $this->getDescription($brand->name, $modelData['name']),
                    'is_active' => true,
                    'is_popular' => $this->isPopularModel($modelData['name']),
                    'sort_order' => 0,
                ]);
            }
        }
    }

    private function getEngineOptions(string $bodyType): array
    {
        $baseEngines = ['2.0L I4', '2.5L I4', '3.0L V6'];
        
        if ($bodyType === 'suv' || $bodyType === 'pickup') {
            return array_merge($baseEngines, ['3.5L V6', '5.0L V8']);
        } elseif ($bodyType === 'sports' || $bodyType === 'supercar') {
            return ['3.0L V6 Turbo', '4.0L V8', '5.2L V10', '6.5L V12'];
        }
        
        return $baseEngines;
    }

    private function getFuelTypes(string $bodyType): array
    {
        $baseFuels = ['gasoline', 'hybrid'];
        
        if ($bodyType === 'suv' || $bodyType === 'pickup') {
            return array_merge($baseFuels, ['diesel']);
        } elseif ($bodyType === 'sports' || $bodyType === 'supercar') {
            return ['gasoline'];
        }
        
        return $baseFuels;
    }

    private function getSpecifications(string $bodyType): array
    {
        $baseSpecs = [
            'seating_capacity' => 5,
            'doors' => 4,
            'drive_type' => 'FWD'
        ];
        
        if ($bodyType === 'suv') {
            $baseSpecs['seating_capacity'] = 7;
            $baseSpecs['drive_type'] = 'AWD';
        } elseif ($bodyType === 'pickup') {
            $baseSpecs['seating_capacity'] = 5;
            $baseSpecs['doors'] = 2;
            $baseSpecs['drive_type'] = 'RWD';
        } elseif ($bodyType === 'coupe') {
            $baseSpecs['seating_capacity'] = 4;
            $baseSpecs['doors'] = 2;
        }
        
        return $baseSpecs;
    }

    private function getDescription(string $brand, string $model): string
    {
        return "The {$brand} {$model} is a premium vehicle that combines performance, comfort, and advanced technology. Known for its reliability and innovative features, it represents the best of {$brand}'s engineering excellence.";
    }

    private function isPopularModel(string $modelName): bool
    {
        $popularModels = [
            'Camry', 'Corolla', 'RAV4', 'Civic', 'Accord', 'CR-V',
            'F-150', 'Silverado', 'Explorer', 'Equinox',
            '3 Series', '5 Series', 'X3', 'X5', 'C-Class', 'E-Class', 'GLC',
            'A4', 'A6', 'Q5', 'Q7', 'Golf', 'Passat', 'Tiguan'
        ];
        
        return in_array($modelName, $popularModels);
    }

    private function seedChineseBrands(): void
    {
        // BYD Models
        $byd = CarBrand::where('name', 'BYD')->first();
        if ($byd) {
            $this->createModels($byd, [
                ['name' => 'Seal', 'generation' => '2022', 'start_year' => 2022, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'Tang', 'generation' => '2023', 'start_year' => 2023, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Han', 'generation' => '2020', 'start_year' => 2020, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'Song', 'generation' => '2021', 'start_year' => 2021, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Qin', 'generation' => '2023', 'start_year' => 2023, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Dolphin', 'generation' => '2021', 'start_year' => 2021, 'body_type' => 'hatchback', 'segment' => 'compact'],
            ]);
        }

        // Geely Models
        $geely = CarBrand::where('name', 'Geely')->first();
        if ($geely) {
            $this->createModels($geely, [
                ['name' => 'Coolray', 'generation' => '2020', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Emgrand', 'generation' => '2023', 'start_year' => 2023, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Boyue', 'generation' => '2016', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Xingyue', 'generation' => '2019', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Geometry A', 'generation' => '2019', 'start_year' => 2019, 'body_type' => 'sedan', 'segment' => 'compact'],
            ]);
        }

        // NIO Models
        $nio = CarBrand::where('name', 'NIO')->first();
        if ($nio) {
            $this->createModels($nio, [
                ['name' => 'ET7', 'generation' => '2022', 'start_year' => 2022, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'ET5', 'generation' => '2022', 'start_year' => 2022, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'ES8', 'generation' => '2018', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'luxury'],
                ['name' => 'ES6', 'generation' => '2019', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'EC6', 'generation' => '2020', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'midsize'],
            ]);
        }
    }

    private function seedIndianBrands(): void
    {
        // Tata Models
        $tata = CarBrand::where('name', 'Tata')->first();
        if ($tata) {
            $this->createModels($tata, [
                ['name' => 'Nexon', 'generation' => '2017', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Harrier', 'generation' => '2019', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Safari', 'generation' => '2021', 'start_year' => 2021, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Altroz', 'generation' => '2020', 'start_year' => 2020, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'Tigor', 'generation' => '2017', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Punch', 'generation' => '2021', 'start_year' => 2021, 'body_type' => 'suv', 'segment' => 'compact'],
            ]);
        }

        // Maruti Suzuki Models
        $maruti = CarBrand::where('name', 'Maruti Suzuki')->first();
        if ($maruti) {
            $this->createModels($maruti, [
                ['name' => 'Swift', 'generation' => '2018', 'start_year' => 2018, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'Baleno', 'generation' => '2015', 'start_year' => 2015, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'Vitara Brezza', 'generation' => '2016', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Dzire', 'generation' => '2017', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Ertiga', 'generation' => '2018', 'start_year' => 2018, 'body_type' => 'mpv', 'segment' => 'compact'],
                ['name' => 'XL6', 'generation' => '2019', 'start_year' => 2019, 'body_type' => 'mpv', 'segment' => 'midsize'],
            ]);
        }
    }

    private function seedRussianBrands(): void
    {
        // Lada Models
        $lada = CarBrand::where('name', 'Lada')->first();
        if ($lada) {
            $this->createModels($lada, [
                ['name' => 'Vesta', 'generation' => '2015', 'start_year' => 2015, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'XRAY', 'generation' => '2016', 'start_year' => 2016, 'body_type' => 'crossover', 'segment' => 'compact'],
                ['name' => 'Granta', 'generation' => '2011', 'start_year' => 2011, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Largus', 'generation' => '2012', 'start_year' => 2012, 'body_type' => 'wagon', 'segment' => 'compact'],
                ['name' => 'Niva', 'generation' => '1977', 'start_year' => 1977, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => '4x4', 'generation' => '2020', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'compact'],
            ]);
        }
    }

    private function seedElectricBrands(): void
    {
        // Rivian Models
        $rivian = CarBrand::where('name', 'Rivian')->first();
        if ($rivian) {
            $this->createModels($rivian, [
                ['name' => 'R1T', 'generation' => '2021', 'start_year' => 2021, 'body_type' => 'pickup', 'segment' => 'fullsize'],
                ['name' => 'R1S', 'generation' => '2022', 'start_year' => 2022, 'body_type' => 'suv', 'segment' => 'fullsize'],
                ['name' => 'EDV 700', 'generation' => '2022', 'start_year' => 2022, 'body_type' => 'van', 'segment' => 'commercial'],
            ]);
        }

        // Polestar Models
        $polestar = CarBrand::where('name', 'Polestar')->first();
        if ($polestar) {
            $this->createModels($polestar, [
                ['name' => 'Polestar 1', 'generation' => '2019', 'start_year' => 2019, 'body_type' => 'coupe', 'segment' => 'luxury'],
                ['name' => 'Polestar 2', 'generation' => '2020', 'start_year' => 2020, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'Polestar 3', 'generation' => '2023', 'start_year' => 2023, 'body_type' => 'suv', 'segment' => 'luxury'],
                ['name' => 'Polestar 4', 'generation' => '2024', 'start_year' => 2024, 'body_type' => 'suv', 'segment' => 'midsize'],
            ]);
        }
    }
}
