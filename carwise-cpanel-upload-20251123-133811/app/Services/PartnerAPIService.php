<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PartnerAPIService
{
    protected $partners;

    public function __construct()
    {
        $this->partners = [
            'autozone' => [
                'name' => 'AutoZone',
                'api_url' => 'https://api.autozone.com/v1',
                'api_key' => env('AUTOZONE_API_KEY'),
                'commission_rate' => 5.0,
                'enabled' => true
            ],
        'oreilly' => [
            'name' => 'O\'Reilly Auto Parts',
            'api_url' => 'https://api.oreillyauto.com/v2',
            'api_key' => env('OREILLY_API_KEY'),
            'commission_rate' => 4.5,
            'enabled' => true
        ],
        'advance_auto' => [
            'name' => 'Advance Auto Parts',
            'api_url' => 'https://api.advanceautoparts.com/v1',
            'api_key' => env('ADVANCE_AUTO_API_KEY'),
            'commission_rate' => 4.0,
            'enabled' => true
        ],
        'napa' => [
            'name' => 'NAPA Auto Parts',
            'api_url' => 'https://api.napaonline.com/v1',
            'api_key' => env('NAPA_API_KEY'),
            'commission_rate' => 6.0,
            'enabled' => true
        ],
        'rockauto' => [
            'name' => 'RockAuto',
            'api_url' => 'https://api.rockauto.com/v1',
            'api_key' => env('ROCKAUTO_API_KEY'),
            'commission_rate' => 3.5,
            'enabled' => true
        ],
        'amazon_auto' => [
            'name' => 'Amazon Automotive',
            'api_url' => 'https://api.amazon.com/paapi5',
            'api_key' => env('AMAZON_AUTO_API_KEY'),
            'commission_rate' => 2.5,
            'enabled' => true
        ],
        'ebay_motors' => [
            'name' => 'eBay Motors',
            'api_url' => 'https://api.ebay.com/buy/browse/v1',
            'api_key' => env('EBAY_MOTORS_API_KEY'),
            'commission_rate' => 3.0,
            'enabled' => true
        ],
        'carparts' => [
            'name' => 'CarParts.com',
            'api_url' => 'https://api.carparts.com/v1',
            'api_key' => env('CARPARTS_API_KEY'),
            'commission_rate' => 4.5,
            'enabled' => true
        ],
        'partsgeek' => [
            'name' => 'PartsGeek',
            'api_url' => 'https://api.partsgeek.com/v1',
            'api_key' => env('PARTSGEEK_API_KEY'),
            'commission_rate' => 4.0,
            'enabled' => true
        ],
            'autopartswarehouse' => [
                'name' => 'AutoPartsWarehouse',
                'api_url' => 'https://api.autopartswarehouse.com/v1',
                'api_key' => env('AUTOPARTSWAREHOUSE_API_KEY'),
                'commission_rate' => 3.5,
                'enabled' => true
            ]
        ];
    }

    /**
     * Search parts across all partner APIs
     */
    public function searchParts(string $query, array $filters = []): array
    {
        $results = [];
        $enabledPartners = $this->getEnabledPartners();

        foreach ($enabledPartners as $partnerId => $partner) {
            try {
                $partnerResults = $this->searchPartnerParts($partnerId, $query, $filters);
                if (!empty($partnerResults)) {
                    $results[$partnerId] = [
                        'partner' => $partner,
                        'parts' => $partnerResults,
                        'count' => count($partnerResults)
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Error searching parts from {$partner['name']}: " . $e->getMessage());
                continue;
            }
        }

        return $results;
    }

    /**
     * Search parts from a specific partner
     */
    public function searchPartnerParts(string $partnerId, string $query, array $filters = []): array
    {
        if (!isset($this->partners[$partnerId]) || !$this->partners[$partnerId]['enabled']) {
            return [];
        }

        $partner = $this->partners[$partnerId];
        $cacheKey = "partner_parts_{$partnerId}_" . md5($query . serialize($filters));

        return Cache::remember($cacheKey, 300, function () use ($partner, $query, $filters) {
            return $this->makePartnerRequest($partner, 'search', [
                'query' => $query,
                'filters' => $filters
            ]);
        });
    }

    /**
     * Get part details from partner
     */
    public function getPartDetails(string $partnerId, string $partId): ?array
    {
        if (!isset($this->partners[$partnerId])) {
            return null;
        }

        $partner = $this->partners[$partnerId];
        $cacheKey = "partner_part_details_{$partnerId}_{$partId}";

        return Cache::remember($cacheKey, 600, function () use ($partner, $partId) {
            return $this->makePartnerRequest($partner, 'part-details', [
                'part_id' => $partId
            ]);
        });
    }

    /**
     * Get part availability from partner
     */
    public function getPartAvailability(string $partnerId, string $partId, string $zipCode = null): ?array
    {
        if (!isset($this->partners[$partnerId])) {
            return null;
        }

        $partner = $this->partners[$partnerId];
        $cacheKey = "partner_availability_{$partnerId}_{$partId}_" . ($zipCode ?? 'default');

        return Cache::remember($cacheKey, 60, function () use ($partner, $partId, $zipCode) {
            return $this->makePartnerRequest($partner, 'availability', [
                'part_id' => $partId,
                'zip_code' => $zipCode
            ]);
        });
    }

    /**
     * Get pricing from partner
     */
    public function getPartPricing(string $partnerId, string $partId): ?array
    {
        if (!isset($this->partners[$partnerId])) {
            return null;
        }

        $partner = $this->partners[$partnerId];
        $cacheKey = "partner_pricing_{$partnerId}_{$partId}";

        return Cache::remember($cacheKey, 300, function () use ($partner, $partId) {
            return $this->makePartnerRequest($partner, 'pricing', [
                'part_id' => $partId
            ]);
        });
    }

    /**
     * Generate affiliate link for partner
     */
    public function generateAffiliateLink(string $partnerId, string $partId, array $params = []): string
    {
        if (!isset($this->partners[$partnerId])) {
            return '';
        }

        $partner = $this->partners[$partnerId];
        $baseUrl = $this->getPartnerAffiliateUrl($partnerId);
        
        $affiliateParams = array_merge([
            'part_id' => $partId,
            'source' => 'carwise_ai',
            'campaign' => 'ai_diagnosis',
            'timestamp' => time()
        ], $params);

        return $baseUrl . '?' . http_build_query($affiliateParams);
    }

    /**
     * Sync parts from all partners
     */
    public function syncPartsFromPartners(): array
    {
        $results = [];
        $enabledPartners = $this->getEnabledPartners();

        foreach ($enabledPartners as $partnerId => $partner) {
            try {
                $syncResult = $this->syncPartnerParts($partnerId);
                $results[$partnerId] = $syncResult;
            } catch (\Exception $e) {
                Log::error("Error syncing parts from {$partner['name']}: " . $e->getMessage());
                $results[$partnerId] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Sync parts from specific partner
     */
    public function syncPartnerParts(string $partnerId): array
    {
        if (!isset($this->partners[$partnerId])) {
            return ['success' => false, 'error' => 'Partner not found'];
        }

        $partner = $this->partners[$partnerId];
        
        try {
            $parts = $this->makePartnerRequest($partner, 'sync-parts', []);
            
            // Process and store parts in our database
            $processed = $this->processPartnerParts($partnerId, $parts);
            
            return [
                'success' => true,
                'partner' => $partner['name'],
                'parts_synced' => count($parts),
                'parts_processed' => $processed
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Make request to partner API
     */
    private function makePartnerRequest(array $partner, string $endpoint, array $params): array
    {
        $url = $partner['api_url'] . '/' . $endpoint;
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $partner['api_key'],
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'CarWise-AI/1.0'
        ])->timeout(30)->get($url, $params);

        if (!$response->successful()) {
            throw new \Exception("API request failed: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Get enabled partners
     */
    private function getEnabledPartners(): array
    {
        return array_filter($this->partners, function ($partner) {
            return $partner['enabled'] && !empty($partner['api_key']);
        });
    }

    /**
     * Get partner affiliate URL
     */
    private function getPartnerAffiliateUrl(string $partnerId): string
    {
        $affiliateUrls = [
            'autozone' => 'https://www.autozone.com/parts',
            'oreilly' => 'https://www.oreillyauto.com/parts',
            'advance_auto' => 'https://shop.advanceautoparts.com/parts',
            'napa' => 'https://www.napaonline.com/parts',
            'rockauto' => 'https://www.rockauto.com/parts',
            'amazon_auto' => 'https://www.amazon.com/automotive',
            'ebay_motors' => 'https://www.ebay.com/motors/parts',
            'carparts' => 'https://www.carparts.com/parts',
            'partsgeek' => 'https://www.partsgeek.com/parts',
            'autopartswarehouse' => 'https://www.autopartswarehouse.com/parts'
        ];

        return $affiliateUrls[$partnerId] ?? '';
    }

    /**
     * Process partner parts for our database
     */
    private function processPartnerParts(string $partnerId, array $parts): int
    {
        $processed = 0;
        
        foreach ($parts as $part) {
            try {
                // Transform partner part data to our format
                $transformedPart = $this->transformPartnerPart($partnerId, $part);
                
                // Store or update in our database
                $this->storePartnerPart($transformedPart);
                $processed++;
            } catch (\Exception $e) {
                Log::error("Error processing part from {$partnerId}: " . $e->getMessage());
                continue;
            }
        }

        return $processed;
    }

    /**
     * Transform partner part to our format
     */
    private function transformPartnerPart(string $partnerId, array $partnerPart): array
    {
        return [
            'name' => $partnerPart['name'] ?? $partnerPart['title'] ?? 'Unknown Part',
            'description' => $partnerPart['description'] ?? $partnerPart['summary'] ?? '',
            'brand' => $partnerPart['brand'] ?? $partnerPart['manufacturer'] ?? 'Unknown',
            'part_number' => $partnerPart['part_number'] ?? $partnerPart['sku'] ?? '',
            'category' => $this->mapCategory($partnerPart['category'] ?? ''),
            'price' => $partnerPart['price'] ?? $partnerPart['cost'] ?? 0,
            'currency' => $partnerPart['currency'] ?? 'USD',
            'image_url' => $partnerPart['image_url'] ?? $partnerPart['image'] ?? '',
            'stock_quantity' => $partnerPart['stock'] ?? $partnerPart['quantity'] ?? 0,
            'rating' => $partnerPart['rating'] ?? 0,
            'review_count' => $partnerPart['review_count'] ?? 0,
            'partner_id' => $partnerId,
            'partner_part_id' => $partnerPart['id'] ?? $partnerPart['part_id'] ?? '',
            'affiliate_link' => $partnerPart['affiliate_link'] ?? '',
            'warranty' => $partnerPart['warranty'] ?? '',
            'compatibility' => $partnerPart['compatibility'] ?? [],
            'specifications' => $partnerPart['specifications'] ?? [],
            'ai_recommended' => false, // Will be set by AI analysis
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    /**
     * Map partner category to our category
     */
    private function mapCategory(string $partnerCategory): string
    {
        $categoryMap = [
            'engine' => 'engine',
            'motor' => 'engine',
            'brake' => 'brakes',
            'brakes' => 'brakes',
            'electrical' => 'electrical',
            'electronics' => 'electrical',
            'suspension' => 'suspension',
            'transmission' => 'transmission',
            'exhaust' => 'exhaust',
            'cooling' => 'cooling',
            'fuel' => 'fuel',
            'body' => 'body',
            'interior' => 'interior',
            'exterior' => 'exterior',
            'lighting' => 'lighting',
            'lights' => 'lighting'
        ];

        $lowerCategory = strtolower($partnerCategory);
        return $categoryMap[$lowerCategory] ?? 'other';
    }

    /**
     * Store partner part in database
     */
    private function storePartnerPart(array $part): void
    {
        // This would integrate with your CarPart model
        // For now, we'll just log it
        Log::info('Storing partner part: ' . $part['name']);
    }

    /**
     * Get partner statistics
     */
    public function getPartnerStats(): array
    {
        $stats = [];
        $enabledPartners = $this->getEnabledPartners();

        foreach ($enabledPartners as $partnerId => $partner) {
            $stats[$partnerId] = [
                'name' => $partner['name'],
                'commission_rate' => $partner['commission_rate'],
                'enabled' => $partner['enabled'],
                'last_sync' => Cache::get("partner_last_sync_{$partnerId}"),
                'parts_count' => Cache::get("partner_parts_count_{$partnerId}", 0)
            ];
        }

        return $stats;
    }
}
