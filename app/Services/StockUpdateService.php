<?php

namespace App\Services;

use App\Models\Car;
use App\Http\Controllers\WebSocketController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StockUpdateService
{
    protected $webSocketController;
    protected $lowStockThreshold = 5;
    protected $criticalStockThreshold = 2;

    public function __construct()
    {
        // In a real implementation, this would be injected or managed differently
        $this->webSocketController = app(WebSocketController::class);
    }

    /**
     * Update stock for a specific part
     */
    public function updatePartStock(string $partId, int $newQuantity, string $source = 'system'): bool
    {
        try {
            // Get previous quantity from cache or database
            $previousQuantity = $this->getPreviousStock($partId);
            
            // Update stock in database (assuming you have a parts table)
            DB::table('car_parts')->where('id', $partId)->update([
                'stock_quantity' => $newQuantity,
                'updated_at' => now()
            ]);
            
            // Cache the new quantity
            Cache::put("stock_{$partId}", $newQuantity, now()->addHours(24));
            
            // Prepare stock update data
            $stockData = [
                'part_id' => $partId,
                'stock_quantity' => $newQuantity,
                'previous_quantity' => $previousQuantity,
                'source' => $source,
                'timestamp' => now()->toISOString()
            ];
            
            // Broadcast the update
            $this->webSocketController->broadcastStockUpdate($stockData);
            
            // Check for alerts
            $this->checkStockAlerts($partId, $newQuantity, $previousQuantity);
            
            Log::info("Stock updated for part {$partId}: {$previousQuantity} -> {$newQuantity}");
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to update stock for part {$partId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Bulk update stock for multiple parts
     */
    public function bulkUpdateStock(array $stockUpdates): array
    {
        $results = [];
        $broadcastUpdates = [];
        
        foreach ($stockUpdates as $update) {
            $partId = $update['part_id'];
            $newQuantity = $update['quantity'];
            $source = $update['source'] ?? 'bulk_update';
            
            try {
                $previousQuantity = $this->getPreviousStock($partId);
                
                // Update in database
                DB::table('car_parts')->where('id', $partId)->update([
                    'stock_quantity' => $newQuantity,
                    'updated_at' => now()
                ]);
                
                // Cache the new quantity
                Cache::put("stock_{$partId}", $newQuantity, now()->addHours(24));
                
                $stockData = [
                    'part_id' => $partId,
                    'stock_quantity' => $newQuantity,
                    'previous_quantity' => $previousQuantity,
                    'source' => $source,
                    'timestamp' => now()->toISOString()
                ];
                
                $broadcastUpdates[] = $stockData;
                $results[$partId] = ['success' => true, 'data' => $stockData];
                
                // Check for alerts
                $this->checkStockAlerts($partId, $newQuantity, $previousQuantity);
                
            } catch (\Exception $e) {
                Log::error("Failed to update stock for part {$partId}: " . $e->getMessage());
                $results[$partId] = ['success' => false, 'error' => $e->getMessage()];
            }
        }
        
        // Broadcast all updates at once
        if (!empty($broadcastUpdates)) {
            $this->webSocketController->broadcastBulkStockUpdate($broadcastUpdates);
        }
        
        return $results;
    }

    /**
     * Simulate stock changes (for demo purposes)
     */
    public function simulateStockChanges(): void
    {
        // Get some random parts to update
        $parts = DB::table('car_parts')->inRandomOrder()->limit(5)->get();
        
        foreach ($parts as $part) {
            $currentStock = $part->stock_quantity ?? 10;
            
            // Simulate random stock changes
            $change = rand(-3, 5); // Can go down by 3 or up by 5
            $newStock = max(0, $currentStock + $change);
            
            $this->updatePartStock($part->id, $newStock, 'simulation');
            
            // Small delay between updates
            usleep(500000); // 0.5 seconds
        }
    }

    /**
     * Check if stock levels trigger alerts
     */
    protected function checkStockAlerts(string $partId, int $newQuantity, int $previousQuantity): void
    {
        // Only send alerts for stock decreases
        if ($newQuantity >= $previousQuantity) {
            return;
        }
        
        $alertData = [
            'part_id' => $partId,
            'stock_quantity' => $newQuantity,
            'previous_quantity' => $previousQuantity,
            'timestamp' => now()->toISOString()
        ];
        
        if ($newQuantity <= $this->criticalStockThreshold) {
            $alertData['type'] = 'critical';
            $alertData['message'] = "Critical stock level: Only {$newQuantity} left for part {$partId}!";
            
            $this->webSocketController->broadcastStockAlert($alertData);
            
        } elseif ($newQuantity <= $this->lowStockThreshold) {
            $alertData['type'] = 'low';
            $alertData['message'] = "Low stock: Only {$newQuantity} left for part {$partId}";
            
            $this->webSocketController->broadcastStockAlert($alertData);
        }
    }

    /**
     * Get previous stock quantity
     */
    protected function getPreviousStock(string $partId): int
    {
        // Try cache first
        $cached = Cache::get("stock_{$partId}");
        if ($cached !== null) {
            return $cached;
        }
        
        // Fallback to database
        $part = DB::table('car_parts')->where('id', $partId)->first();
        return $part ? ($part->stock_quantity ?? 0) : 0;
    }

    /**
     * Get current stock for a part
     */
    public function getCurrentStock(string $partId): int
    {
        return $this->getPreviousStock($partId);
    }

    /**
     * Reserve stock for a purchase
     */
    public function reserveStock(string $partId, int $quantity): bool
    {
        try {
            $currentStock = $this->getCurrentStock($partId);
            
            if ($currentStock < $quantity) {
                return false; // Not enough stock
            }
            
            $newStock = $currentStock - $quantity;
            return $this->updatePartStock($partId, $newStock, 'reservation');
            
        } catch (\Exception $e) {
            Log::error("Failed to reserve stock for part {$partId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Release reserved stock (e.g., when order is cancelled)
     */
    public function releaseStock(string $partId, int $quantity): bool
    {
        try {
            $currentStock = $this->getCurrentStock($partId);
            $newStock = $currentStock + $quantity;
            
            return $this->updatePartStock($partId, $newStock, 'release');
            
        } catch (\Exception $e) {
            Log::error("Failed to release stock for part {$partId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get stock statistics
     */
    public function getStockStatistics(): array
    {
        try {
            $stats = DB::table('car_parts')
                ->selectRaw('
                    COUNT(*) as total_parts,
                    SUM(stock_quantity) as total_stock,
                    AVG(stock_quantity) as avg_stock,
                    COUNT(CASE WHEN stock_quantity = 0 THEN 1 END) as out_of_stock,
                    COUNT(CASE WHEN stock_quantity <= ? THEN 1 END) as critical_stock,
                    COUNT(CASE WHEN stock_quantity <= ? THEN 1 END) as low_stock
                ', [$this->criticalStockThreshold, $this->lowStockThreshold])
                ->first();
                
            return [
                'total_parts' => $stats->total_parts ?? 0,
                'total_stock' => $stats->total_stock ?? 0,
                'average_stock' => round($stats->avg_stock ?? 0, 2),
                'out_of_stock_count' => $stats->out_of_stock ?? 0,
                'critical_stock_count' => $stats->critical_stock ?? 0,
                'low_stock_count' => $stats->low_stock ?? 0,
                'thresholds' => [
                    'critical' => $this->criticalStockThreshold,
                    'low' => $this->lowStockThreshold
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error("Failed to get stock statistics: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Set stock thresholds
     */
    public function setStockThresholds(int $critical, int $low): void
    {
        $this->criticalStockThreshold = $critical;
        $this->lowStockThreshold = $low;
        
        // Cache the thresholds
        Cache::put('stock_thresholds', [
            'critical' => $critical,
            'low' => $low
        ], now()->addDays(30));
    }
}

