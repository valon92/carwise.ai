<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StockUpdateService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class StockController extends Controller
{
    protected $stockService;

    public function __construct(StockUpdateService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Get current stock for a specific part
     */
    public function getStock(Request $request, string $partId): JsonResponse
    {
        try {
            $stock = $this->stockService->getCurrentStock($partId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'part_id' => $partId,
                    'stock_quantity' => $stock,
                    'timestamp' => now()->toISOString()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error("Failed to get stock for part {$partId}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stock information'
            ], 500);
        }
    }

    /**
     * Update stock for a specific part
     */
    public function updateStock(Request $request, string $partId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:0',
            'source' => 'sometimes|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $quantity = $request->input('quantity');
            $source = $request->input('source', 'api_update');
            
            $success = $this->stockService->updatePartStock($partId, $quantity, $source);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock updated successfully',
                    'data' => [
                        'part_id' => $partId,
                        'new_quantity' => $quantity,
                        'source' => $source,
                        'timestamp' => now()->toISOString()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update stock'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error("Failed to update stock for part {$partId}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock'
            ], 500);
        }
    }

    /**
     * Bulk update stock for multiple parts
     */
    public function bulkUpdateStock(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'updates' => 'required|array|min:1',
            'updates.*.part_id' => 'required|string',
            'updates.*.quantity' => 'required|integer|min:0',
            'updates.*.source' => 'sometimes|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updates = $request->input('updates');
            $results = $this->stockService->bulkUpdateStock($updates);
            
            $successCount = count(array_filter($results, fn($r) => $r['success']));
            $totalCount = count($results);
            
            return response()->json([
                'success' => true,
                'message' => "Updated {$successCount} of {$totalCount} parts",
                'data' => [
                    'results' => $results,
                    'summary' => [
                        'total' => $totalCount,
                        'successful' => $successCount,
                        'failed' => $totalCount - $successCount
                    ],
                    'timestamp' => now()->toISOString()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error("Failed to bulk update stock: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update stock'
            ], 500);
        }
    }

    /**
     * Reserve stock for a purchase
     */
    public function reserveStock(Request $request, string $partId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $quantity = $request->input('quantity');
            $success = $this->stockService->reserveStock($partId, $quantity);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock reserved successfully',
                    'data' => [
                        'part_id' => $partId,
                        'reserved_quantity' => $quantity,
                        'timestamp' => now()->toISOString()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available'
                ], 400);
            }
            
        } catch (\Exception $e) {
            Log::error("Failed to reserve stock for part {$partId}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reserve stock'
            ], 500);
        }
    }

    /**
     * Release reserved stock
     */
    public function releaseStock(Request $request, string $partId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $quantity = $request->input('quantity');
            $success = $this->stockService->releaseStock($partId, $quantity);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock released successfully',
                    'data' => [
                        'part_id' => $partId,
                        'released_quantity' => $quantity,
                        'timestamp' => now()->toISOString()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to release stock'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error("Failed to release stock for part {$partId}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to release stock'
            ], 500);
        }
    }

    /**
     * Get stock statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = $this->stockService->getStockStatistics();
            
            return response()->json([
                'success' => true,
                'data' => $statistics,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error("Failed to get stock statistics: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stock statistics'
            ], 500);
        }
    }

    /**
     * Simulate stock changes (for demo purposes)
     */
    public function simulateChanges(): JsonResponse
    {
        try {
            $this->stockService->simulateStockChanges();
            
            return response()->json([
                'success' => true,
                'message' => 'Stock simulation completed',
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error("Failed to simulate stock changes: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to simulate stock changes'
            ], 500);
        }
    }

    /**
     * Update stock thresholds
     */
    public function updateThresholds(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'critical_threshold' => 'required|integer|min:0|max:100',
            'low_threshold' => 'required|integer|min:0|max:100|gte:critical_threshold'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $critical = $request->input('critical_threshold');
            $low = $request->input('low_threshold');
            
            $this->stockService->setStockThresholds($critical, $low);
            
            return response()->json([
                'success' => true,
                'message' => 'Stock thresholds updated successfully',
                'data' => [
                    'critical_threshold' => $critical,
                    'low_threshold' => $low,
                    'timestamp' => now()->toISOString()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error("Failed to update stock thresholds: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock thresholds'
            ], 500);
        }
    }
}

