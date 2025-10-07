<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Support\Str;

class CompleteCarModelsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAllBrands();
    }

    private function seedAllBrands(): void
    {
        $brands = CarBrand::all()->keyBy('name');
        
        $models = [
            // German Brands
            'BMW' => [
                ['name' => '1 Series', 'generation' => 'F40', 'start_year' => 2019, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => '2 Series', 'generation' => 'F44', 'start_year' => 2019, 'body_type' => 'coupe', 'segment' => 'compact'],
                ['name' => '3 Series', 'generation' => 'G20', 'start_year' => 2019, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => '4 Series', 'generation' => 'G22', 'start_year' => 2020, 'body_type' => 'coupe', 'segment' => 'compact'],
                ['name' => '5 Series', 'generation' => 'G30', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => '6 Series', 'generation' => 'G32', 'start_year' => 2017, 'body_type' => 'coupe', 'segment' => 'luxury'],
                ['name' => '7 Series', 'generation' => 'G11', 'start_year' => 2015, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => '8 Series', 'generation' => 'G15', 'start_year' => 2018, 'body_type' => 'coupe', 'segment' => 'luxury'],
                ['name' => 'X1', 'generation' => 'F48', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'X2', 'generation' => 'F39', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'X3', 'generation' => 'G01', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'X4', 'generation' => 'G02', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'X5', 'generation' => 'G05', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'X6', 'generation' => 'G06', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'X7', 'generation' => 'G07', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'fullsize'],
                ['name' => 'i3', 'generation' => 'I01', 'start_year' => 2013, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'i4', 'generation' => 'G26', 'start_year' => 2021, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'i7', 'generation' => 'G70', 'start_year' => 2022, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'i8', 'generation' => 'I12', 'start_year' => 2014, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'iX', 'generation' => 'I20', 'start_year' => 2021, 'body_type' => 'suv', 'segment' => 'luxury'],
                ['name' => 'iX3', 'generation' => 'G08', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'M2', 'generation' => 'F87', 'start_year' => 2016, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'M3', 'generation' => 'G80', 'start_year' => 2020, 'body_type' => 'sedan', 'segment' => 'sports'],
                ['name' => 'M4', 'generation' => 'G82', 'start_year' => 2020, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'M5', 'generation' => 'F90', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'sports'],
                ['name' => 'M8', 'generation' => 'F91', 'start_year' => 2019, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'Z4', 'generation' => 'G29', 'start_year' => 2018, 'body_type' => 'convertible', 'segment' => 'sports'],
            ],
            
            'Mercedes-Benz' => [
                ['name' => 'A-Class', 'generation' => 'W177', 'start_year' => 2018, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'B-Class', 'generation' => 'W247', 'start_year' => 2019, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'C-Class', 'generation' => 'W205', 'start_year' => 2014, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'E-Class', 'generation' => 'W213', 'start_year' => 2016, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'S-Class', 'generation' => 'W223', 'start_year' => 2020, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'CLA', 'generation' => 'C118', 'start_year' => 2019, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'CLS', 'generation' => 'C257', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'GLA', 'generation' => 'H247', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'GLB', 'generation' => 'X247', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'GLC', 'generation' => 'X253', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'GLE', 'generation' => 'W167', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'GLS', 'generation' => 'X167', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'fullsize'],
                ['name' => 'G-Class', 'generation' => 'W463', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'luxury'],
                ['name' => 'AMG GT', 'generation' => 'C190', 'start_year' => 2014, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'SL', 'generation' => 'R232', 'start_year' => 2021, 'body_type' => 'convertible', 'segment' => 'sports'],
                ['name' => 'EQS', 'generation' => 'V297', 'start_year' => 2021, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'EQC', 'generation' => 'N293', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'EQE', 'generation' => 'V295', 'start_year' => 2022, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'EQS SUV', 'generation' => 'X296', 'start_year' => 2022, 'body_type' => 'suv', 'segment' => 'luxury'],
            ],
            
            'Audi' => [
                ['name' => 'A1', 'generation' => '8Y', 'start_year' => 2018, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'A3', 'generation' => '8Y', 'start_year' => 2020, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'A4', 'generation' => 'B9', 'start_year' => 2016, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'A5', 'generation' => 'F5', 'start_year' => 2016, 'body_type' => 'coupe', 'segment' => 'compact'],
                ['name' => 'A6', 'generation' => 'C8', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'A7', 'generation' => 'C8', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'A8', 'generation' => 'D5', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'Q2', 'generation' => 'GA', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Q3', 'generation' => 'F3', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Q4 e-tron', 'generation' => 'FZ', 'start_year' => 2021, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Q5', 'generation' => 'FY', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Q7', 'generation' => '4M', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Q8', 'generation' => '4M', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'TT', 'generation' => '8S', 'start_year' => 2014, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'R8', 'generation' => '4S', 'start_year' => 2015, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'e-tron', 'generation' => 'GE', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'e-tron GT', 'generation' => 'J1', 'start_year' => 2021, 'body_type' => 'sedan', 'segment' => 'luxury'],
            ],
            
            'Volkswagen' => [
                ['name' => 'Golf', 'generation' => 'Mk8', 'start_year' => 2019, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'Passat', 'generation' => 'B8', 'start_year' => 2014, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'Polo', 'generation' => '6R', 'start_year' => 2017, 'body_type' => 'hatchback', 'segment' => 'subcompact'],
                ['name' => 'Tiguan', 'generation' => '5N', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Touareg', 'generation' => '3', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Arteon', 'generation' => '3H', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'ID.3', 'generation' => '1', 'start_year' => 2020, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'ID.4', 'generation' => '1', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'ID.Buzz', 'generation' => '1', 'start_year' => 2022, 'body_type' => 'van', 'segment' => 'compact'],
            ],
            
            'Porsche' => [
                ['name' => '911', 'generation' => '992', 'start_year' => 2019, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'Cayenne', 'generation' => 'E3', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Macan', 'generation' => '95B', 'start_year' => 2014, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Panamera', 'generation' => '971', 'start_year' => 2016, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'Taycan', 'generation' => '9J1', 'start_year' => 2019, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => '718 Boxster', 'generation' => '982', 'start_year' => 2016, 'body_type' => 'convertible', 'segment' => 'sports'],
                ['name' => '718 Cayman', 'generation' => '982', 'start_year' => 2016, 'body_type' => 'coupe', 'segment' => 'sports'],
            ],
            
            'Opel' => [
                ['name' => 'Corsa', 'generation' => 'F', 'start_year' => 2019, 'body_type' => 'hatchback', 'segment' => 'subcompact'],
                ['name' => 'Astra', 'generation' => 'K', 'start_year' => 2015, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'Insignia', 'generation' => 'B', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'Crossland', 'generation' => '1', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'subcompact'],
                ['name' => 'Grandland', 'generation' => '1', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Mokka', 'generation' => '2', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'subcompact'],
            ],
            
            // Japanese Brands
            'Toyota' => [
                ['name' => 'Camry', 'generation' => 'XV70', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'Corolla', 'generation' => 'E210', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'RAV4', 'generation' => 'XA50', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Highlander', 'generation' => 'XU70', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Prius', 'generation' => 'XW50', 'start_year' => 2015, 'body_type' => 'hatchback', 'segment' => 'compact'],
                ['name' => 'Land Cruiser', 'generation' => 'J200', 'start_year' => 2007, 'body_type' => 'suv', 'segment' => 'fullsize'],
                ['name' => '4Runner', 'generation' => 'N280', 'start_year' => 2009, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Tacoma', 'generation' => 'N300', 'start_year' => 2015, 'body_type' => 'pickup', 'segment' => 'midsize'],
                ['name' => 'Tundra', 'generation' => 'XK70', 'start_year' => 2021, 'body_type' => 'pickup', 'segment' => 'fullsize'],
                ['name' => 'C-HR', 'generation' => '1', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'subcompact'],
                ['name' => 'Avalon', 'generation' => 'XX50', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'Sienna', 'generation' => 'XL40', 'start_year' => 2020, 'body_type' => 'minivan', 'segment' => 'midsize'],
            ],
            
            'Honda' => [
                ['name' => 'Civic', 'generation' => '11th', 'start_year' => 2021, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Accord', 'generation' => '10th', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'CR-V', 'generation' => '5th', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Pilot', 'generation' => '3rd', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'HR-V', 'generation' => '2nd', 'start_year' => 2014, 'body_type' => 'suv', 'segment' => 'subcompact'],
                ['name' => 'Passport', 'generation' => '2nd', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'Ridgeline', 'generation' => '2nd', 'start_year' => 2016, 'body_type' => 'pickup', 'segment' => 'midsize'],
                ['name' => 'Insight', 'generation' => '3rd', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Fit', 'generation' => '3rd', 'start_year' => 2014, 'body_type' => 'hatchback', 'segment' => 'subcompact'],
                ['name' => 'Odyssey', 'generation' => '5th', 'start_year' => 2017, 'body_type' => 'minivan', 'segment' => 'midsize'],
            ],
            
            'Nissan' => [
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
                ['name' => 'Versa', 'generation' => 'B18', 'start_year' => 2019, 'body_type' => 'sedan', 'segment' => 'subcompact'],
                ['name' => 'Kicks', 'generation' => '1', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'subcompact'],
            ],
            
            'Mazda' => [
                ['name' => 'Mazda3', 'generation' => '4th', 'start_year' => 2019, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Mazda6', 'generation' => '3rd', 'start_year' => 2012, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'CX-3', 'generation' => '1st', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'subcompact'],
                ['name' => 'CX-5', 'generation' => '2nd', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'CX-9', 'generation' => '2nd', 'start_year' => 2016, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'MX-5 Miata', 'generation' => '4th', 'start_year' => 2015, 'body_type' => 'convertible', 'segment' => 'sports'],
                ['name' => 'CX-30', 'generation' => '1st', 'start_year' => 2019, 'body_type' => 'suv', 'segment' => 'subcompact'],
                ['name' => 'MX-30', 'generation' => '1st', 'start_year' => 2020, 'body_type' => 'suv', 'segment' => 'compact'],
            ],
            
            'Subaru' => [
                ['name' => 'Impreza', 'generation' => '5th', 'start_year' => 2016, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Legacy', 'generation' => '7th', 'start_year' => 2019, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'Outback', 'generation' => '7th', 'start_year' => 2019, 'body_type' => 'wagon', 'segment' => 'midsize'],
                ['name' => 'Forester', 'generation' => '5th', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Crosstrek', 'generation' => '2nd', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'subcompact'],
                ['name' => 'Ascent', 'generation' => '1st', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'WRX', 'generation' => '4th', 'start_year' => 2021, 'body_type' => 'sedan', 'segment' => 'sports'],
                ['name' => 'BRZ', 'generation' => '2nd', 'start_year' => 2021, 'body_type' => 'coupe', 'segment' => 'sports'],
            ],
            
            'Mitsubishi' => [
                ['name' => 'Mirage', 'generation' => '6th', 'start_year' => 2012, 'body_type' => 'hatchback', 'segment' => 'subcompact'],
                ['name' => 'Lancer', 'generation' => '10th', 'start_year' => 2007, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Outlander', 'generation' => '4th', 'start_year' => 2021, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Eclipse Cross', 'generation' => '1st', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'Outlander Sport', 'generation' => '2nd', 'start_year' => 2010, 'body_type' => 'suv', 'segment' => 'subcompact'],
                ['name' => 'Eclipse', 'generation' => '4th', 'start_year' => 2005, 'body_type' => 'coupe', 'segment' => 'sports'],
            ],
            
            'Lexus' => [
                ['name' => 'IS', 'generation' => '3rd', 'start_year' => 2013, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'ES', 'generation' => '7th', 'start_year' => 2018, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'GS', 'generation' => '4th', 'start_year' => 2012, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'LS', 'generation' => '5th', 'start_year' => 2017, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'NX', 'generation' => '2nd', 'start_year' => 2021, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'RX', 'generation' => '4th', 'start_year' => 2015, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'GX', 'generation' => '2nd', 'start_year' => 2009, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'LX', 'generation' => '3rd', 'start_year' => 2021, 'body_type' => 'suv', 'segment' => 'fullsize'],
                ['name' => 'LC', 'generation' => '1st', 'start_year' => 2017, 'body_type' => 'coupe', 'segment' => 'luxury'],
                ['name' => 'RC', 'generation' => '1st', 'start_year' => 2014, 'body_type' => 'coupe', 'segment' => 'sports'],
            ],
            
            'Infiniti' => [
                ['name' => 'Q50', 'generation' => '1st', 'start_year' => 2013, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'Q60', 'generation' => '1st', 'start_year' => 2016, 'body_type' => 'coupe', 'segment' => 'sports'],
                ['name' => 'Q70', 'generation' => '2nd', 'start_year' => 2013, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'QX50', 'generation' => '2nd', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'QX60', 'generation' => '2nd', 'start_year' => 2021, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'QX80', 'generation' => '2nd', 'start_year' => 2017, 'body_type' => 'suv', 'segment' => 'fullsize'],
            ],
            
            'Acura' => [
                ['name' => 'ILX', 'generation' => '1st', 'start_year' => 2012, 'body_type' => 'sedan', 'segment' => 'compact'],
                ['name' => 'TLX', 'generation' => '2nd', 'start_year' => 2020, 'body_type' => 'sedan', 'segment' => 'midsize'],
                ['name' => 'RLX', 'generation' => '1st', 'start_year' => 2013, 'body_type' => 'sedan', 'segment' => 'luxury'],
                ['name' => 'RDX', 'generation' => '3rd', 'start_year' => 2018, 'body_type' => 'suv', 'segment' => 'compact'],
                ['name' => 'MDX', 'generation' => '4th', 'start_year' => 2021, 'body_type' => 'suv', 'segment' => 'midsize'],
                ['name' => 'NSX', 'generation' => '2nd', 'start_year' => 2016, 'body_type' => 'coupe', 'segment' => 'sports'],
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
                            'slug' => Str::slug($brand->name . ' ' . $modelData['name']),
                            'start_year' => $modelData['start_year'],
                            'end_year' => $modelData['end_year'] ?? null,
                            'body_type' => $modelData['body_type'],
                            'segment' => $modelData['segment'],
                            'engine_options' => $this->getEngineOptions($modelData['body_type']),
                            'transmission_options' => ['manual', 'automatic', 'cvt'],
                            'fuel_types' => $this->getFuelTypes($modelData['body_type']),
                            'specifications' => $this->getSpecifications($modelData['body_type']),
                            'description' => $this->getDescription($brand->name, $modelData['name']),
                            'is_active' => true,
                            'is_popular' => $this->isPopularModel($modelData['name']),
                            'sort_order' => 0,
                        ]
                    );
                }
            }
        }
        
        $this->command->info('Complete car models seeded successfully!');
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
}

