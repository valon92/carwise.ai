<?php

namespace App\Services;

use App\Events\PriceUpdated;
use App\Models\CarPart;
use Illuminate\Support\Facades\Log;

class PriceUpdateService
{
    /**
     * Update the price for a given part.
     *
     * @param int $partId
     * @param float $newPrice
     * @param float|null $oldPrice
     * @return CarPart|null
     */
    public function updatePrice(int $partId, float $newPrice, ?float $oldPrice = null): ?CarPart
    {
        $part = CarPart::find($partId);

        if (!$part) {
            Log::warning("CarPart with ID {$partId} not found for price update.");
            return null;
        }

        $oldPrice = $oldPrice ?? $part->price;
        $part->price = $newPrice;
        $part->save();

        Log::info("Price updated for part {$partId}: from {$oldPrice} to {$newPrice}");

        // Dispatch event for real-time update
        event(new PriceUpdated($partId, $newPrice, $oldPrice));

        return $part;
    }

    /**
     * Simulate random price changes for a few parts.
     *
     * @param int $count
     * @return array
     */
    public function simulateRandomPriceChanges(int $count = 5): array
    {
        $updatedParts = [];
        $parts = CarPart::inRandomOrder()->limit($count)->get();

        foreach ($parts as $part) {
            $oldPrice = $part->price;
            
            // Simulate price changes: -20% to +15%
            $changePercentage = rand(-20, 15) / 100;
            $newPrice = round($oldPrice * (1 + $changePercentage), 2);
            
            // Ensure minimum price of $1.00
            $newPrice = max(1.00, $newPrice);
            
            $updatedParts[] = $this->updatePrice($part->id, $newPrice, $oldPrice);
        }

        Log::info("Simulated random price changes for " . count($updatedParts) . " parts.");

        return array_filter($updatedParts);
    }

    /**
     * Apply seasonal price adjustments.
     *
     * @param string $season
     * @return array
     */
    public function applySeasonalPricing(string $season): array
    {
        $updatedParts = [];
        
        // Define seasonal categories and their price multipliers
        $seasonalAdjustments = [
            'winter' => [
                'battery' => 1.15,      // Batteries more expensive in winter
                'antifreeze' => 1.20,   // Antifreeze in high demand
                'tire' => 1.10,         // Winter tires
                'heater' => 1.25,       // Heating components
            ],
            'summer' => [
                'air_conditioning' => 1.20, // AC parts more expensive
                'coolant' => 1.15,          // Cooling system parts
                'tire' => 0.95,             // Winter tire clearance
                'battery' => 0.90,          // Less battery issues
            ],
            'spring' => [
                'wiper' => 1.10,        // Spring cleaning, rain season
                'filter' => 1.05,       // Maintenance season
                'oil' => 0.95,          // Off-season for winter oils
            ],
            'fall' => [
                'battery' => 1.05,      // Preparing for winter
                'antifreeze' => 1.10,   // Getting ready for cold
                'tire' => 1.15,         // Winter tire preparation
            ]
        ];

        if (!isset($seasonalAdjustments[$season])) {
            Log::warning("Unknown season: {$season}");
            return [];
        }

        $adjustments = $seasonalAdjustments[$season];

        foreach ($adjustments as $category => $multiplier) {
            $parts = CarPart::where('category', 'LIKE', "%{$category}%")
                           ->orWhere('name', 'LIKE', "%{$category}%")
                           ->limit(10)
                           ->get();

            foreach ($parts as $part) {
                $oldPrice = $part->price;
                $newPrice = round($oldPrice * $multiplier, 2);
                $updatedParts[] = $this->updatePrice($part->id, $newPrice, $oldPrice);
            }
        }

        Log::info("Applied {$season} seasonal pricing to " . count($updatedParts) . " parts.");

        return array_filter($updatedParts);
    }

    /**
     * Apply market-based price fluctuations.
     *
     * @param array $marketFactors
     * @return array
     */
    public function applyMarketFluctuations(array $marketFactors = []): array
    {
        $updatedParts = [];
        
        // Default market factors if none provided
        $defaultFactors = [
            'supply_chain_disruption' => 0.15,  // 15% increase due to supply issues
            'raw_material_cost' => 0.08,        // 8% increase in raw materials
            'fuel_price_impact' => 0.05,        // 5% shipping cost increase
            'seasonal_demand' => -0.03,         // 3% decrease due to low demand
        ];

        $factors = array_merge($defaultFactors, $marketFactors);
        $totalImpact = array_sum($factors);

        // Apply to random selection of parts
        $parts = CarPart::inRandomOrder()->limit(20)->get();

        foreach ($parts as $part) {
            $oldPrice = $part->price;
            
            // Apply market impact with some randomness
            $randomFactor = (rand(-5, 5) / 100); // ±5% random variation
            $priceMultiplier = 1 + $totalImpact + $randomFactor;
            
            $newPrice = round($oldPrice * $priceMultiplier, 2);
            $newPrice = max(1.00, $newPrice); // Minimum price
            
            $updatedParts[] = $this->updatePrice($part->id, $newPrice, $oldPrice);
        }

        Log::info("Applied market fluctuations to " . count($updatedParts) . " parts. Total impact: " . ($totalImpact * 100) . "%");

        return array_filter($updatedParts);
    }

    /**
     * Set promotional pricing for specific parts.
     *
     * @param array $partIds
     * @param float $discountPercentage
     * @param int $durationHours
     * @return array
     */
    public function setPromotionalPricing(array $partIds, float $discountPercentage, int $durationHours = 24): array
    {
        $updatedParts = [];
        $discountMultiplier = 1 - ($discountPercentage / 100);

        foreach ($partIds as $partId) {
            $part = CarPart::find($partId);
            if (!$part) continue;

            $oldPrice = $part->price;
            $newPrice = round($oldPrice * $discountMultiplier, 2);
            
            // Store original price for restoration later
            $part->original_price = $oldPrice;
            $part->promotion_end_time = now()->addHours($durationHours);
            
            $updatedParts[] = $this->updatePrice($partId, $newPrice, $oldPrice);
        }

        Log::info("Set promotional pricing for " . count($updatedParts) . " parts. Discount: {$discountPercentage}%");

        return array_filter($updatedParts);
    }

    /**
     * Restore original pricing after promotions end.
     *
     * @return array
     */
    public function restorePromotionalPricing(): array
    {
        $restoredParts = [];
        
        $expiredPromotions = CarPart::whereNotNull('original_price')
                                   ->whereNotNull('promotion_end_time')
                                   ->where('promotion_end_time', '<=', now())
                                   ->get();

        foreach ($expiredPromotions as $part) {
            $currentPrice = $part->price;
            $originalPrice = $part->original_price;
            
            $part->price = $originalPrice;
            $part->original_price = null;
            $part->promotion_end_time = null;
            $part->save();
            
            // Dispatch price update event
            event(new PriceUpdated($part->id, $originalPrice, $currentPrice));
            
            $restoredParts[] = $part;
        }

        if (count($restoredParts) > 0) {
            Log::info("Restored original pricing for " . count($restoredParts) . " parts after promotion expiry.");
        }

        return $restoredParts;
    }

    /**
     * Get price history for a part.
     *
     * @param int $partId
     * @param int $days
     * @return array
     */
    public function getPriceHistory(int $partId, int $days = 30): array
    {
        // This would typically query a price_history table
        // For now, return mock data
        $part = CarPart::find($partId);
        if (!$part) return [];

        $history = [];
        $currentPrice = $part->price;
        
        // Generate mock price history
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $variation = (rand(-10, 10) / 100); // ±10% variation
            $price = round($currentPrice * (1 + $variation), 2);
            
            $history[] = [
                'date' => $date->format('Y-m-d'),
                'price' => $price,
                'change' => $i < $days ? $price - $history[count($history) - 1]['price'] : 0
            ];
        }

        return $history;
    }

    /**
     * Get price statistics for all parts.
     *
     * @return array
     */
    public function getPriceStatistics(): array
    {
        $totalParts = CarPart::count();
        $avgPrice = CarPart::avg('price');
        $minPrice = CarPart::min('price');
        $maxPrice = CarPart::max('price');
        
        // Parts with recent price increases/decreases (mock data)
        $recentIncreases = rand(5, 15);
        $recentDecreases = rand(3, 12);
        
        return [
            'total_parts' => $totalParts,
            'average_price' => round($avgPrice, 2),
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'recent_increases' => $recentIncreases,
            'recent_decreases' => $recentDecreases,
            'price_volatility' => rand(5, 25) / 100, // Mock volatility percentage
            'market_trend' => rand(0, 1) ? 'increasing' : 'decreasing'
        ];
    }
}

