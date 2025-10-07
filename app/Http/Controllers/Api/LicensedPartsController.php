<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LicensedPartsAPIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LicensedPartsController extends Controller
{
    protected $licensedPartsService;

    public function __construct(LicensedPartsAPIService $licensedPartsService)
    {
        $this->licensedPartsService = $licensedPartsService;
    }

    /**
     * Search parts across all licensed providers
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'category' => 'nullable|string|max:50',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        try {
            $filters = $request->only(['make', 'model', 'year', 'category', 'price_min', 'price_max', 'limit']);
            $results = $this->licensedPartsService->searchLicensedParts($request->query, $filters);

            return response()->json([
                'success' => true,
                'data' => $results,
                'total_providers' => count($results),
                'total_parts' => array_sum(array_column($results, 'count'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching licensed parts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get AI-powered part suggestions
     */
    public function getAISuggestions(Request $request): JsonResponse
    {
        $request->validate([
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'issue' => 'nullable|string|max:100'
        ]);

        try {
            $suggestions = $this->licensedPartsService->getAIPartSuggestions(
                $request->make,
                $request->model,
                $request->year,
                $request->issue
            );

            return response()->json([
                'success' => true,
                'data' => $suggestions,
                'vehicle' => [
                    'make' => $request->make,
                    'model' => $request->model,
                    'year' => $request->year
                ],
                'issue' => $request->issue
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting AI suggestions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get part details from specific provider
     */
    public function getPartDetails(Request $request, string $providerId, string $partNumber): JsonResponse
    {
        try {
            $details = $this->licensedPartsService->getPartDetails($providerId, $partNumber);

            return response()->json([
                'success' => true,
                'data' => $details,
                'provider' => $providerId,
                'part_number' => $partNumber
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting part details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get provider statistics
     */
    public function getProviderStats(): JsonResponse
    {
        try {
            $stats = $this->licensedPartsService->getProviderStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'total_providers' => count($stats)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting provider stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get parts by vehicle compatibility
     */
    public function getPartsByVehicle(Request $request): JsonResponse
    {
        $request->validate([
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'category' => 'nullable|string|max:50',
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        try {
            $filters = $request->only(['make', 'model', 'year', 'category', 'limit']);
            $query = "{$request->make} {$request->model} {$request->year}";
            
            $results = $this->licensedPartsService->searchLicensedParts($query, $filters);

            return response()->json([
                'success' => true,
                'data' => $results,
                'vehicle' => [
                    'make' => $request->make,
                    'model' => $request->model,
                    'year' => $request->year
                ],
                'total_providers' => count($results),
                'total_parts' => array_sum(array_column($results, 'count'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting parts by vehicle: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get parts by category
     */
    public function getPartsByCategory(Request $request, string $category): JsonResponse
    {
        $request->validate([
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        try {
            $filters = $request->only(['make', 'model', 'year', 'limit']);
            $filters['category'] = $category;
            
            $query = $category;
            if ($request->make && $request->model && $request->year) {
                $query = "{$request->make} {$request->model} {$request->year} {$category}";
            }
            
            $results = $this->licensedPartsService->searchLicensedParts($query, $filters);

            return response()->json([
                'success' => true,
                'data' => $results,
                'category' => $category,
                'vehicle' => $request->only(['make', 'model', 'year']),
                'total_providers' => count($results),
                'total_parts' => array_sum(array_column($results, 'count'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting parts by category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get popular parts
     */
    public function getPopularParts(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        try {
            $limit = $request->get('limit', 20);
            $popularQueries = [
                'oil filter',
                'air filter',
                'brake pads',
                'spark plugs',
                'battery',
                'alternator',
                'starter',
                'water pump',
                'thermostat',
                'timing belt'
            ];

            $allResults = [];
            foreach ($popularQueries as $query) {
                $results = $this->licensedPartsService->searchLicensedParts($query, ['limit' => 5]);
                foreach ($results as $providerId => $providerResults) {
                    if (!isset($allResults[$providerId])) {
                        $allResults[$providerId] = [
                            'provider' => $providerResults['provider'],
                            'parts' => []
                        ];
                    }
                    $allResults[$providerId]['parts'] = array_merge(
                        $allResults[$providerId]['parts'],
                        array_slice($providerResults['parts'], 0, 2)
                    );
                }
            }

            // Limit results
            foreach ($allResults as $providerId => &$providerResults) {
                $providerResults['parts'] = array_slice($providerResults['parts'], 0, $limit);
                $providerResults['count'] = count($providerResults['parts']);
            }

            return response()->json([
                'success' => true,
                'data' => $allResults,
                'total_providers' => count($allResults),
                'total_parts' => array_sum(array_column($allResults, 'count'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting popular parts: ' . $e->getMessage()
            ], 500);
        }
    }
}

