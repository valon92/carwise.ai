<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PartnerAPIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PartnerController extends Controller
{
    protected $partnerService;

    public function __construct(PartnerAPIService $partnerService)
    {
        $this->partnerService = $partnerService;
    }

    /**
     * Search parts across all partners
     */
    public function searchParts(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'query' => 'required|string|min:2',
                'category' => 'nullable|string',
                'brand' => 'nullable|string',
                'price_min' => 'nullable|numeric|min:0',
                'price_max' => 'nullable|numeric|min:0',
                'in_stock' => 'nullable|boolean'
            ]);

            $filters = array_filter([
                'category' => $validated['category'] ?? null,
                'brand' => $validated['brand'] ?? null,
                'price_min' => $validated['price_min'] ?? null,
                'price_max' => $validated['price_max'] ?? null,
                'in_stock' => $validated['in_stock'] ?? null
            ]);

            $results = $this->partnerService->searchParts($validated['query'], $filters);

            return response()->json([
                'success' => true,
                'data' => $results,
                'total_partners' => count($results),
                'total_parts' => array_sum(array_column($results, 'count'))
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching parts from partners: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error searching parts from partners'
            ], 500);
        }
    }

    /**
     * Get part details from specific partner
     */
    public function getPartDetails(Request $request, string $partnerId, string $partId): JsonResponse
    {
        try {
            $details = $this->partnerService->getPartDetails($partnerId, $partId);

            if (!$details) {
                return response()->json([
                    'success' => false,
                    'message' => 'Part not found or partner not available'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $details
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting part details from {$partnerId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting part details'
            ], 500);
        }
    }

    /**
     * Get part availability from partner
     */
    public function getPartAvailability(Request $request, string $partnerId, string $partId): JsonResponse
    {
        try {
            $zipCode = $request->get('zip_code');
            $availability = $this->partnerService->getPartAvailability($partnerId, $partId, $zipCode);

            if (!$availability) {
                return response()->json([
                    'success' => false,
                    'message' => 'Availability information not available'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $availability
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting part availability from {$partnerId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting availability information'
            ], 500);
        }
    }

    /**
     * Get part pricing from partner
     */
    public function getPartPricing(Request $request, string $partnerId, string $partId): JsonResponse
    {
        try {
            $pricing = $this->partnerService->getPartPricing($partnerId, $partId);

            if (!$pricing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pricing information not available'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $pricing
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting part pricing from {$partnerId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting pricing information'
            ], 500);
        }
    }

    /**
     * Generate affiliate link for partner
     */
    public function generateAffiliateLink(Request $request, string $partnerId, string $partId): JsonResponse
    {
        try {
            $params = $request->only(['campaign', 'source', 'user_id']);
            $affiliateLink = $this->partnerService->generateAffiliateLink($partnerId, $partId, $params);

            if (!$affiliateLink) {
                return response()->json([
                    'success' => false,
                    'message' => 'Affiliate link not available for this partner'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'affiliate_link' => $affiliateLink,
                    'partner_id' => $partnerId,
                    'part_id' => $partId
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error generating affiliate link for {$partnerId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating affiliate link'
            ], 500);
        }
    }

    /**
     * Sync parts from all partners
     */
    public function syncParts(): JsonResponse
    {
        try {
            $results = $this->partnerService->syncPartsFromPartners();

            $successful = array_filter($results, function ($result) {
                return $result['success'] ?? false;
            });

            $failed = array_filter($results, function ($result) {
                return !($result['success'] ?? false);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'sync_results' => $results,
                    'summary' => [
                        'total_partners' => count($results),
                        'successful_syncs' => count($successful),
                        'failed_syncs' => count($failed),
                        'total_parts_synced' => array_sum(array_column($successful, 'parts_synced'))
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error syncing parts from partners: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing parts from partners'
            ], 500);
        }
    }

    /**
     * Sync parts from specific partner
     */
    public function syncPartnerParts(string $partnerId): JsonResponse
    {
        try {
            $result = $this->partnerService->syncPartnerParts($partnerId);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Sync failed'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error("Error syncing parts from partner {$partnerId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing parts from partner'
            ], 500);
        }
    }

    /**
     * Get partner statistics
     */
    public function getPartnerStats(): JsonResponse
    {
        try {
            $stats = $this->partnerService->getPartnerStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting partner stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting partner statistics'
            ], 500);
        }
    }

    /**
     * Get available partners
     */
    public function getPartners(): JsonResponse
    {
        try {
            $partners = $this->partnerService->getPartnerStats();

            return response()->json([
                'success' => true,
                'data' => $partners
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting partners: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting partners'
            ], 500);
        }
    }

    /**
     * Compare prices across partners for a specific part
     */
    public function comparePrices(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'part_name' => 'required|string',
                'brand' => 'nullable|string',
                'part_number' => 'nullable|string'
            ]);

            $query = $validated['part_name'];
            if ($validated['brand']) {
                $query .= ' ' . $validated['brand'];
            }
            if ($validated['part_number']) {
                $query .= ' ' . $validated['part_number'];
            }

            $results = $this->partnerService->searchParts($query);

            // Extract pricing information
            $priceComparison = [];
            foreach ($results as $partnerId => $partnerData) {
                foreach ($partnerData['parts'] as $part) {
                    $priceComparison[] = [
                        'partner_id' => $partnerId,
                        'partner_name' => $partnerData['partner']['name'],
                        'part_name' => $part['name'] ?? 'Unknown',
                        'brand' => $part['brand'] ?? 'Unknown',
                        'part_number' => $part['part_number'] ?? '',
                        'price' => $part['price'] ?? 0,
                        'currency' => $part['currency'] ?? 'USD',
                        'stock' => $part['stock_quantity'] ?? 0,
                        'rating' => $part['rating'] ?? 0,
                        'affiliate_link' => $part['affiliate_link'] ?? ''
                    ];
                }
            }

            // Sort by price
            usort($priceComparison, function ($a, $b) {
                return $a['price'] <=> $b['price'];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'price_comparison' => $priceComparison,
                    'total_options' => count($priceComparison),
                    'price_range' => [
                        'min' => min(array_column($priceComparison, 'price')),
                        'max' => max(array_column($priceComparison, 'price'))
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error comparing prices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error comparing prices across partners'
            ], 500);
        }
    }
}
