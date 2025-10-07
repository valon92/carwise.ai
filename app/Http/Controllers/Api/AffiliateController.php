<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AffiliateClick;
use App\Models\AffiliateCommission;
use App\Models\CarPart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AffiliateController extends Controller
{
    /**
     * Track affiliate click
     */
    public function trackClick(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'part_id' => 'required|string',
                'brand' => 'required|string',
                'category' => 'required|string',
                'user_agent' => 'nullable|string',
                'referrer' => 'nullable|string',
                'timestamp' => 'required|date'
            ]);

            // Get part details (for external parts, we'll use the provided data)
            $part = CarPart::find($validated['part_id']);
            if (!$part) {
                // For external parts (eBay, Amazon, etc.), create a temporary part object
                $part = (object) [
                    'id' => $validated['part_id'],
                    'name' => $validated['brand'] . ' ' . $validated['category'],
                    'brand' => $validated['brand'],
                    'category' => $validated['category']
                ];
            }

            // Create affiliate click record
            $click = AffiliateClick::create([
                'part_id' => $validated['part_id'],
                'brand' => $validated['brand'],
                'category' => $validated['category'],
                'user_agent' => $validated['user_agent'],
                'referrer' => $validated['referrer'],
                'ip_address' => $request->ip(),
                'session_id' => session()->getId(),
                'click_id' => Str::uuid(),
                'timestamp' => $validated['timestamp']
            ]);

            // Generate affiliate link
            $affiliateLink = $this->generateAffiliateLink($part, $click->click_id);

            return response()->json([
                'success' => true,
                'data' => [
                    'click_id' => $click->click_id,
                    'affiliate_link' => $affiliateLink,
                    'commission_rate' => $this->getCommissionRate($part->brand, $part->category)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error tracking affiliate click: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error tracking click'], 500);
        }
    }

    /**
     * Track affiliate purchase
     */
    public function trackPurchase(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'click_id' => 'required|string',
                'part_id' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|size:3',
                'order_id' => 'required|string',
                'customer_email' => 'required|email'
            ]);

            // Find the original click
            $click = AffiliateClick::where('click_id', $validated['click_id'])->first();
            if (!$click) {
                return response()->json(['success' => false, 'message' => 'Click not found'], 404);
            }

            // Get part details
            $part = CarPart::find($validated['part_id']);
            if (!$part) {
                return response()->json(['success' => false, 'message' => 'Part not found'], 404);
            }

            // Calculate commission
            $commissionRate = $this->getCommissionRate($part->brand, $part->category);
            $commissionAmount = $validated['amount'] * ($commissionRate / 100);

            // Create commission record
            $commission = AffiliateCommission::create([
                'click_id' => $validated['click_id'],
                'part_id' => $validated['part_id'],
                'brand' => $part->brand,
                'category' => $part->category,
                'order_id' => $validated['order_id'],
                'customer_email' => $validated['customer_email'],
                'purchase_amount' => $validated['amount'],
                'currency' => $validated['currency'],
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'status' => 'pending',
                'purchase_date' => now()
            ]);

            // Update click record
            $click->update([
                'converted' => true,
                'conversion_date' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'commission_id' => $commission->id,
                    'commission_amount' => $commissionAmount,
                    'commission_rate' => $commissionRate,
                    'status' => 'pending'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error tracking affiliate purchase: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error tracking purchase'], 500);
        }
    }

    /**
     * Get affiliate statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $stats = [
                'total_clicks' => AffiliateClick::count(),
                'total_conversions' => AffiliateClick::where('converted', true)->count(),
                'conversion_rate' => 0,
                'total_commission' => AffiliateCommission::sum('commission_amount'),
                'pending_commission' => AffiliateCommission::where('status', 'pending')->sum('commission_amount'),
                'paid_commission' => AffiliateCommission::where('status', 'paid')->sum('commission_amount'),
                'top_brands' => $this->getTopBrands(),
                'top_categories' => $this->getTopCategories(),
                'monthly_stats' => $this->getMonthlyStats()
            ];

            // Calculate conversion rate
            if ($stats['total_clicks'] > 0) {
                $stats['conversion_rate'] = round(($stats['total_conversions'] / $stats['total_clicks']) * 100, 2);
            }

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting affiliate stats: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error getting stats'], 500);
        }
    }

    /**
     * Generate affiliate link
     */
    private function generateAffiliateLink(CarPart $part, string $clickId): string
    {
        $baseUrl = config('app.affiliate_base_url', 'https://partners.carwise.ai');
        $params = http_build_query([
            'click_id' => $clickId,
            'part_id' => $part->id,
            'brand' => $part->brand,
            'category' => $part->category,
            'source' => 'carwise_ai'
        ]);

        return "{$baseUrl}/buy/{$part->id}?{$params}";
    }

    /**
     * Get commission rate based on brand and category
     */
    private function getCommissionRate(string $brand, string $category): float
    {
        // Premium brands get higher commission rates
        $premiumBrands = ['BMW', 'Mercedes-Benz', 'Audi', 'Porsche', 'Ferrari', 'Lamborghini', 'Bentley', 'Rolls-Royce'];
        $isPremiumBrand = in_array($brand, $premiumBrands);

        // High-value categories get higher commission rates
        $highValueCategories = ['engine', 'transmission', 'brakes', 'suspension'];
        $isHighValueCategory = in_array($category, $highValueCategories);

        // Base commission rates
        if ($isPremiumBrand && $isHighValueCategory) {
            return 8.0; // 8% for premium brands in high-value categories
        } elseif ($isPremiumBrand) {
            return 6.0; // 6% for premium brands
        } elseif ($isHighValueCategory) {
            return 5.0; // 5% for high-value categories
        } else {
            return 3.0; // 3% base rate
        }
    }

    /**
     * Get top performing brands
     */
    private function getTopBrands(): array
    {
        return AffiliateClick::selectRaw('brand, COUNT(*) as clicks, SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions')
            ->groupBy('brand')
            ->orderByDesc('clicks')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get top performing categories
     */
    private function getTopCategories(): array
    {
        return AffiliateClick::selectRaw('category, COUNT(*) as clicks, SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions')
            ->groupBy('category')
            ->orderByDesc('clicks')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get monthly statistics
     */
    private function getMonthlyStats(): array
    {
        return AffiliateClick::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as clicks,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100 as conversion_rate
            ')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }
}
