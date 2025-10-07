<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarPart;
use App\Services\PriceUpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PriceController extends Controller
{
    protected $priceUpdateService;

    public function __construct(PriceUpdateService $priceUpdateService)
    {
        $this->priceUpdateService = $priceUpdateService;
    }

    /**
     * Get current price for a specific part.
     *
     * @param int $partId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPrice(int $partId)
    {
        $part = CarPart::find($partId);

        if (!$part) {
            return response()->json(['success' => false, 'message' => 'Part not found'], 404);
        }

        return response()->json(['success' => true, 'data' => [
            'part_id' => $part->id,
            'current_price' => $part->price,
            'formatted_price' => '$' . number_format($part->price, 2),
            'last_updated' => $part->updated_at,
        ]]);
    }

    /**
     * Update price for a specific part.
     *
     * @param Request $request
     * @param int $partId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePrice(Request $request, int $partId)
    {
        $validator = Validator::make($request->all(), [
            'new_price' => 'required|numeric|min:0.01',
            'old_price' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $part = $this->priceUpdateService->updatePrice(
                $partId, 
                $request->new_price, 
                $request->old_price
            );

            if (!$part) {
                return response()->json(['success' => false, 'message' => 'Part not found or update failed'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Price updated successfully', 'data' => [
                'part_id' => $part->id,
                'new_price' => $part->price,
                'formatted_price' => '$' . number_format($part->price, 2),
            ]]);
        } catch (\Exception $e) {
            Log::error("Error updating price for part {$partId}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Perform bulk price updates.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkUpdatePrices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'updates' => 'required|array',
            'updates.*.part_id' => 'required|integer|exists:car_parts,id',
            'updates.*.new_price' => 'required|numeric|min:0.01',
            'updates.*.old_price' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $results = [];
        foreach ($request->updates as $update) {
            try {
                $part = $this->priceUpdateService->updatePrice(
                    $update['part_id'], 
                    $update['new_price'], 
                    $update['old_price'] ?? null
                );
                $results[] = [
                    'part_id' => $update['part_id'],
                    'success' => (bool)$part,
                    'new_price' => $part ? $part->price : null,
                    'message' => $part ? 'Updated' : 'Part not found or update failed',
                ];
            } catch (\Exception $e) {
                Log::error("Error in bulk price update for part {$update['part_id']}: " . $e->getMessage());
                $results[] = [
                    'part_id' => $update['part_id'],
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ];
            }
        }

        return response()->json(['success' => true, 'message' => 'Bulk price update processed', 'data' => $results]);
    }

    /**
     * Simulate random price changes.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function simulateChanges(Request $request)
    {
        $count = $request->input('count', 5);
        $updatedParts = $this->priceUpdateService->simulateRandomPriceChanges($count);

        return response()->json([
            'success' => true, 
            'message' => 'Price changes simulated', 
            'data' => $updatedParts->map(function($part) {
                return [
                    'part_id' => $part->id,
                    'name' => $part->name,
                    'new_price' => $part->price,
                    'formatted_price' => '$' . number_format($part->price, 2),
                ];
            })
        ]);
    }

    /**
     * Apply seasonal pricing.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applySeasonalPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'season' => 'required|in:winter,spring,summer,fall',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $updatedParts = $this->priceUpdateService->applySeasonalPricing($request->season);

        return response()->json([
            'success' => true, 
            'message' => "Applied {$request->season} seasonal pricing", 
            'data' => collect($updatedParts)->map(function($part) {
                return [
                    'part_id' => $part->id,
                    'name' => $part->name,
                    'new_price' => $part->price,
                    'formatted_price' => '$' . number_format($part->price, 2),
                ];
            })
        ]);
    }

    /**
     * Apply market fluctuations.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyMarketFluctuations(Request $request)
    {
        $marketFactors = $request->input('market_factors', []);
        $updatedParts = $this->priceUpdateService->applyMarketFluctuations($marketFactors);

        return response()->json([
            'success' => true, 
            'message' => 'Applied market fluctuations', 
            'data' => collect($updatedParts)->map(function($part) {
                return [
                    'part_id' => $part->id,
                    'name' => $part->name,
                    'new_price' => $part->price,
                    'formatted_price' => '$' . number_format($part->price, 2),
                ];
            })
        ]);
    }

    /**
     * Set promotional pricing.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPromotionalPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'part_ids' => 'required|array',
            'part_ids.*' => 'integer|exists:car_parts,id',
            'discount_percentage' => 'required|numeric|min:1|max:90',
            'duration_hours' => 'sometimes|integer|min:1|max:168', // Max 1 week
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $updatedParts = $this->priceUpdateService->setPromotionalPricing(
            $request->part_ids,
            $request->discount_percentage,
            $request->input('duration_hours', 24)
        );

        return response()->json([
            'success' => true, 
            'message' => 'Promotional pricing set', 
            'data' => collect($updatedParts)->map(function($part) {
                return [
                    'part_id' => $part->id,
                    'name' => $part->name,
                    'new_price' => $part->price,
                    'original_price' => $part->original_price,
                    'formatted_price' => '$' . number_format($part->price, 2),
                    'promotion_end_time' => $part->promotion_end_time,
                ];
            })
        ]);
    }

    /**
     * Get price history for a part.
     *
     * @param int $partId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPriceHistory(int $partId, Request $request)
    {
        $days = $request->input('days', 30);
        $history = $this->priceUpdateService->getPriceHistory($partId, $days);

        if (empty($history)) {
            return response()->json(['success' => false, 'message' => 'Part not found or no history available'], 404);
        }

        return response()->json(['success' => true, 'data' => $history]);
    }

    /**
     * Get price statistics overview.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        $statistics = $this->priceUpdateService->getPriceStatistics();

        return response()->json(['success' => true, 'data' => $statistics]);
    }

    /**
     * Restore promotional pricing.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restorePromotionalPricing()
    {
        $restoredParts = $this->priceUpdateService->restorePromotionalPricing();

        return response()->json([
            'success' => true, 
            'message' => 'Promotional pricing restored', 
            'data' => [
                'restored_count' => count($restoredParts),
                'parts' => collect($restoredParts)->map(function($part) {
                    return [
                        'part_id' => $part->id,
                        'name' => $part->name,
                        'restored_price' => $part->price,
                        'formatted_price' => '$' . number_format($part->price, 2),
                    ];
                })
            ]
        ]);
    }

    /**
     * Get parts with active promotions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActivePromotions()
    {
        $promotionalParts = CarPart::whereNotNull('original_price')
                                  ->whereNotNull('promotion_end_time')
                                  ->where('promotion_end_time', '>', now())
                                  ->get();

        return response()->json([
            'success' => true, 
            'data' => $promotionalParts->map(function($part) {
                $discountPercentage = (($part->original_price - $part->price) / $part->original_price) * 100;
                return [
                    'part_id' => $part->id,
                    'name' => $part->name,
                    'current_price' => $part->price,
                    'original_price' => $part->original_price,
                    'formatted_current_price' => '$' . number_format($part->price, 2),
                    'formatted_original_price' => '$' . number_format($part->original_price, 2),
                    'discount_percentage' => round($discountPercentage, 1),
                    'promotion_end_time' => $part->promotion_end_time,
                    'time_remaining' => $part->promotion_end_time->diffForHumans(),
                ];
            })
        ]);
    }
}

