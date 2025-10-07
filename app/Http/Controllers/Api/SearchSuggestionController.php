<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SearchSuggestionController extends Controller
{
    /**
     * Get search suggestions based on query
     */
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 10);
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        try {
            // Cache key for suggestions
            $cacheKey = "search_suggestions_{$query}_{$limit}";
            
            $suggestions = Cache::remember($cacheKey, 300, function () use ($query, $limit) {
                return $this->getSuggestions($query, $limit);
            });

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get search suggestions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get search suggestions from multiple sources
     */
    private function getSuggestions($query, $limit)
    {
        $suggestions = [];
        $queryLower = strtolower($query);

        // 1. Car part names (from mock data or database)
        $partSuggestions = $this->getPartSuggestions($queryLower, $limit);
        $suggestions = array_merge($suggestions, $partSuggestions);

        // 2. Car brands
        $brandSuggestions = $this->getBrandSuggestions($queryLower, $limit);
        $suggestions = array_merge($suggestions, $brandSuggestions);

        // 3. Car categories
        $categorySuggestions = $this->getCategorySuggestions($queryLower, $limit);
        $suggestions = array_merge($suggestions, $categorySuggestions);

        // 4. Car makes and models
        $vehicleSuggestions = $this->getVehicleSuggestions($queryLower, $limit);
        $suggestions = array_merge($suggestions, $vehicleSuggestions);

        // 5. Common car part terms
        $termSuggestions = $this->getTermSuggestions($queryLower, $limit);
        $suggestions = array_merge($suggestions, $termSuggestions);

        // Remove duplicates and sort by relevance
        $suggestions = $this->deduplicateAndSort($suggestions, $queryLower);

        return array_slice($suggestions, 0, $limit);
    }

    /**
     * Get part name suggestions
     */
    private function getPartSuggestions($query, $limit)
    {
        $parts = [
            'Air Filter', 'Oil Filter', 'Brake Pads', 'Brake Rotors', 'Spark Plugs',
            'Battery', 'Alternator', 'Starter Motor', 'Timing Belt', 'Water Pump',
            'Radiator', 'Thermostat', 'Fuel Filter', 'Transmission Filter', 'PCV Valve',
            'Mass Air Flow Sensor', 'Oxygen Sensor', 'Catalytic Converter', 'Muffler',
            'Shock Absorbers', 'Struts', 'Control Arms', 'Ball Joints', 'Tie Rod Ends',
            'Wheel Bearings', 'CV Joints', 'Drive Belt', 'Serpentine Belt', 'Power Steering Pump',
            'Steering Rack', 'Steering Pump', 'Brake Calipers', 'Brake Lines', 'Brake Fluid',
            'Engine Oil', 'Transmission Fluid', 'Coolant', 'Power Steering Fluid', 'Windshield Wipers',
            'Headlight Bulbs', 'Taillight Bulbs', 'Turn Signal Bulbs', 'Fog Light Bulbs',
            'Horn', 'Fuse Box', 'Relay', 'Ignition Coil', 'Distributor Cap', 'Rotor',
            'Fuel Pump', 'Fuel Injector', 'Throttle Body', 'Intake Manifold', 'Exhaust Manifold',
            'Turbocharger', 'Supercharger', 'Intercooler', 'Blow-off Valve', 'Wastegate'
        ];

        $suggestions = [];
        foreach ($parts as $part) {
            if (strpos(strtolower($part), $query) !== false) {
                $suggestions[] = [
                    'text' => $part,
                    'type' => 'part',
                    'category' => 'Car Parts',
                    'relevance' => $this->calculateRelevance($part, $query)
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Get brand suggestions
     */
    private function getBrandSuggestions($query, $limit)
    {
        $brands = [
            'Bosch', 'Denso', 'NGK', 'ACDelco', 'Motorcraft', 'Mopar', 'Genuine',
            'Fram', 'Mann-Filter', 'Hengst', 'Mahle', 'Valeo', 'Brembo', 'Akebono',
            'Raybestos', 'Wagner', 'Monroe', 'KYB', 'Bilstein', 'Gabriel', 'Sachs',
            'Luk', 'Valeo', 'Exedy', 'Aisin', 'Gates', 'Dayco', 'Continental',
            'Michelin', 'Bridgestone', 'Goodyear', 'Pirelli', 'Yokohama', 'Hankook',
            'Kumho', 'Toyo', 'Falken', 'Nitto', 'Cooper', 'General', 'Uniroyal',
            'BFGoodrich', 'Firestone', 'Dunlop', 'Maxxis', 'Nexen', 'Vredestein'
        ];

        $suggestions = [];
        foreach ($brands as $brand) {
            if (strpos(strtolower($brand), $query) !== false) {
                $suggestions[] = [
                    'text' => $brand,
                    'type' => 'brand',
                    'category' => 'Brands',
                    'relevance' => $this->calculateRelevance($brand, $query)
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Get category suggestions
     */
    private function getCategorySuggestions($query, $limit)
    {
        $categories = [
            'Engine Parts', 'Brake System', 'Suspension', 'Exhaust System', 'Electrical',
            'Filters', 'Fluids', 'Belts & Hoses', 'Ignition System', 'Fuel System',
            'Cooling System', 'Transmission', 'Steering', 'Wheels & Tires', 'Body Parts',
            'Interior Parts', 'Lighting', 'Sensors', 'Gaskets & Seals', 'Hardware',
            'Tools & Equipment', 'Accessories', 'Performance Parts', 'OEM Parts',
            'Aftermarket Parts', 'Replacement Parts', 'Maintenance Parts'
        ];

        $suggestions = [];
        foreach ($categories as $category) {
            if (strpos(strtolower($category), $query) !== false) {
                $suggestions[] = [
                    'text' => $category,
                    'type' => 'category',
                    'category' => 'Categories',
                    'relevance' => $this->calculateRelevance($category, $query)
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Get vehicle suggestions
     */
    private function getVehicleSuggestions($query, $limit)
    {
        $vehicles = [
            'Toyota Camry', 'Honda Accord', 'Ford F-150', 'Chevrolet Silverado',
            'BMW 3 Series', 'Mercedes-Benz C-Class', 'Audi A4', 'Volkswagen Jetta',
            'Nissan Altima', 'Hyundai Sonata', 'Kia Optima', 'Mazda6', 'Subaru Outback',
            'Honda Civic', 'Toyota Corolla', 'Nissan Sentra', 'Hyundai Elantra',
            'Ford Mustang', 'Chevrolet Camaro', 'Dodge Challenger', 'BMW M3',
            'Mercedes-AMG C63', 'Audi RS4', 'Porsche 911', 'Ferrari 488',
            'Lamborghini Huracan', 'McLaren 720S', 'Aston Martin DB11', 'Bentley Continental',
            'Rolls-Royce Phantom', 'Tesla Model S', 'Tesla Model 3', 'Tesla Model X',
            'Tesla Model Y', 'Rivian R1T', 'Lucid Air', 'Polestar 2'
        ];

        $suggestions = [];
        foreach ($vehicles as $vehicle) {
            if (strpos(strtolower($vehicle), $query) !== false) {
                $suggestions[] = [
                    'text' => $vehicle,
                    'type' => 'vehicle',
                    'category' => 'Vehicles',
                    'relevance' => $this->calculateRelevance($vehicle, $query)
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Get common car part terms
     */
    private function getTermSuggestions($query, $limit)
    {
        $terms = [
            'OEM', 'Aftermarket', 'Performance', 'Heavy Duty', 'Premium',
            'Standard', 'Economy', 'High Performance', 'Racing', 'Street',
            'Track', 'Off-Road', 'Towing', 'Commercial', 'Industrial',
            'Marine', 'Motorcycle', 'ATV', 'UTV', 'Snowmobile',
            'Replacement', 'Upgrade', 'Modification', 'Tuning', 'Custom',
            'Universal', 'Direct Fit', 'Exact Fit', 'OE Quality', 'Genuine',
            'Original', 'Authentic', 'Certified', 'Tested', 'Warranted'
        ];

        $suggestions = [];
        foreach ($terms as $term) {
            if (strpos(strtolower($term), $query) !== false) {
                $suggestions[] = [
                    'text' => $term,
                    'type' => 'term',
                    'category' => 'Terms',
                    'relevance' => $this->calculateRelevance($term, $query)
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Calculate relevance score for suggestions
     */
    private function calculateRelevance($text, $query)
    {
        $textLower = strtolower($text);
        $queryLower = strtolower($query);

        // Exact match gets highest score
        if ($textLower === $queryLower) {
            return 100;
        }

        // Starts with query gets high score
        if (strpos($textLower, $queryLower) === 0) {
            return 90;
        }

        // Contains query gets medium score
        if (strpos($textLower, $queryLower) !== false) {
            return 80;
        }

        // Word boundary match gets lower score
        if (preg_match('/\b' . preg_quote($queryLower, '/') . '\b/', $textLower)) {
            return 70;
        }

        return 0;
    }

    /**
     * Remove duplicates and sort by relevance
     */
    private function deduplicateAndSort($suggestions, $query)
    {
        // Remove duplicates based on text
        $unique = [];
        $seen = [];
        
        foreach ($suggestions as $suggestion) {
            $key = strtolower($suggestion['text']);
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $suggestion;
            }
        }

        // Sort by relevance (descending)
        usort($unique, function ($a, $b) {
            return $b['relevance'] - $a['relevance'];
        });

        return $unique;
    }

    /**
     * Get popular searches
     */
    public function popular(Request $request)
    {
        $limit = $request->get('limit', 10);

        $popular = [
            'Air Filter', 'Oil Filter', 'Brake Pads', 'Spark Plugs', 'Battery',
            'Alternator', 'Timing Belt', 'Water Pump', 'Radiator', 'Shock Absorbers',
            'Brake Rotors', 'Transmission Fluid', 'Engine Oil', 'Windshield Wipers',
            'Headlight Bulbs', 'Fuel Filter', 'PCV Valve', 'Mass Air Flow Sensor',
            'Oxygen Sensor', 'Catalytic Converter'
        ];

        $suggestions = [];
        foreach (array_slice($popular, 0, $limit) as $item) {
            $suggestions[] = [
                'text' => $item,
                'type' => 'popular',
                'category' => 'Popular Searches',
                'relevance' => 100
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $suggestions
        ]);
    }

    /**
     * Get recent searches (if user is authenticated)
     */
    public function recent(Request $request)
    {
        $user = $request->user();
        $limit = $request->get('limit', 10);

        if (!$user) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        // In a real app, you would store recent searches in database
        // For now, return empty array
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }
}


