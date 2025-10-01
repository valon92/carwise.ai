<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PublicAPIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PublicAPIController extends Controller
{
    protected $publicAPIService;

    public function __construct(PublicAPIService $publicAPIService)
    {
        $this->publicAPIService = $publicAPIService;
    }

    /**
     * Get vehicle data by VIN
     */
    public function getVehicleByVIN(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vin' => 'required|string|size:17|regex:/^[A-HJ-NPR-Z0-9]{17}$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid VIN format',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $vehicleData = $this->publicAPIService->getVehicleByVIN($request->vin);

            if ($vehicleData) {
                return response()->json([
                    'success' => true,
                    'data' => $vehicleData,
                    'message' => 'Vehicle data retrieved successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found or VIN is invalid'
            ], 404);

        } catch (\Exception $e) {
            Log::error('VIN lookup error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving vehicle data'
            ], 500);
        }
    }

    /**
     * Get vehicle data by make, model, year
     */
    public function getVehicleByMakeModelYear(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid vehicle parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $vehicleData = $this->publicAPIService->getVehicleByMakeModelYear(
                $request->make,
                $request->model,
                $request->year
            );

            if ($vehicleData) {
                return response()->json([
                    'success' => true,
                    'data' => $vehicleData,
                    'message' => 'Vehicle data retrieved successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Vehicle lookup error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving vehicle data'
            ], 500);
        }
    }

    /**
     * Get all vehicle makes
     */
    public function getAllMakes(): JsonResponse
    {
        try {
            $makes = $this->publicAPIService->getAllMakes();

            return response()->json([
                'success' => true,
                'data' => $makes,
                'message' => 'Vehicle makes retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Makes lookup error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving vehicle makes'
            ], 500);
        }
    }

    /**
     * Search eBay parts
     */
    public function searchEbayParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'category_id' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid search parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $results = $this->publicAPIService->searchEbayParts(
                $request->query,
                $request->category_id,
                $request->limit ?? 20
            );

            if ($results) {
                return response()->json([
                    'success' => true,
                    'data' => $results,
                    'message' => 'eBay parts search completed successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No parts found on eBay'
            ], 404);

        } catch (\Exception $e) {
            Log::error('eBay search error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error searching eBay parts'
            ], 500);
        }
    }

    /**
     * Get eBay item details
     */
    public function getEbayItemDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid item ID',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $itemDetails = $this->publicAPIService->getEbayItemDetails($request->item_id);

            if ($itemDetails) {
                return response()->json([
                    'success' => true,
                    'data' => $itemDetails,
                    'message' => 'eBay item details retrieved successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Item not found on eBay'
            ], 404);

        } catch (\Exception $e) {
            Log::error('eBay item details error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving eBay item details'
            ], 500);
        }
    }

    /**
     * Search Amazon parts
     */
    public function searchAmazonParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'category' => 'nullable|string|max:50',
            'limit' => 'nullable|integer|min:1|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid search parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $results = $this->publicAPIService->searchAmazonParts(
                $request->query,
                $request->category ?? 'Automotive',
                $request->limit ?? 10
            );

            if ($results) {
                return response()->json([
                    'success' => true,
                    'data' => $results,
                    'message' => 'Amazon parts search completed successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No parts found on Amazon'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Amazon search error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error searching Amazon parts'
            ], 500);
        }
    }

    /**
     * Search across all APIs
     */
    public function searchAllAPIs(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'vin' => 'nullable|string|size:17|regex:/^[A-HJ-NPR-Z0-9]{17}$/',
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid search parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $vehicleData = null;

            // Get vehicle data if VIN is provided
            if ($request->vin) {
                $vehicleData = $this->publicAPIService->getVehicleByVIN($request->vin);
            } elseif ($request->make && $request->model && $request->year) {
                $vehicleData = $this->publicAPIService->getVehicleByMakeModelYear(
                    $request->make,
                    $request->model,
                    $request->year
                );
            }

            // Search all APIs
            $results = $this->publicAPIService->searchAllAPIs($request->query, $vehicleData);

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Multi-API search completed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Multi-API search error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error searching across APIs'
            ], 500);
        }
    }

    /**
     * Get API status and configuration
     */
    public function getAPIStatus(): JsonResponse
    {
        $status = [
            'nhtsa' => [
                'enabled' => true,
                'name' => 'NHTSA Vehicle API',
                'description' => 'Free vehicle data from US Department of Transportation',
                'rate_limit' => 'No limit',
                'cost' => 'Free'
            ],
            'ebay' => [
                'enabled' => !empty(env('EBAY_APP_ID')),
                'name' => 'eBay Motors API',
                'description' => 'Access to millions of car parts listings',
                'rate_limit' => '5000 requests/day',
                'cost' => 'Free with affiliate commission'
            ],
            'amazon' => [
                'enabled' => !empty(env('AMAZON_ACCESS_KEY')),
                'name' => 'Amazon Product Advertising API',
                'description' => 'Automotive parts from Amazon marketplace',
                'rate_limit' => '8640 requests/day',
                'cost' => 'Free with affiliate commission'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $status,
            'message' => 'API status retrieved successfully'
        ]);
    }

    /**
     * Get supported categories
     */
    public function getSupportedCategories(): JsonResponse
    {
        $categories = [
            'engine' => [
                'name' => 'Engine Parts',
                'subcategories' => ['Air Filters', 'Oil Filters', 'Spark Plugs', 'Belts', 'Gaskets', 'Pumps']
            ],
            'brakes' => [
                'name' => 'Brake System',
                'subcategories' => ['Brake Pads', 'Brake Rotors', 'Brake Lines', 'Brake Fluid', 'Calipers']
            ],
            'electrical' => [
                'name' => 'Electrical System',
                'subcategories' => ['Batteries', 'Alternators', 'Starters', 'Fuses', 'Wiring', 'Sensors']
            ],
            'suspension' => [
                'name' => 'Suspension & Steering',
                'subcategories' => ['Shocks', 'Struts', 'Springs', 'Control Arms', 'Tie Rods', 'Ball Joints']
            ],
            'transmission' => [
                'name' => 'Transmission',
                'subcategories' => ['Transmission Fluid', 'Filters', 'Gaskets', 'Sensors', 'Solenoids']
            ],
            'exhaust' => [
                'name' => 'Exhaust System',
                'subcategories' => ['Mufflers', 'Catalytic Converters', 'Pipes', 'Headers', 'O2 Sensors']
            ],
            'cooling' => [
                'name' => 'Cooling System',
                'subcategories' => ['Radiators', 'Thermostats', 'Water Pumps', 'Hoses', 'Coolant']
            ],
            'fuel' => [
                'name' => 'Fuel System',
                'subcategories' => ['Fuel Pumps', 'Fuel Filters', 'Injectors', 'Fuel Lines', 'Tanks']
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Supported categories retrieved successfully'
        ]);
    }
}
