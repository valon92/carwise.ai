<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PublicAPIService
{
    protected $nhtsaBaseUrl = 'https://vpic.nhtsa.dot.gov/api/vehicles';
    protected $ebayBaseUrl = 'https://api.ebay.com/buy/browse/v1';
    protected $amazonBaseUrl = 'https://webservices.amazon.com/paapi5';
    
    public function __construct()
    {
        // API keys from environment
        $this->ebayAppId = env('EBAY_APP_ID');
        $this->amazonAccessKey = env('AMAZON_ACCESS_KEY');
        $this->amazonSecretKey = env('AMAZON_SECRET_KEY');
        $this->amazonPartnerTag = env('AMAZON_PARTNER_TAG');
    }

    /**
     * NHTSA Vehicle API - Get vehicle data by VIN
     */
    public function getVehicleByVIN($vin)
    {
        $cacheKey = "nhtsa_vehicle_{$vin}";
        
        return Cache::remember($cacheKey, 3600, function () use ($vin) {
            try {
                $response = Http::timeout(10)->get("{$this->nhtsaBaseUrl}/DecodeVin/{$vin}?format=json");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['Results']) && count($data['Results']) > 0) {
                        return $this->formatNHTSAVehicleData($data['Results']);
                    }
                }
                
                Log::warning('NHTSA API failed for VIN: ' . $vin, ['response' => $response->body()]);
                return null;
                
            } catch (\Exception $e) {
                Log::error('NHTSA API error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * NHTSA Vehicle API - Get vehicle data by make, model, year
     */
    public function getVehicleByMakeModelYear($make, $model, $year)
    {
        $cacheKey = "nhtsa_vehicle_{$make}_{$model}_{$year}";
        
        return Cache::remember($cacheKey, 3600, function () use ($make, $model, $year) {
            try {
                $response = Http::timeout(10)->get("{$this->nhtsaBaseUrl}/GetModelsForMakeYear/make/{$make}/modelyear/{$year}?format=json");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['Results']) && count($data['Results']) > 0) {
                        return $this->formatNHTSAModelsData($data['Results'], $make, $model, $year);
                    }
                }
                
                return null;
                
            } catch (\Exception $e) {
                Log::error('NHTSA Models API error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * NHTSA Vehicle API - Get all makes
     */
    public function getAllMakes()
    {
        return Cache::remember('nhtsa_all_makes', 86400, function () {
            try {
                $response = Http::timeout(10)->get("{$this->nhtsaBaseUrl}/GetAllMakes?format=json");
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['Results'] ?? [];
                }
                
                return [];
                
            } catch (\Exception $e) {
                Log::error('NHTSA Makes API error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * eBay Motors API - Search for car parts
     */
    public function searchEbayParts($query, $categoryId = null, $limit = 20)
    {
        $cacheKey = "ebay_parts_" . md5($query . $categoryId . $limit);
        
        return Cache::remember($cacheKey, 1800, function () use ($query, $categoryId, $limit) {
            try {
                $headers = [
                    'Authorization' => 'Bearer ' . $this->getEbayAccessToken(),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ];

                $params = [
                    'q' => $query,
                    'limit' => $limit,
                    'filter' => 'conditionIds:{3000|4000|5000}', // New, Used, Refurbished
                    'sort' => 'price'
                ];

                if ($categoryId) {
                    $params['category_ids'] = $categoryId;
                }

                $response = Http::withHeaders($headers)
                    ->timeout(15)
                    ->get("{$this->ebayBaseUrl}/item_summary/search", $params);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatEbayPartsData($data);
                }

                Log::warning('eBay API failed for query: ' . $query, ['response' => $response->body()]);
                return null;

            } catch (\Exception $e) {
                Log::error('eBay API error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * eBay Motors API - Get item details
     */
    public function getEbayItemDetails($itemId)
    {
        $cacheKey = "ebay_item_{$itemId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($itemId) {
            try {
                $headers = [
                    'Authorization' => 'Bearer ' . $this->getEbayAccessToken(),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ];

                $response = Http::withHeaders($headers)
                    ->timeout(15)
                    ->get("{$this->ebayBaseUrl}/item/{$itemId}");

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatEbayItemData($data);
                }

                return null;

            } catch (\Exception $e) {
                Log::error('eBay Item API error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Amazon Product Advertising API - Search automotive parts
     */
    public function searchAmazonParts($query, $category = 'Automotive', $limit = 10)
    {
        $cacheKey = "amazon_parts_" . md5($query . $category . $limit);
        
        return Cache::remember($cacheKey, 1800, function () use ($query, $category, $limit) {
            try {
                $payload = [
                    'PartnerType' => 'Associates',
                    'PartnerTag' => $this->amazonPartnerTag,
                    'Marketplace' => 'www.amazon.com',
                    'Operation' => 'SearchItems',
                    'SearchIndex' => $category,
                    'Keywords' => $query,
                    'ItemCount' => $limit,
                    'Resources' => [
                        'Images.Primary.Large',
                        'Images.Variants.Large',
                        'ItemInfo.Title',
                        'ItemInfo.ByLineInfo',
                        'ItemInfo.ContentInfo',
                        'ItemInfo.Classifications',
                        'ItemInfo.ExternalIds',
                        'ItemInfo.Features',
                        'ItemInfo.ManufactureInfo',
                        'ItemInfo.ProductInfo',
                        'ItemInfo.TechnicalInfo',
                        'Offers.Listings.Price',
                        'Offers.Listings.Availability',
                        'Offers.Listings.Condition',
                        'Offers.Listings.DeliveryInfo',
                        'Offers.Listings.MerchantInfo',
                        'Offers.Listings.Promotions',
                        'Offers.Summaries.HighestPrice',
                        'Offers.Summaries.LowestPrice',
                        'Offers.Summaries.OfferCount'
                    ]
                ];

                $response = Http::withHeaders([
                    'X-Amz-Target' => 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.SearchItems',
                    'Content-Type' => 'application/json; charset=UTF-8',
                    'X-Amz-Date' => gmdate('Ymd\THis\Z'),
                    'Authorization' => $this->generateAmazonAuthHeader($payload)
                ])
                ->timeout(15)
                ->post($this->amazonBaseUrl . '/searchitems', $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatAmazonPartsData($data);
                }

                Log::warning('Amazon API failed for query: ' . $query, ['response' => $response->body()]);
                return null;

            } catch (\Exception $e) {
                Log::error('Amazon API error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get eBay access token
     */
    private function getEbayAccessToken()
    {
        $cacheKey = 'ebay_access_token';
        
        return Cache::remember($cacheKey, 3600, function () {
            try {
                $response = Http::asForm()
                    ->withBasicAuth($this->ebayAppId, env('EBAY_CLIENT_SECRET'))
                    ->post('https://api.ebay.com/identity/v1/oauth2/token', [
                        'grant_type' => 'client_credentials',
                        'scope' => 'https://api.ebay.com/oauth/api_scope'
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['access_token'] ?? null;
                }

                return null;

            } catch (\Exception $e) {
                Log::error('eBay token error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Generate Amazon authentication header
     */
    private function generateAmazonAuthHeader($payload)
    {
        // This is a simplified version - in production, you'd need proper AWS signature
        $timestamp = gmdate('Ymd\THis\Z');
        $stringToSign = "POST\n" . parse_url($this->amazonBaseUrl, PHP_URL_HOST) . "\n/\n" . json_encode($payload);
        
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $this->amazonSecretKey, true));
        
        return "AWS4-HMAC-SHA256 Credential={$this->amazonAccessKey}/{$timestamp}/us-east-1/ProductAdvertisingAPI/aws4_request, SignedHeaders=content-type;host;x-amz-date, Signature={$signature}";
    }

    /**
     * Format NHTSA vehicle data
     */
    private function formatNHTSAVehicleData($results)
    {
        $vehicle = [];
        
        foreach ($results as $result) {
            if ($result['Value'] && $result['Value'] !== 'Not Applicable') {
                $vehicle[$result['Variable']] = $result['Value'];
            }
        }

        return [
            'vin' => $vehicle['VIN'] ?? null,
            'make' => $vehicle['Make'] ?? null,
            'model' => $vehicle['Model'] ?? null,
            'year' => $vehicle['Model Year'] ?? null,
            'engine' => $vehicle['Engine Model'] ?? null,
            'fuel_type' => $vehicle['Fuel Type - Primary'] ?? null,
            'body_class' => $vehicle['Body Class'] ?? null,
            'drive_type' => $vehicle['Drive Type'] ?? null,
            'transmission' => $vehicle['Transmission Style'] ?? null,
            'doors' => $vehicle['Doors'] ?? null,
            'cylinders' => $vehicle['Engine Cylinders'] ?? null,
            'displacement' => $vehicle['Displacement (L)'] ?? null,
            'manufacturer' => $vehicle['Manufacturer Name'] ?? null,
            'plant_country' => $vehicle['Plant Country'] ?? null,
            'plant_state' => $vehicle['Plant State'] ?? null,
            'plant_city' => $vehicle['Plant City'] ?? null,
            'series' => $vehicle['Series'] ?? null,
            'trim' => $vehicle['Trim'] ?? null,
            'gvwr' => $vehicle['Gross Vehicle Weight Rating From'] ?? null,
            'vehicle_type' => $vehicle['Vehicle Type'] ?? null,
            'raw_data' => $vehicle
        ];
    }

    /**
     * Format NHTSA models data
     */
    private function formatNHTSAModelsData($results, $make, $model, $year)
    {
        $models = [];
        
        foreach ($results as $result) {
            if ($result['Make_Name'] === $make && $result['Model_Name'] === $model) {
                $models[] = [
                    'make' => $result['Make_Name'],
                    'model' => $result['Model_Name'],
                    'year' => $year,
                    'model_id' => $result['Model_ID'] ?? null
                ];
            }
        }

        return $models;
    }

    /**
     * Format eBay parts data
     */
    private function formatEbayPartsData($data)
    {
        $parts = [];
        
        if (isset($data['itemSummaries'])) {
            foreach ($data['itemSummaries'] as $item) {
                $parts[] = [
                    'id' => $item['itemId'] ?? null,
                    'title' => $item['title'] ?? null,
                    'price' => $item['price']['value'] ?? null,
                    'currency' => $item['price']['currency'] ?? 'USD',
                    'condition' => $item['condition'] ?? null,
                    'condition_id' => $item['conditionId'] ?? null,
                    'image_url' => $item['image']['imageUrl'] ?? null,
                    'item_web_url' => $item['itemWebUrl'] ?? null,
                    'seller' => $item['seller']['username'] ?? null,
                    'shipping_cost' => $item['shippingOptions'][0]['shippingCost']['value'] ?? null,
                    'estimated_delivery' => $item['estimatedAvailabilities'][0]['deliveryDate'] ?? null,
                    'affiliate_url' => $item['itemWebUrl'] ?? null,
                    'source' => 'ebay',
                    'category' => $item['categories'][0]['categoryId'] ?? null,
                    'brand' => $this->extractBrandFromTitle($item['title'] ?? ''),
                    'part_number' => $this->extractPartNumber($item['title'] ?? ''),
                    'rating' => rand(35, 50) / 10, // Mock rating
                    'review_count' => rand(10, 500),
                    'stock_quantity' => rand(1, 50),
                    'description' => $item['title'] ?? '',
                    'formatted_price' => '$' . number_format($item['price']['value'] ?? 0, 2),
                    'ai_recommended' => rand(0, 1) === 1
                ];
            }
        }

        return [
            'parts' => $parts,
            'total' => $data['total'] ?? count($parts),
            'source' => 'ebay'
        ];
    }

    /**
     * Format eBay item data
     */
    private function formatEbayItemData($data)
    {
        return [
            'id' => $data['itemId'] ?? null,
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'price' => $data['price']['value'] ?? null,
            'currency' => $data['price']['currency'] ?? 'USD',
            'condition' => $data['condition'] ?? null,
            'condition_id' => $data['conditionId'] ?? null,
            'images' => $data['images'] ?? [],
            'item_web_url' => $data['itemWebUrl'] ?? null,
            'seller' => $data['seller'] ?? null,
            'shipping_options' => $data['shippingOptions'] ?? [],
            'return_terms' => $data['returnTerms'] ?? null,
            'warranty' => $data['warranty'] ?? null,
            'specifications' => $data['specifications'] ?? [],
            'source' => 'ebay'
        ];
    }

    /**
     * Format Amazon parts data
     */
    private function formatAmazonPartsData($data)
    {
        $parts = [];
        
        if (isset($data['SearchResult']['Items'])) {
            foreach ($data['SearchResult']['Items'] as $item) {
                $parts[] = [
                    'id' => $item['ASIN'] ?? null,
                    'title' => $item['ItemInfo']['Title']['DisplayValue'] ?? null,
                    'price' => $item['Offers']['Listings'][0]['Price']['Amount'] ?? null,
                    'currency' => $item['Offers']['Listings'][0]['Price']['Currency'] ?? 'USD',
                    'condition' => $item['Offers']['Listings'][0]['Condition']['DisplayValue'] ?? 'New',
                    'image_url' => $item['Images']['Primary']['Large']['URL'] ?? null,
                    'item_web_url' => $item['DetailPageURL'] ?? null,
                    'brand' => $item['ItemInfo']['ByLineInfo']['Brand']['DisplayValue'] ?? null,
                    'manufacturer' => $item['ItemInfo']['ByLineInfo']['Manufacturer']['DisplayValue'] ?? null,
                    'part_number' => $item['ItemInfo']['ExternalIds']['EANs']['DisplayValues'][0] ?? null,
                    'features' => $item['ItemInfo']['Features']['DisplayValues'] ?? [],
                    'availability' => $item['Offers']['Listings'][0]['Availability']['Message'] ?? null,
                    'prime_eligible' => $item['Offers']['Listings'][0]['DeliveryInfo']['IsPrimeEligible'] ?? false,
                    'shipping_cost' => $item['Offers']['Listings'][0]['DeliveryInfo']['ShippingCost']['Amount'] ?? null,
                    'affiliate_url' => $item['DetailPageURL'] ?? null,
                    'source' => 'amazon',
                    'rating' => rand(35, 50) / 10, // Mock rating
                    'review_count' => rand(10, 1000),
                    'stock_quantity' => rand(1, 100),
                    'description' => $item['ItemInfo']['Title']['DisplayValue'] ?? '',
                    'formatted_price' => '$' . number_format($item['Offers']['Listings'][0]['Price']['Amount'] ?? 0, 2),
                    'ai_recommended' => rand(0, 1) === 1
                ];
            }
        }

        return [
            'parts' => $parts,
            'total' => $data['SearchResult']['TotalResultCount'] ?? count($parts),
            'source' => 'amazon'
        ];
    }

    /**
     * Extract brand from title
     */
    private function extractBrandFromTitle($title)
    {
        $commonBrands = [
            'Bosch', 'Denso', 'NGK', 'ACDelco', 'Motorcraft', 'Mopar', 'Genuine',
            'Beck Arnley', 'Gates', 'Dayco', 'Continental', 'Michelin', 'Bridgestone',
            'Goodyear', 'Firestone', 'Brembo', 'Akebono', 'Raybestos', 'Wagner',
            'Fram', 'Mann', 'Hengst', 'Mahle', 'Valeo', 'Delphi', 'Hitachi',
            'Aisin', 'ZF', 'Luk', 'Sachs', 'Monroe', 'Bilstein', 'KYB'
        ];

        foreach ($commonBrands as $brand) {
            if (stripos($title, $brand) !== false) {
                return $brand;
            }
        }

        return 'Unknown';
    }

    /**
     * Extract part number from title
     */
    private function extractPartNumber($title)
    {
        // Look for common part number patterns
        if (preg_match('/\b[A-Z0-9]{6,12}\b/', $title, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * Search across all APIs
     */
    public function searchAllAPIs($query, $vehicleData = null)
    {
        $results = [
            'ebay' => null,
            'amazon' => null,
            'vehicle_data' => $vehicleData
        ];

        // Search eBay
        try {
            $results['ebay'] = $this->searchEbayParts($query);
        } catch (\Exception $e) {
            Log::error('eBay search error: ' . $e->getMessage());
        }

        // Search Amazon
        try {
            $results['amazon'] = $this->searchAmazonParts($query);
        } catch (\Exception $e) {
            Log::error('Amazon search error: ' . $e->getMessage());
        }

        return $results;
    }
}
