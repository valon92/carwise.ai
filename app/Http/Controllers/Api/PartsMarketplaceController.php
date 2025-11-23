<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PartsMarketplaceAPIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PartsMarketplaceController extends Controller
{
    private $partsMarketplaceService;

    public function __construct(PartsMarketplaceAPIService $partsMarketplaceService)
    {
        $this->partsMarketplaceService = $partsMarketplaceService;
    }

    /**
     * Search parts on eBay Motors
     */
    public function searchEbayMotorsParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
            'sort' => 'nullable|string|in:price_asc,price_desc,relevance,ending_soonest',
            'condition' => 'nullable|string|in:new,used,refurbished',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $searchParams = $request->only([
                'query', 'limit', 'offset', 'sort', 'condition', 'price_min', 'price_max'
            ]);

            $results = $this->partsMarketplaceService->searchEbayMotorsParts($searchParams);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to search eBay Motors parts', [
                'user_id' => Auth::id(),
                'query' => $request->input('query', ''),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search eBay Motors parts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search parts on Amazon
     */
    public function searchAmazonParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'limit' => 'nullable|integer|min:1|max:50',
            'offset' => 'nullable|integer|min:0',
            'sort' => 'nullable|string|in:price_asc,price_desc,relevance',
            'category' => 'nullable|string',
            'brand' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $searchParams = $request->only([
                'query', 'limit', 'offset', 'sort', 'category', 'brand'
            ]);

            $results = $this->partsMarketplaceService->searchAmazonParts($searchParams);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to search Amazon parts', [
                'user_id' => Auth::id(),
                'query' => $request->input('query', ''),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search Amazon parts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search parts on AutoZone
     */
    public function searchAutoZoneParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
            'sort' => 'nullable|string|in:price_asc,price_desc,relevance',
            'category' => 'nullable|string',
            'brand' => 'nullable|string',
            'vehicle_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'vehicle_make' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $searchParams = $request->only([
                'query', 'limit', 'offset', 'sort', 'category', 'brand',
                'vehicle_year', 'vehicle_make', 'vehicle_model'
            ]);

            $results = $this->partsMarketplaceService->searchAutoZoneParts($searchParams);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to search AutoZone parts', [
                'user_id' => Auth::id(),
                'query' => $request->input('query', ''),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search AutoZone parts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search parts on RockAuto
     */
    public function searchRockAutoParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
            'sort' => 'nullable|string|in:price_asc,price_desc,relevance',
            'category' => 'nullable|string',
            'brand' => 'nullable|string',
            'vehicle_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'vehicle_make' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $searchParams = $request->only([
                'query', 'limit', 'offset', 'sort', 'category', 'brand',
                'vehicle_year', 'vehicle_make', 'vehicle_model'
            ]);

            $results = $this->partsMarketplaceService->searchRockAutoParts($searchParams);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to search RockAuto parts', [
                'user_id' => Auth::id(),
                'query' => $request->input('query', ''),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search RockAuto parts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search parts on PartsGeek
     */
    public function searchPartsGeekParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
            'sort' => 'nullable|string|in:price_asc,price_desc,relevance',
            'type' => 'nullable|string|in:oem,aftermarket,all',
            'brand' => 'nullable|string',
            'vehicle_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'vehicle_make' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $searchParams = $request->only([
                'query', 'limit', 'offset', 'sort', 'type', 'brand',
                'vehicle_year', 'vehicle_make', 'vehicle_model'
            ]);

            $results = $this->partsMarketplaceService->searchPartsGeekParts($searchParams);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to search PartsGeek parts', [
                'user_id' => Auth::id(),
                'query' => $request->input('query', ''),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search PartsGeek parts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search parts across all marketplaces
     */
    public function searchAllMarketplaces(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
            'sort' => 'nullable|string|in:price_asc,price_desc,relevance',
            'category' => 'nullable|string',
            'brand' => 'nullable|string',
            'type' => 'nullable|string|in:oem,aftermarket,all',
            'vehicle_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'vehicle_make' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $searchParams = $request->only([
                'query', 'limit', 'offset', 'sort', 'category', 'brand', 'type',
                'vehicle_year', 'vehicle_make', 'vehicle_model', 'price_min', 'price_max'
            ]);

            $results = $this->partsMarketplaceService->searchAllMarketplaces($searchParams);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to search all marketplaces', [
                'user_id' => Auth::id(),
                'query' => $request->input('query', ''),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search all marketplaces',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supported marketplaces
     */
    public function getSupportedMarketplaces(): JsonResponse
    {
        try {
            $marketplaces = $this->partsMarketplaceService->getSupportedMarketplaces();
            $status = $this->partsMarketplaceService->getStatus();

            $marketplaceInfo = [];
            foreach ($marketplaces as $marketplace) {
                $config = $this->partsMarketplaceService->getMarketplaceConfig($marketplace);
                $marketplaceInfo[] = [
                    'name' => $marketplace,
                    'display_name' => ucwords(str_replace('_', ' ', $marketplace)),
                    'enabled' => $status[$marketplace]['enabled'] ?? false,
                    'configured' => $status[$marketplace]['configured'] ?? false,
                    'api_url' => $status[$marketplace]['base_url'] ?? ''
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'marketplaces' => $marketplaceInfo,
                    'total_count' => count($marketplaces)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get supported marketplaces', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve supported marketplaces',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test marketplace API connection
     */
    public function testMarketplaceAPI(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'marketplace' => 'required|string|in:ebay_motors,amazon_paapi,autozone,rockauto,partsgeek',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $marketplace = $request->marketplace;
            $result = $this->partsMarketplaceService->testMarketplaceAPI($marketplace);

            return response()->json([
                'success' => true,
                'data' => [
                    'marketplace' => $marketplace,
                    'connection_test' => $result ? 'success' : 'failed',
                    'status' => $result ? 'API connection successful' : 'API connection failed'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to test marketplace API', [
                'user_id' => Auth::id(),
                'marketplace' => $request->marketplace,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to test marketplace API',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get marketplace API status
     */
    public function getAPIStatus(): JsonResponse
    {
        try {
            $status = $this->partsMarketplaceService->getStatus();

            return response()->json([
                'success' => true,
                'data' => [
                    'marketplaces' => $status,
                    'total_marketplaces' => count($status),
                    'enabled_marketplaces' => count(array_filter($status, fn($s) => $s['enabled'])),
                    'configured_marketplaces' => count(array_filter($status, fn($s) => $s['configured']))
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get marketplace API status', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve marketplace API status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get marketplace API documentation
     */
    public function getAPIDocumentation(): JsonResponse
    {
        try {
            $documentation = [
                'ebay_motors' => [
                    'name' => 'eBay Motors API',
                    'description' => 'Parts marketplace for new and used automotive parts',
                    'website' => 'https://developer.ebay.com/api-docs/commerce/parts-compatibility',
                    'documentation_url' => 'https://developer.ebay.com/api-docs/commerce/parts-compatibility',
                    'authentication' => 'OAuth 2.0',
                    'rate_limits' => '5000 requests/day',
                    'features' => [
                        'Parts search and discovery',
                        'Parts compatibility checking',
                        'Price comparison',
                        'Seller ratings and feedback',
                        'Shipping information',
                        'Condition filtering (new, used, refurbished)'
                    ],
                    'supported_parts' => [
                        'Engine parts',
                        'Transmission parts',
                        'Brake components',
                        'Suspension parts',
                        'Electrical components',
                        'Body parts',
                        'Interior parts',
                        'Accessories'
                    ]
                ],
                'amazon_paapi' => [
                    'name' => 'Amazon Product Advertising API',
                    'description' => 'Original parts for many car brands through Amazon marketplace',
                    'website' => 'https://webservices.amazon.com/paapi5/documentation/',
                    'documentation_url' => 'https://webservices.amazon.com/paapi5/documentation/',
                    'authentication' => 'AWS Signature V4',
                    'rate_limits' => '8640 requests/day',
                    'features' => [
                        'Product search and discovery',
                        'Price comparison',
                        'Product details and specifications',
                        'Customer reviews and ratings',
                        'Availability information',
                        'Prime shipping eligibility'
                    ],
                    'supported_parts' => [
                        'OEM parts',
                        'Aftermarket parts',
                        'Accessories',
                        'Tools and equipment',
                        'Maintenance supplies',
                        'Performance parts'
                    ]
                ],
                'autozone' => [
                    'name' => 'AutoZone API',
                    'description' => 'Replacement parts and online sales platform',
                    'website' => 'https://www.autozone.com',
                    'documentation_url' => 'https://developer.autozone.com',
                    'authentication' => 'API Key',
                    'rate_limits' => '1000 requests/hour',
                    'features' => [
                        'Parts search and catalog',
                        'Vehicle compatibility',
                        'Store locator',
                        'Inventory checking',
                        'Price comparison',
                        'Professional services'
                    ],
                    'supported_parts' => [
                        'Engine parts',
                        'Brake systems',
                        'Suspension components',
                        'Electrical parts',
                        'Filters and fluids',
                        'Tools and equipment'
                    ]
                ],
                'rockauto' => [
                    'name' => 'RockAuto Data Services',
                    'description' => 'Global parts catalog with comprehensive coverage',
                    'website' => 'https://www.rockauto.com',
                    'documentation_url' => 'https://developer.rockauto.com',
                    'authentication' => 'API Key',
                    'rate_limits' => '2000 requests/hour',
                    'features' => [
                        'Global parts catalog',
                        'Vehicle-specific parts',
                        'Brand and category filtering',
                        'Price comparison',
                        'Shipping information',
                        'Parts compatibility'
                    ],
                    'supported_parts' => [
                        'All major automotive parts',
                        'OEM and aftermarket',
                        'Performance parts',
                        'Maintenance items',
                        'Tools and equipment',
                        'Accessories'
                    ]
                ],
                'partsgeek' => [
                    'name' => 'PartsGeek API',
                    'description' => 'OEM and aftermarket parts marketplace',
                    'website' => 'https://www.partsgeek.com',
                    'documentation_url' => 'https://developer.partsgeek.com',
                    'authentication' => 'API Key',
                    'rate_limits' => '1500 requests/hour',
                    'features' => [
                        'OEM and aftermarket parts',
                        'Vehicle compatibility',
                        'Price comparison',
                        'Brand filtering',
                        'Category browsing',
                        'Shipping options'
                    ],
                    'supported_parts' => [
                        'OEM parts',
                        'Aftermarket parts',
                        'Performance parts',
                        'Maintenance supplies',
                        'Accessories',
                        'Tools and equipment'
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'documentation' => $documentation,
                    'total_marketplaces' => count($documentation),
                    'total_features' => array_sum(array_map(fn($d) => count($d['features']), $documentation))
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get marketplace API documentation', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve marketplace API documentation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PartsMarketplaceAPIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PartsMarketplaceController extends Controller
{
    private $partsMarketplaceService;

    public function __construct(PartsMarketplaceAPIService $partsMarketplaceService)
    {
        $this->partsMarketplaceService = $partsMarketplaceService;
    }

    /**
     * Search parts on eBay Motors
     */
    public function searchEbayMotorsParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
            'sort' => 'nullable|string|in:price_asc,price_desc,relevance,ending_soonest',
            'condition' => 'nullable|string|in:new,used,refurbished',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $searchParams = $request->only([
                'query', 'limit', 'offset', 'sort', 'condition', 'price_min', 'price_max'
            ]);

            $results = $this->partsMarketplaceService->searchEbayMotorsParts($searchParams);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to search eBay Motors parts', [
                'user_id' => Auth::id(),
                'query' => $request->input('query', ''),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search eBay Motors parts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search parts on Amazon
     */
    public function searchAmazonParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'limit' => 'nullable|integer|min:1|max:50',
            'offset' => 'nullable|integer|min:0',
            'sort' => 'nullable|string|in:price_asc,price_desc,relevance',
            'category' => 'nullable|string',
            'brand' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $searchParams = $request->only([
                'query', 'limit', 'offset', 'sort', 'category', 'brand'
            ]);

            $results = $this->partsMarketplaceService->searchAmazonParts($searchParams);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to search Amazon parts', [
                'user_id' => Auth::id(),
                'query' => $request->input('query', ''),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search Amazon parts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search parts on AutoZone
     */
    public function searchAutoZoneParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
            'sort' => 'nullable|string|in:price_asc,price_desc,relevance',
            'category' => 'nullable|string',
            'brand' => 'nullable|string',
            'vehicle_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'vehicle_make' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $searchParams = $request->only([
                'query', 'limit', 'offset', 'sort', 'category', 'brand',
                'vehicle_year', 'vehicle_make', 'vehicle_model'
            ]);

            $results = $this->partsMarketplaceService->searchAutoZoneParts($searchParams);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to search AutoZone parts', [
                'user_id' => Auth::id(),
                'query' => $request->input('query', ''),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search AutoZone parts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search parts on RockAuto
     */
    public function searchRockAutoParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
            'sort' => 'nullable|string|in:price_asc,price_desc,relevance',
            'category' => 'nullable|string',
            'brand' => 'nullable|string',
            'vehicle_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'vehicle_make' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $searchParams = $request->only([
                'query', 'limit', 'offset', 'sort', 'category', 'brand',
                'vehicle_year', 'vehicle_make', 'vehicle_model'
            ]);

            $results = $this->partsMarketplaceService->searchRockAutoParts($searchParams);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to search RockAuto parts', [
                'user_id' => Auth::id(),
                'query' => $request->input('query', ''),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search RockAuto parts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search parts on PartsGeek
     */
    public function searchPartsGeekParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
            'sort' => 'nullable|string|in:price_asc,price_desc,relevance',
            'type' => 'nullable|string|in:oem,aftermarket,all',
            'brand' => 'nullable|string',
            'vehicle_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'vehicle_make' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $searchParams = $request->only([
                'query', 'limit', 'offset', 'sort', 'type', 'brand',
                'vehicle_year', 'vehicle_make', 'vehicle_model'
            ]);

            $results = $this->partsMarketplaceService->searchPartsGeekParts($searchParams);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to search PartsGeek parts', [
                'user_id' => Auth::id(),
                'query' => $request->input('query', ''),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search PartsGeek parts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search parts across all marketplaces
     */
    public function searchAllMarketplaces(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
            'sort' => 'nullable|string|in:price_asc,price_desc,relevance',
            'category' => 'nullable|string',
            'brand' => 'nullable|string',
            'type' => 'nullable|string|in:oem,aftermarket,all',
            'vehicle_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'vehicle_make' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $searchParams = $request->only([
                'query', 'limit', 'offset', 'sort', 'category', 'brand', 'type',
                'vehicle_year', 'vehicle_make', 'vehicle_model', 'price_min', 'price_max'
            ]);

            $results = $this->partsMarketplaceService->searchAllMarketplaces($searchParams);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to search all marketplaces', [
                'user_id' => Auth::id(),
                'query' => $request->input('query', ''),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search all marketplaces',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supported marketplaces
     */
    public function getSupportedMarketplaces(): JsonResponse
    {
        try {
            $marketplaces = $this->partsMarketplaceService->getSupportedMarketplaces();
            $status = $this->partsMarketplaceService->getStatus();

            $marketplaceInfo = [];
            foreach ($marketplaces as $marketplace) {
                $config = $this->partsMarketplaceService->getMarketplaceConfig($marketplace);
                $marketplaceInfo[] = [
                    'name' => $marketplace,
                    'display_name' => ucwords(str_replace('_', ' ', $marketplace)),
                    'enabled' => $status[$marketplace]['enabled'] ?? false,
                    'configured' => $status[$marketplace]['configured'] ?? false,
                    'api_url' => $status[$marketplace]['base_url'] ?? ''
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'marketplaces' => $marketplaceInfo,
                    'total_count' => count($marketplaces)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get supported marketplaces', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve supported marketplaces',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test marketplace API connection
     */
    public function testMarketplaceAPI(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'marketplace' => 'required|string|in:ebay_motors,amazon_paapi,autozone,rockauto,partsgeek',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $marketplace = $request->marketplace;
            $result = $this->partsMarketplaceService->testMarketplaceAPI($marketplace);

            return response()->json([
                'success' => true,
                'data' => [
                    'marketplace' => $marketplace,
                    'connection_test' => $result ? 'success' : 'failed',
                    'status' => $result ? 'API connection successful' : 'API connection failed'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to test marketplace API', [
                'user_id' => Auth::id(),
                'marketplace' => $request->marketplace,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to test marketplace API',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get marketplace API status
     */
    public function getAPIStatus(): JsonResponse
    {
        try {
            $status = $this->partsMarketplaceService->getStatus();

            return response()->json([
                'success' => true,
                'data' => [
                    'marketplaces' => $status,
                    'total_marketplaces' => count($status),
                    'enabled_marketplaces' => count(array_filter($status, fn($s) => $s['enabled'])),
                    'configured_marketplaces' => count(array_filter($status, fn($s) => $s['configured']))
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get marketplace API status', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve marketplace API status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get marketplace API documentation
     */
    public function getAPIDocumentation(): JsonResponse
    {
        try {
            $documentation = [
                'ebay_motors' => [
                    'name' => 'eBay Motors API',
                    'description' => 'Parts marketplace for new and used automotive parts',
                    'website' => 'https://developer.ebay.com/api-docs/commerce/parts-compatibility',
                    'documentation_url' => 'https://developer.ebay.com/api-docs/commerce/parts-compatibility',
                    'authentication' => 'OAuth 2.0',
                    'rate_limits' => '5000 requests/day',
                    'features' => [
                        'Parts search and discovery',
                        'Parts compatibility checking',
                        'Price comparison',
                        'Seller ratings and feedback',
                        'Shipping information',
                        'Condition filtering (new, used, refurbished)'
                    ],
                    'supported_parts' => [
                        'Engine parts',
                        'Transmission parts',
                        'Brake components',
                        'Suspension parts',
                        'Electrical components',
                        'Body parts',
                        'Interior parts',
                        'Accessories'
                    ]
                ],
                'amazon_paapi' => [
                    'name' => 'Amazon Product Advertising API',
                    'description' => 'Original parts for many car brands through Amazon marketplace',
                    'website' => 'https://webservices.amazon.com/paapi5/documentation/',
                    'documentation_url' => 'https://webservices.amazon.com/paapi5/documentation/',
                    'authentication' => 'AWS Signature V4',
                    'rate_limits' => '8640 requests/day',
                    'features' => [
                        'Product search and discovery',
                        'Price comparison',
                        'Product details and specifications',
                        'Customer reviews and ratings',
                        'Availability information',
                        'Prime shipping eligibility'
                    ],
                    'supported_parts' => [
                        'OEM parts',
                        'Aftermarket parts',
                        'Accessories',
                        'Tools and equipment',
                        'Maintenance supplies',
                        'Performance parts'
                    ]
                ],
                'autozone' => [
                    'name' => 'AutoZone API',
                    'description' => 'Replacement parts and online sales platform',
                    'website' => 'https://www.autozone.com',
                    'documentation_url' => 'https://developer.autozone.com',
                    'authentication' => 'API Key',
                    'rate_limits' => '1000 requests/hour',
                    'features' => [
                        'Parts search and catalog',
                        'Vehicle compatibility',
                        'Store locator',
                        'Inventory checking',
                        'Price comparison',
                        'Professional services'
                    ],
                    'supported_parts' => [
                        'Engine parts',
                        'Brake systems',
                        'Suspension components',
                        'Electrical parts',
                        'Filters and fluids',
                        'Tools and equipment'
                    ]
                ],
                'rockauto' => [
                    'name' => 'RockAuto Data Services',
                    'description' => 'Global parts catalog with comprehensive coverage',
                    'website' => 'https://www.rockauto.com',
                    'documentation_url' => 'https://developer.rockauto.com',
                    'authentication' => 'API Key',
                    'rate_limits' => '2000 requests/hour',
                    'features' => [
                        'Global parts catalog',
                        'Vehicle-specific parts',
                        'Brand and category filtering',
                        'Price comparison',
                        'Shipping information',
                        'Parts compatibility'
                    ],
                    'supported_parts' => [
                        'All major automotive parts',
                        'OEM and aftermarket',
                        'Performance parts',
                        'Maintenance items',
                        'Tools and equipment',
                        'Accessories'
                    ]
                ],
                'partsgeek' => [
                    'name' => 'PartsGeek API',
                    'description' => 'OEM and aftermarket parts marketplace',
                    'website' => 'https://www.partsgeek.com',
                    'documentation_url' => 'https://developer.partsgeek.com',
                    'authentication' => 'API Key',
                    'rate_limits' => '1500 requests/hour',
                    'features' => [
                        'OEM and aftermarket parts',
                        'Vehicle compatibility',
                        'Price comparison',
                        'Brand filtering',
                        'Category browsing',
                        'Shipping options'
                    ],
                    'supported_parts' => [
                        'OEM parts',
                        'Aftermarket parts',
                        'Performance parts',
                        'Maintenance supplies',
                        'Accessories',
                        'Tools and equipment'
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'documentation' => $documentation,
                    'total_marketplaces' => count($documentation),
                    'total_features' => array_sum(array_map(fn($d) => count($d['features']), $documentation))
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get marketplace API documentation', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve marketplace API documentation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
