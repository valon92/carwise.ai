<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PartsMarketplaceAPIService
{
    private $marketplaceConfigs;
    private $defaultTimeout = 30;

    public function __construct()
    {
        $this->marketplaceConfigs = [
            'ebay_motors' => [
                'enabled' => config('services.ebay_motors.enabled', false),
                'app_id' => config('services.ebay_motors.app_id'),
                'client_id' => config('services.ebay_motors.client_id'),
                'client_secret' => config('services.ebay_motors.client_secret'),
                'base_url' => config('services.ebay_motors.base_url', 'https://api.ebay.com'),
                'sandbox_url' => config('services.ebay_motors.sandbox_url', 'https://api.sandbox.ebay.com'),
                'endpoints' => [
                    'oauth_token' => '/identity/v1/oauth2/token',
                    'search_items' => '/buy/browse/v1/item_summary/search',
                    'get_item' => '/buy/browse/v1/item/{item_id}',
                    'parts_compatibility' => '/commerce/parts_compatibility/v1/part_compatibility',
                    'categories' => '/commerce/taxonomy/v1/category_tree/{category_tree_id}'
                ]
            ],
            'amazon_paapi' => [
                'enabled' => config('services.amazon_paapi.enabled', false),
                'access_key' => config('services.amazon_paapi.access_key'),
                'secret_key' => config('services.amazon_paapi.secret_key'),
                'partner_tag' => config('services.amazon_paapi.partner_tag'),
                'host' => config('services.amazon_paapi.host', 'webservices.amazon.com'),
                'region' => config('services.amazon_paapi.region', 'us-east-1'),
                'endpoints' => [
                    'search_items' => '/paapi5/searchitems',
                    'get_items' => '/paapi5/getitems',
                    'get_browse_nodes' => '/paapi5/getbrowsenodes'
                ]
            ],
            'autozone' => [
                'enabled' => config('services.autozone.enabled', false),
                'api_key' => config('services.autozone.api_key'),
                'base_url' => config('services.autozone.base_url', 'https://api.autozone.com'),
                'endpoints' => [
                    'search_parts' => '/v1/parts/search',
                    'get_part_details' => '/v1/parts/{part_number}',
                    'vehicle_compatibility' => '/v1/parts/{part_number}/compatibility',
                    'store_locator' => '/v1/stores/locate',
                    'inventory' => '/v1/inventory/{part_number}'
                ]
            ],
            'rockauto' => [
                'enabled' => config('services.rockauto.enabled', false),
                'api_key' => config('services.rockauto.api_key'),
                'base_url' => config('services.rockauto.base_url', 'https://api.rockauto.com'),
                'endpoints' => [
                    'catalog' => '/v1/catalog',
                    'search_parts' => '/v1/parts/search',
                    'part_details' => '/v1/parts/{part_id}',
                    'vehicle_parts' => '/v1/vehicles/{vehicle_id}/parts',
                    'brands' => '/v1/brands',
                    'categories' => '/v1/categories'
                ]
            ],
            'partsgeek' => [
                'enabled' => config('services.partsgeek.enabled', false),
                'api_key' => config('services.partsgeek.api_key'),
                'base_url' => config('services.partsgeek.base_url', 'https://api.partsgeek.com'),
                'endpoints' => [
                    'search_parts' => '/v1/parts/search',
                    'part_details' => '/v1/parts/{part_number}',
                    'oem_parts' => '/v1/parts/oem',
                    'aftermarket_parts' => '/v1/parts/aftermarket',
                    'vehicle_parts' => '/v1/vehicles/{vehicle_id}/parts',
                    'brands' => '/v1/brands'
                ]
            ]
        ];
    }

    /**
     * Search parts on eBay Motors
     */
    public function searchEbayMotorsParts(array $searchParams): array
    {
        if (!$this->isMarketplaceEnabled('ebay_motors')) {
            return $this->getMockPartsData('ebay_motors', $searchParams);
        }

        $config = $this->marketplaceConfigs['ebay_motors'];
        $endpoint = $config['endpoints']['search_items'];
        $url = $config['base_url'] . $endpoint;

        try {
            // Get OAuth token first
            $accessToken = $this->getEbayOAuthToken();
            if (!$accessToken) {
                return $this->getMockPartsData('ebay_motors', $searchParams);
            }

            $params = array_merge([
                'q' => $searchParams['query'] ?? '',
                'category_ids' => '6028', // eBay Motors category
                'limit' => $searchParams['limit'] ?? 20,
                'offset' => $searchParams['offset'] ?? 0,
                'sort' => $searchParams['sort'] ?? 'price_asc',
                'filter' => 'conditionIds:{3000|4000|5000}', // New, Used, Refurbished
            ], $searchParams);

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('eBay Motors parts search successful', [
                    'query' => $searchParams['query'] ?? '',
                    'results_count' => count($data['itemSummaries'] ?? [])
                ]);

                return $this->normalizeEbayMotorsData($data);
            } else {
                Log::warning('Failed to search eBay Motors parts', [
                    'query' => $searchParams['query'] ?? '',
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockPartsData('ebay_motors', $searchParams);
            }
        } catch (\Exception $e) {
            Log::error('Error searching eBay Motors parts', [
                'query' => $searchParams['query'] ?? '',
                'error' => $e->getMessage()
            ]);

            return $this->getMockPartsData('ebay_motors', $searchParams);
        }
    }

    /**
     * Search parts on Amazon Product Advertising API
     */
    public function searchAmazonParts(array $searchParams): array
    {
        if (!$this->isMarketplaceEnabled('amazon_paapi')) {
            return $this->getMockPartsData('amazon_paapi', $searchParams);
        }

        $config = $this->marketplaceConfigs['amazon_paapi'];
        $endpoint = $config['endpoints']['search_items'];
        $url = 'https://' . $config['host'] . $endpoint;

        try {
            $requestBody = [
                'PartnerTag' => $config['partner_tag'],
                'PartnerType' => 'Associates',
                'Marketplace' => 'www.amazon.com',
                'SearchIndex' => 'Automotive',
                'Keywords' => $searchParams['query'] ?? '',
                'ItemCount' => $searchParams['limit'] ?? 20,
                'ItemPage' => ($searchParams['offset'] ?? 0) / ($searchParams['limit'] ?? 20) + 1,
                'Resources' => [
                    'ItemInfo.Title',
                    'ItemInfo.ByLineInfo',
                    'ItemInfo.Classifications',
                    'ItemInfo.ExternalIds',
                    'ItemInfo.Features',
                    'ItemInfo.ManufactureInfo',
                    'ItemInfo.ProductInfo',
                    'ItemInfo.TechnicalInfo',
                    'Offers.Listings.Price',
                    'Offers.Listings.Availability',
                    'Offers.Listings.MerchantInfo',
                    'Images.Primary.Large',
                    'Images.Variants'
                ]
            ];

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Content-Type' => 'application/json; charset=UTF-8',
                    'X-Amz-Target' => 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.SearchItems',
                    'X-Amz-Date' => gmdate('Ymd\THis\Z'),
                    'Authorization' => $this->getAmazonAuthHeader($requestBody, $config)
                ])
                ->post($url, $requestBody);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Amazon parts search successful', [
                    'query' => $searchParams['query'] ?? '',
                    'results_count' => count($data['SearchResult']['Items'] ?? [])
                ]);

                return $this->normalizeAmazonData($data);
            } else {
                Log::warning('Failed to search Amazon parts', [
                    'query' => $searchParams['query'] ?? '',
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockPartsData('amazon_paapi', $searchParams);
            }
        } catch (\Exception $e) {
            Log::error('Error searching Amazon parts', [
                'query' => $searchParams['query'] ?? '',
                'error' => $e->getMessage()
            ]);

            return $this->getMockPartsData('amazon_paapi', $searchParams);
        }
    }

    /**
     * Search parts on AutoZone
     */
    public function searchAutoZoneParts(array $searchParams): array
    {
        if (!$this->isMarketplaceEnabled('autozone')) {
            return $this->getMockPartsData('autozone', $searchParams);
        }

        $config = $this->marketplaceConfigs['autozone'];
        $endpoint = $config['endpoints']['search_parts'];
        $url = $config['base_url'] . $endpoint;

        try {
            $params = array_merge([
                'q' => $searchParams['query'] ?? '',
                'limit' => $searchParams['limit'] ?? 20,
                'offset' => $searchParams['offset'] ?? 0,
                'sort' => $searchParams['sort'] ?? 'relevance',
                'filters' => $searchParams['filters'] ?? []
            ], $searchParams);

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('AutoZone parts search successful', [
                    'query' => $searchParams['query'] ?? '',
                    'results_count' => count($data['parts'] ?? [])
                ]);

                return $this->normalizeAutoZoneData($data);
            } else {
                Log::warning('Failed to search AutoZone parts', [
                    'query' => $searchParams['query'] ?? '',
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockPartsData('autozone', $searchParams);
            }
        } catch (\Exception $e) {
            Log::error('Error searching AutoZone parts', [
                'query' => $searchParams['query'] ?? '',
                'error' => $e->getMessage()
            ]);

            return $this->getMockPartsData('autozone', $searchParams);
        }
    }

    /**
     * Search parts on RockAuto
     */
    public function searchRockAutoParts(array $searchParams): array
    {
        if (!$this->isMarketplaceEnabled('rockauto')) {
            return $this->getMockPartsData('rockauto', $searchParams);
        }

        $config = $this->marketplaceConfigs['rockauto'];
        $endpoint = $config['endpoints']['search_parts'];
        $url = $config['base_url'] . $endpoint;

        try {
            $params = array_merge([
                'q' => $searchParams['query'] ?? '',
                'limit' => $searchParams['limit'] ?? 20,
                'offset' => $searchParams['offset'] ?? 0,
                'sort' => $searchParams['sort'] ?? 'price_asc',
                'category' => $searchParams['category'] ?? '',
                'brand' => $searchParams['brand'] ?? ''
            ], $searchParams);

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('RockAuto parts search successful', [
                    'query' => $searchParams['query'] ?? '',
                    'results_count' => count($data['parts'] ?? [])
                ]);

                return $this->normalizeRockAutoData($data);
            } else {
                Log::warning('Failed to search RockAuto parts', [
                    'query' => $searchParams['query'] ?? '',
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockPartsData('rockauto', $searchParams);
            }
        } catch (\Exception $e) {
            Log::error('Error searching RockAuto parts', [
                'query' => $searchParams['query'] ?? '',
                'error' => $e->getMessage()
            ]);

            return $this->getMockPartsData('rockauto', $searchParams);
        }
    }

    /**
     * Search parts on PartsGeek
     */
    public function searchPartsGeekParts(array $searchParams): array
    {
        if (!$this->isMarketplaceEnabled('partsgeek')) {
            return $this->getMockPartsData('partsgeek', $searchParams);
        }

        $config = $this->marketplaceConfigs['partsgeek'];
        $endpoint = $config['endpoints']['search_parts'];
        $url = $config['base_url'] . $endpoint;

        try {
            $params = array_merge([
                'q' => $searchParams['query'] ?? '',
                'limit' => $searchParams['limit'] ?? 20,
                'offset' => $searchParams['offset'] ?? 0,
                'sort' => $searchParams['sort'] ?? 'relevance',
                'type' => $searchParams['type'] ?? 'all', // oem, aftermarket, all
                'brand' => $searchParams['brand'] ?? ''
            ], $searchParams);

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('PartsGeek parts search successful', [
                    'query' => $searchParams['query'] ?? '',
                    'results_count' => count($data['parts'] ?? [])
                ]);

                return $this->normalizePartsGeekData($data);
            } else {
                Log::warning('Failed to search PartsGeek parts', [
                    'query' => $searchParams['query'] ?? '',
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockPartsData('partsgeek', $searchParams);
            }
        } catch (\Exception $e) {
            Log::error('Error searching PartsGeek parts', [
                'query' => $searchParams['query'] ?? '',
                'error' => $e->getMessage()
            ]);

            return $this->getMockPartsData('partsgeek', $searchParams);
        }
    }

    /**
     * Search parts across all marketplaces
     */
    public function searchAllMarketplaces(array $searchParams): array
    {
        $results = [
            'query' => $searchParams['query'] ?? '',
            'marketplaces' => [],
            'aggregated_results' => [],
            'total_results' => 0,
            'last_updated' => now()->toISOString()
        ];

        // Search each marketplace
        foreach ($this->marketplaceConfigs as $marketplace => $config) {
            if (!$config['enabled']) {
                continue;
            }

            try {
                switch ($marketplace) {
                    case 'ebay_motors':
                        $data = $this->searchEbayMotorsParts($searchParams);
                        break;
                    case 'amazon_paapi':
                        $data = $this->searchAmazonParts($searchParams);
                        break;
                    case 'autozone':
                        $data = $this->searchAutoZoneParts($searchParams);
                        break;
                    case 'rockauto':
                        $data = $this->searchRockAutoParts($searchParams);
                        break;
                    case 'partsgeek':
                        $data = $this->searchPartsGeekParts($searchParams);
                        break;
                    default:
                        continue 2;
                }

                $results['marketplaces'][$marketplace] = $data;
                $results['total_results'] += count($data['parts'] ?? []);
            } catch (\Exception $e) {
                Log::error("Error searching {$marketplace} parts", [
                    'query' => $searchParams['query'] ?? '',
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Aggregate results
        $results['aggregated_results'] = $this->aggregatePartsResults($results['marketplaces']);

        return $results;
    }

    /**
     * Get eBay OAuth token
     */
    private function getEbayOAuthToken(): ?string
    {
        $config = $this->marketplaceConfigs['ebay_motors'];
        $url = $config['base_url'] . $config['endpoints']['oauth_token'];

        try {
            $response = Http::timeout(10)
                ->asForm()
                ->withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic ' . base64_encode($config['client_id'] . ':' . $config['client_secret'])
                ])
                ->post($url, [
                    'grant_type' => 'client_credentials',
                    'scope' => 'https://api.ebay.com/oauth/api_scope'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Error getting eBay OAuth token', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Get Amazon authentication header
     */
    private function getAmazonAuthHeader(array $requestBody, array $config): string
    {
        // This is a simplified version - in production, you'd implement proper AWS signature
        $timestamp = gmdate('Ymd\THis\Z');
        $stringToSign = "POST\n{$config['host']}\n/paapi5/searchitems\n{$timestamp}";
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $config['secret_key'], true));
        
        return "AWS4-HMAC-SHA256 Credential={$config['access_key']}/{$timestamp}/{$config['region']}/paapi5/aws4_request, SignedHeaders=host;x-amz-date, Signature={$signature}";
    }

    /**
     * Check if marketplace is enabled
     */
    private function isMarketplaceEnabled(string $marketplace): bool
    {
        return isset($this->marketplaceConfigs[$marketplace]) && 
               $this->marketplaceConfigs[$marketplace]['enabled'] &&
               !empty($this->marketplaceConfigs[$marketplace]['api_key'] ?? $this->marketplaceConfigs[$marketplace]['app_id'] ?? null);
    }

    /**
     * Normalize eBay Motors data
     */
    private function normalizeEbayMotorsData(array $data): array
    {
        $normalized = [
            'marketplace' => 'ebay_motors',
            'parts' => [],
            'total_results' => $data['total'] ?? 0,
            'data_source' => 'ebay_motors_api'
        ];

        foreach ($data['itemSummaries'] ?? [] as $item) {
            $normalized['parts'][] = [
                'id' => $item['itemId'] ?? '',
                'title' => $item['title'] ?? '',
                'price' => [
                    'value' => $item['price']['value'] ?? 0,
                    'currency' => $item['price']['currency'] ?? 'USD'
                ],
                'condition' => $item['condition'] ?? 'unknown',
                'seller' => [
                    'username' => $item['seller']['username'] ?? '',
                    'feedback_score' => $item['seller']['feedbackScore'] ?? 0
                ],
                'shipping' => [
                    'cost' => $item['shippingOptions'][0]['shippingCost']['value'] ?? 0,
                    'currency' => $item['shippingOptions'][0]['shippingCost']['currency'] ?? 'USD'
                ],
                'image_url' => $item['image']['imageUrl'] ?? '',
                'item_url' => $item['itemWebUrl'] ?? '',
                'marketplace' => 'eBay Motors',
                'data_source' => 'ebay_motors_api'
            ];
        }

        return $normalized;
    }

    /**
     * Normalize Amazon data
     */
    private function normalizeAmazonData(array $data): array
    {
        $normalized = [
            'marketplace' => 'amazon_paapi',
            'parts' => [],
            'total_results' => $data['SearchResult']['TotalResultCount'] ?? 0,
            'data_source' => 'amazon_paapi_api'
        ];

        foreach ($data['SearchResult']['Items'] ?? [] as $item) {
            $normalized['parts'][] = [
                'id' => $item['ASIN'] ?? '',
                'title' => $item['ItemInfo']['Title']['DisplayValue'] ?? '',
                'price' => [
                    'value' => $item['Offers']['Listings'][0]['Price']['Amount'] ?? 0,
                    'currency' => $item['Offers']['Listings'][0]['Price']['Currency'] ?? 'USD'
                ],
                'brand' => $item['ItemInfo']['ByLineInfo']['Brand']['DisplayValue'] ?? '',
                'manufacturer' => $item['ItemInfo']['ByLineInfo']['Manufacturer']['DisplayValue'] ?? '',
                'availability' => $item['Offers']['Listings'][0]['Availability']['Message'] ?? 'unknown',
                'merchant' => $item['Offers']['Listings'][0]['MerchantInfo']['Name'] ?? '',
                'image_url' => $item['Images']['Primary']['Large']['URL'] ?? '',
                'item_url' => $item['DetailPageURL'] ?? '',
                'marketplace' => 'Amazon',
                'data_source' => 'amazon_paapi_api'
            ];
        }

        return $normalized;
    }

    /**
     * Normalize AutoZone data
     */
    private function normalizeAutoZoneData(array $data): array
    {
        $normalized = [
            'marketplace' => 'autozone',
            'parts' => [],
            'total_results' => $data['total'] ?? 0,
            'data_source' => 'autozone_api'
        ];

        foreach ($data['parts'] ?? [] as $part) {
            $normalized['parts'][] = [
                'id' => $part['part_number'] ?? '',
                'title' => $part['name'] ?? '',
                'price' => [
                    'value' => $part['price'] ?? 0,
                    'currency' => 'USD'
                ],
                'brand' => $part['brand'] ?? '',
                'part_number' => $part['part_number'] ?? '',
                'category' => $part['category'] ?? '',
                'availability' => $part['availability'] ?? 'unknown',
                'image_url' => $part['image_url'] ?? '',
                'item_url' => $part['url'] ?? '',
                'marketplace' => 'AutoZone',
                'data_source' => 'autozone_api'
            ];
        }

        return $normalized;
    }

    /**
     * Normalize RockAuto data
     */
    private function normalizeRockAutoData(array $data): array
    {
        $normalized = [
            'marketplace' => 'rockauto',
            'parts' => [],
            'total_results' => $data['total'] ?? 0,
            'data_source' => 'rockauto_api'
        ];

        foreach ($data['parts'] ?? [] as $part) {
            $normalized['parts'][] = [
                'id' => $part['part_id'] ?? '',
                'title' => $part['name'] ?? '',
                'price' => [
                    'value' => $part['price'] ?? 0,
                    'currency' => 'USD'
                ],
                'brand' => $part['brand'] ?? '',
                'part_number' => $part['part_number'] ?? '',
                'category' => $part['category'] ?? '',
                'availability' => $part['availability'] ?? 'unknown',
                'image_url' => $part['image_url'] ?? '',
                'item_url' => $part['url'] ?? '',
                'marketplace' => 'RockAuto',
                'data_source' => 'rockauto_api'
            ];
        }

        return $normalized;
    }

    /**
     * Normalize PartsGeek data
     */
    private function normalizePartsGeekData(array $data): array
    {
        $normalized = [
            'marketplace' => 'partsgeek',
            'parts' => [],
            'total_results' => $data['total'] ?? 0,
            'data_source' => 'partsgeek_api'
        ];

        foreach ($data['parts'] ?? [] as $part) {
            $normalized['parts'][] = [
                'id' => $part['part_number'] ?? '',
                'title' => $part['name'] ?? '',
                'price' => [
                    'value' => $part['price'] ?? 0,
                    'currency' => 'USD'
                ],
                'brand' => $part['brand'] ?? '',
                'part_number' => $part['part_number'] ?? '',
                'type' => $part['type'] ?? '', // OEM, Aftermarket
                'category' => $part['category'] ?? '',
                'availability' => $part['availability'] ?? 'unknown',
                'image_url' => $part['image_url'] ?? '',
                'item_url' => $part['url'] ?? '',
                'marketplace' => 'PartsGeek',
                'data_source' => 'partsgeek_api'
            ];
        }

        return $normalized;
    }

    /**
     * Aggregate parts results from multiple marketplaces
     */
    private function aggregatePartsResults(array $marketplaceResults): array
    {
        $aggregated = [
            'parts' => [],
            'price_range' => [
                'min' => null,
                'max' => null,
                'average' => null
            ],
            'brands' => [],
            'categories' => [],
            'marketplaces' => []
        ];

        $allParts = [];
        $prices = [];

        foreach ($marketplaceResults as $marketplace => $data) {
            if (empty($data['parts'])) continue;

            $aggregated['marketplaces'][] = $marketplace;
            
            foreach ($data['parts'] as $part) {
                $allParts[] = $part;
                
                if (isset($part['price']['value'])) {
                    $prices[] = $part['price']['value'];
                }
                
                if (!empty($part['brand'])) {
                    $aggregated['brands'][] = $part['brand'];
                }
                
                if (!empty($part['category'])) {
                    $aggregated['categories'][] = $part['category'];
                }
            }
        }

        // Sort by price
        usort($allParts, function($a, $b) {
            $priceA = $a['price']['value'] ?? 0;
            $priceB = $b['price']['value'] ?? 0;
            return $priceA <=> $priceB;
        });

        $aggregated['parts'] = $allParts;
        $aggregated['brands'] = array_unique($aggregated['brands']);
        $aggregated['categories'] = array_unique($aggregated['categories']);

        if (!empty($prices)) {
            $aggregated['price_range']['min'] = min($prices);
            $aggregated['price_range']['max'] = max($prices);
            $aggregated['price_range']['average'] = array_sum($prices) / count($prices);
        }

        return $aggregated;
    }

    /**
     * Get mock parts data for testing
     */
    private function getMockPartsData(string $marketplace, array $searchParams): array
    {
        $mockParts = [
            [
                'id' => 'mock_' . uniqid(),
                'title' => 'Mock ' . ($searchParams['query'] ?? 'Part') . ' - ' . ucfirst($marketplace),
                'price' => [
                    'value' => rand(50, 500),
                    'currency' => 'USD'
                ],
                'brand' => 'Mock Brand',
                'part_number' => 'MOCK-' . rand(1000, 9999),
                'category' => 'Engine Parts',
                'availability' => 'In Stock',
                'image_url' => 'https://via.placeholder.com/300x300?text=Mock+Part',
                'item_url' => 'https://example.com/part/' . uniqid(),
                'marketplace' => ucfirst(str_replace('_', ' ', $marketplace)),
                'data_source' => 'mock_data'
            ]
        ];

        return [
            'marketplace' => $marketplace,
            'parts' => $mockParts,
            'total_results' => count($mockParts),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get supported marketplaces
     */
    public function getSupportedMarketplaces(): array
    {
        return array_keys($this->marketplaceConfigs);
    }

    /**
     * Get marketplace configuration
     */
    public function getMarketplaceConfig(string $marketplace): array
    {
        return $this->marketplaceConfigs[$marketplace] ?? [];
    }

    /**
     * Get service status
     */
    public function getStatus(): array
    {
        $status = [];
        
        foreach ($this->marketplaceConfigs as $marketplace => $config) {
            $status[$marketplace] = [
                'enabled' => $config['enabled'],
                'configured' => !empty($config['api_key'] ?? $config['app_id'] ?? null),
                'base_url' => $config['base_url']
            ];
        }

        return $status;
    }

    /**
     * Test marketplace API connection
     */
    public function testMarketplaceAPI(string $marketplace): bool
    {
        if (!$this->isMarketplaceEnabled($marketplace)) {
            return false;
        }

        $config = $this->marketplaceConfigs[$marketplace];
        $testUrl = $config['base_url'] . '/health'; // Most APIs have a health endpoint

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . ($config['api_key'] ?? $config['app_id'] ?? ''),
                    'Accept' => 'application/json'
                ])
                ->get($testUrl);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Marketplace API test failed', [
                'marketplace' => $marketplace,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PartsMarketplaceAPIService
{
    private $marketplaceConfigs;
    private $defaultTimeout = 30;

    public function __construct()
    {
        $this->marketplaceConfigs = [
            'ebay_motors' => [
                'enabled' => config('services.ebay_motors.enabled', false),
                'app_id' => config('services.ebay_motors.app_id'),
                'client_id' => config('services.ebay_motors.client_id'),
                'client_secret' => config('services.ebay_motors.client_secret'),
                'base_url' => config('services.ebay_motors.base_url', 'https://api.ebay.com'),
                'sandbox_url' => config('services.ebay_motors.sandbox_url', 'https://api.sandbox.ebay.com'),
                'endpoints' => [
                    'oauth_token' => '/identity/v1/oauth2/token',
                    'search_items' => '/buy/browse/v1/item_summary/search',
                    'get_item' => '/buy/browse/v1/item/{item_id}',
                    'parts_compatibility' => '/commerce/parts_compatibility/v1/part_compatibility',
                    'categories' => '/commerce/taxonomy/v1/category_tree/{category_tree_id}'
                ]
            ],
            'amazon_paapi' => [
                'enabled' => config('services.amazon_paapi.enabled', false),
                'access_key' => config('services.amazon_paapi.access_key'),
                'secret_key' => config('services.amazon_paapi.secret_key'),
                'partner_tag' => config('services.amazon_paapi.partner_tag'),
                'host' => config('services.amazon_paapi.host', 'webservices.amazon.com'),
                'region' => config('services.amazon_paapi.region', 'us-east-1'),
                'endpoints' => [
                    'search_items' => '/paapi5/searchitems',
                    'get_items' => '/paapi5/getitems',
                    'get_browse_nodes' => '/paapi5/getbrowsenodes'
                ]
            ],
            'autozone' => [
                'enabled' => config('services.autozone.enabled', false),
                'api_key' => config('services.autozone.api_key'),
                'base_url' => config('services.autozone.base_url', 'https://api.autozone.com'),
                'endpoints' => [
                    'search_parts' => '/v1/parts/search',
                    'get_part_details' => '/v1/parts/{part_number}',
                    'vehicle_compatibility' => '/v1/parts/{part_number}/compatibility',
                    'store_locator' => '/v1/stores/locate',
                    'inventory' => '/v1/inventory/{part_number}'
                ]
            ],
            'rockauto' => [
                'enabled' => config('services.rockauto.enabled', false),
                'api_key' => config('services.rockauto.api_key'),
                'base_url' => config('services.rockauto.base_url', 'https://api.rockauto.com'),
                'endpoints' => [
                    'catalog' => '/v1/catalog',
                    'search_parts' => '/v1/parts/search',
                    'part_details' => '/v1/parts/{part_id}',
                    'vehicle_parts' => '/v1/vehicles/{vehicle_id}/parts',
                    'brands' => '/v1/brands',
                    'categories' => '/v1/categories'
                ]
            ],
            'partsgeek' => [
                'enabled' => config('services.partsgeek.enabled', false),
                'api_key' => config('services.partsgeek.api_key'),
                'base_url' => config('services.partsgeek.base_url', 'https://api.partsgeek.com'),
                'endpoints' => [
                    'search_parts' => '/v1/parts/search',
                    'part_details' => '/v1/parts/{part_number}',
                    'oem_parts' => '/v1/parts/oem',
                    'aftermarket_parts' => '/v1/parts/aftermarket',
                    'vehicle_parts' => '/v1/vehicles/{vehicle_id}/parts',
                    'brands' => '/v1/brands'
                ]
            ]
        ];
    }

    /**
     * Search parts on eBay Motors
     */
    public function searchEbayMotorsParts(array $searchParams): array
    {
        if (!$this->isMarketplaceEnabled('ebay_motors')) {
            return $this->getMockPartsData('ebay_motors', $searchParams);
        }

        $config = $this->marketplaceConfigs['ebay_motors'];
        $endpoint = $config['endpoints']['search_items'];
        $url = $config['base_url'] . $endpoint;

        try {
            // Get OAuth token first
            $accessToken = $this->getEbayOAuthToken();
            if (!$accessToken) {
                return $this->getMockPartsData('ebay_motors', $searchParams);
            }

            $params = array_merge([
                'q' => $searchParams['query'] ?? '',
                'category_ids' => '6028', // eBay Motors category
                'limit' => $searchParams['limit'] ?? 20,
                'offset' => $searchParams['offset'] ?? 0,
                'sort' => $searchParams['sort'] ?? 'price_asc',
                'filter' => 'conditionIds:{3000|4000|5000}', // New, Used, Refurbished
            ], $searchParams);

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('eBay Motors parts search successful', [
                    'query' => $searchParams['query'] ?? '',
                    'results_count' => count($data['itemSummaries'] ?? [])
                ]);

                return $this->normalizeEbayMotorsData($data);
            } else {
                Log::warning('Failed to search eBay Motors parts', [
                    'query' => $searchParams['query'] ?? '',
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockPartsData('ebay_motors', $searchParams);
            }
        } catch (\Exception $e) {
            Log::error('Error searching eBay Motors parts', [
                'query' => $searchParams['query'] ?? '',
                'error' => $e->getMessage()
            ]);

            return $this->getMockPartsData('ebay_motors', $searchParams);
        }
    }

    /**
     * Search parts on Amazon Product Advertising API
     */
    public function searchAmazonParts(array $searchParams): array
    {
        if (!$this->isMarketplaceEnabled('amazon_paapi')) {
            return $this->getMockPartsData('amazon_paapi', $searchParams);
        }

        $config = $this->marketplaceConfigs['amazon_paapi'];
        $endpoint = $config['endpoints']['search_items'];
        $url = 'https://' . $config['host'] . $endpoint;

        try {
            $requestBody = [
                'PartnerTag' => $config['partner_tag'],
                'PartnerType' => 'Associates',
                'Marketplace' => 'www.amazon.com',
                'SearchIndex' => 'Automotive',
                'Keywords' => $searchParams['query'] ?? '',
                'ItemCount' => $searchParams['limit'] ?? 20,
                'ItemPage' => ($searchParams['offset'] ?? 0) / ($searchParams['limit'] ?? 20) + 1,
                'Resources' => [
                    'ItemInfo.Title',
                    'ItemInfo.ByLineInfo',
                    'ItemInfo.Classifications',
                    'ItemInfo.ExternalIds',
                    'ItemInfo.Features',
                    'ItemInfo.ManufactureInfo',
                    'ItemInfo.ProductInfo',
                    'ItemInfo.TechnicalInfo',
                    'Offers.Listings.Price',
                    'Offers.Listings.Availability',
                    'Offers.Listings.MerchantInfo',
                    'Images.Primary.Large',
                    'Images.Variants'
                ]
            ];

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Content-Type' => 'application/json; charset=UTF-8',
                    'X-Amz-Target' => 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.SearchItems',
                    'X-Amz-Date' => gmdate('Ymd\THis\Z'),
                    'Authorization' => $this->getAmazonAuthHeader($requestBody, $config)
                ])
                ->post($url, $requestBody);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Amazon parts search successful', [
                    'query' => $searchParams['query'] ?? '',
                    'results_count' => count($data['SearchResult']['Items'] ?? [])
                ]);

                return $this->normalizeAmazonData($data);
            } else {
                Log::warning('Failed to search Amazon parts', [
                    'query' => $searchParams['query'] ?? '',
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockPartsData('amazon_paapi', $searchParams);
            }
        } catch (\Exception $e) {
            Log::error('Error searching Amazon parts', [
                'query' => $searchParams['query'] ?? '',
                'error' => $e->getMessage()
            ]);

            return $this->getMockPartsData('amazon_paapi', $searchParams);
        }
    }

    /**
     * Search parts on AutoZone
     */
    public function searchAutoZoneParts(array $searchParams): array
    {
        if (!$this->isMarketplaceEnabled('autozone')) {
            return $this->getMockPartsData('autozone', $searchParams);
        }

        $config = $this->marketplaceConfigs['autozone'];
        $endpoint = $config['endpoints']['search_parts'];
        $url = $config['base_url'] . $endpoint;

        try {
            $params = array_merge([
                'q' => $searchParams['query'] ?? '',
                'limit' => $searchParams['limit'] ?? 20,
                'offset' => $searchParams['offset'] ?? 0,
                'sort' => $searchParams['sort'] ?? 'relevance',
                'filters' => $searchParams['filters'] ?? []
            ], $searchParams);

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('AutoZone parts search successful', [
                    'query' => $searchParams['query'] ?? '',
                    'results_count' => count($data['parts'] ?? [])
                ]);

                return $this->normalizeAutoZoneData($data);
            } else {
                Log::warning('Failed to search AutoZone parts', [
                    'query' => $searchParams['query'] ?? '',
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockPartsData('autozone', $searchParams);
            }
        } catch (\Exception $e) {
            Log::error('Error searching AutoZone parts', [
                'query' => $searchParams['query'] ?? '',
                'error' => $e->getMessage()
            ]);

            return $this->getMockPartsData('autozone', $searchParams);
        }
    }

    /**
     * Search parts on RockAuto
     */
    public function searchRockAutoParts(array $searchParams): array
    {
        if (!$this->isMarketplaceEnabled('rockauto')) {
            return $this->getMockPartsData('rockauto', $searchParams);
        }

        $config = $this->marketplaceConfigs['rockauto'];
        $endpoint = $config['endpoints']['search_parts'];
        $url = $config['base_url'] . $endpoint;

        try {
            $params = array_merge([
                'q' => $searchParams['query'] ?? '',
                'limit' => $searchParams['limit'] ?? 20,
                'offset' => $searchParams['offset'] ?? 0,
                'sort' => $searchParams['sort'] ?? 'price_asc',
                'category' => $searchParams['category'] ?? '',
                'brand' => $searchParams['brand'] ?? ''
            ], $searchParams);

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('RockAuto parts search successful', [
                    'query' => $searchParams['query'] ?? '',
                    'results_count' => count($data['parts'] ?? [])
                ]);

                return $this->normalizeRockAutoData($data);
            } else {
                Log::warning('Failed to search RockAuto parts', [
                    'query' => $searchParams['query'] ?? '',
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockPartsData('rockauto', $searchParams);
            }
        } catch (\Exception $e) {
            Log::error('Error searching RockAuto parts', [
                'query' => $searchParams['query'] ?? '',
                'error' => $e->getMessage()
            ]);

            return $this->getMockPartsData('rockauto', $searchParams);
        }
    }

    /**
     * Search parts on PartsGeek
     */
    public function searchPartsGeekParts(array $searchParams): array
    {
        if (!$this->isMarketplaceEnabled('partsgeek')) {
            return $this->getMockPartsData('partsgeek', $searchParams);
        }

        $config = $this->marketplaceConfigs['partsgeek'];
        $endpoint = $config['endpoints']['search_parts'];
        $url = $config['base_url'] . $endpoint;

        try {
            $params = array_merge([
                'q' => $searchParams['query'] ?? '',
                'limit' => $searchParams['limit'] ?? 20,
                'offset' => $searchParams['offset'] ?? 0,
                'sort' => $searchParams['sort'] ?? 'relevance',
                'type' => $searchParams['type'] ?? 'all', // oem, aftermarket, all
                'brand' => $searchParams['brand'] ?? ''
            ], $searchParams);

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('PartsGeek parts search successful', [
                    'query' => $searchParams['query'] ?? '',
                    'results_count' => count($data['parts'] ?? [])
                ]);

                return $this->normalizePartsGeekData($data);
            } else {
                Log::warning('Failed to search PartsGeek parts', [
                    'query' => $searchParams['query'] ?? '',
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockPartsData('partsgeek', $searchParams);
            }
        } catch (\Exception $e) {
            Log::error('Error searching PartsGeek parts', [
                'query' => $searchParams['query'] ?? '',
                'error' => $e->getMessage()
            ]);

            return $this->getMockPartsData('partsgeek', $searchParams);
        }
    }

    /**
     * Search parts across all marketplaces
     */
    public function searchAllMarketplaces(array $searchParams): array
    {
        $results = [
            'query' => $searchParams['query'] ?? '',
            'marketplaces' => [],
            'aggregated_results' => [],
            'total_results' => 0,
            'last_updated' => now()->toISOString()
        ];

        // Search each marketplace
        foreach ($this->marketplaceConfigs as $marketplace => $config) {
            if (!$config['enabled']) {
                continue;
            }

            try {
                switch ($marketplace) {
                    case 'ebay_motors':
                        $data = $this->searchEbayMotorsParts($searchParams);
                        break;
                    case 'amazon_paapi':
                        $data = $this->searchAmazonParts($searchParams);
                        break;
                    case 'autozone':
                        $data = $this->searchAutoZoneParts($searchParams);
                        break;
                    case 'rockauto':
                        $data = $this->searchRockAutoParts($searchParams);
                        break;
                    case 'partsgeek':
                        $data = $this->searchPartsGeekParts($searchParams);
                        break;
                    default:
                        continue 2;
                }

                $results['marketplaces'][$marketplace] = $data;
                $results['total_results'] += count($data['parts'] ?? []);
            } catch (\Exception $e) {
                Log::error("Error searching {$marketplace} parts", [
                    'query' => $searchParams['query'] ?? '',
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Aggregate results
        $results['aggregated_results'] = $this->aggregatePartsResults($results['marketplaces']);

        return $results;
    }

    /**
     * Get eBay OAuth token
     */
    private function getEbayOAuthToken(): ?string
    {
        $config = $this->marketplaceConfigs['ebay_motors'];
        $url = $config['base_url'] . $config['endpoints']['oauth_token'];

        try {
            $response = Http::timeout(10)
                ->asForm()
                ->withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic ' . base64_encode($config['client_id'] . ':' . $config['client_secret'])
                ])
                ->post($url, [
                    'grant_type' => 'client_credentials',
                    'scope' => 'https://api.ebay.com/oauth/api_scope'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Error getting eBay OAuth token', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Get Amazon authentication header
     */
    private function getAmazonAuthHeader(array $requestBody, array $config): string
    {
        // This is a simplified version - in production, you'd implement proper AWS signature
        $timestamp = gmdate('Ymd\THis\Z');
        $stringToSign = "POST\n{$config['host']}\n/paapi5/searchitems\n{$timestamp}";
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $config['secret_key'], true));
        
        return "AWS4-HMAC-SHA256 Credential={$config['access_key']}/{$timestamp}/{$config['region']}/paapi5/aws4_request, SignedHeaders=host;x-amz-date, Signature={$signature}";
    }

    /**
     * Check if marketplace is enabled
     */
    private function isMarketplaceEnabled(string $marketplace): bool
    {
        return isset($this->marketplaceConfigs[$marketplace]) && 
               $this->marketplaceConfigs[$marketplace]['enabled'] &&
               !empty($this->marketplaceConfigs[$marketplace]['api_key'] ?? $this->marketplaceConfigs[$marketplace]['app_id'] ?? null);
    }

    /**
     * Normalize eBay Motors data
     */
    private function normalizeEbayMotorsData(array $data): array
    {
        $normalized = [
            'marketplace' => 'ebay_motors',
            'parts' => [],
            'total_results' => $data['total'] ?? 0,
            'data_source' => 'ebay_motors_api'
        ];

        foreach ($data['itemSummaries'] ?? [] as $item) {
            $normalized['parts'][] = [
                'id' => $item['itemId'] ?? '',
                'title' => $item['title'] ?? '',
                'price' => [
                    'value' => $item['price']['value'] ?? 0,
                    'currency' => $item['price']['currency'] ?? 'USD'
                ],
                'condition' => $item['condition'] ?? 'unknown',
                'seller' => [
                    'username' => $item['seller']['username'] ?? '',
                    'feedback_score' => $item['seller']['feedbackScore'] ?? 0
                ],
                'shipping' => [
                    'cost' => $item['shippingOptions'][0]['shippingCost']['value'] ?? 0,
                    'currency' => $item['shippingOptions'][0]['shippingCost']['currency'] ?? 'USD'
                ],
                'image_url' => $item['image']['imageUrl'] ?? '',
                'item_url' => $item['itemWebUrl'] ?? '',
                'marketplace' => 'eBay Motors',
                'data_source' => 'ebay_motors_api'
            ];
        }

        return $normalized;
    }

    /**
     * Normalize Amazon data
     */
    private function normalizeAmazonData(array $data): array
    {
        $normalized = [
            'marketplace' => 'amazon_paapi',
            'parts' => [],
            'total_results' => $data['SearchResult']['TotalResultCount'] ?? 0,
            'data_source' => 'amazon_paapi_api'
        ];

        foreach ($data['SearchResult']['Items'] ?? [] as $item) {
            $normalized['parts'][] = [
                'id' => $item['ASIN'] ?? '',
                'title' => $item['ItemInfo']['Title']['DisplayValue'] ?? '',
                'price' => [
                    'value' => $item['Offers']['Listings'][0]['Price']['Amount'] ?? 0,
                    'currency' => $item['Offers']['Listings'][0]['Price']['Currency'] ?? 'USD'
                ],
                'brand' => $item['ItemInfo']['ByLineInfo']['Brand']['DisplayValue'] ?? '',
                'manufacturer' => $item['ItemInfo']['ByLineInfo']['Manufacturer']['DisplayValue'] ?? '',
                'availability' => $item['Offers']['Listings'][0]['Availability']['Message'] ?? 'unknown',
                'merchant' => $item['Offers']['Listings'][0]['MerchantInfo']['Name'] ?? '',
                'image_url' => $item['Images']['Primary']['Large']['URL'] ?? '',
                'item_url' => $item['DetailPageURL'] ?? '',
                'marketplace' => 'Amazon',
                'data_source' => 'amazon_paapi_api'
            ];
        }

        return $normalized;
    }

    /**
     * Normalize AutoZone data
     */
    private function normalizeAutoZoneData(array $data): array
    {
        $normalized = [
            'marketplace' => 'autozone',
            'parts' => [],
            'total_results' => $data['total'] ?? 0,
            'data_source' => 'autozone_api'
        ];

        foreach ($data['parts'] ?? [] as $part) {
            $normalized['parts'][] = [
                'id' => $part['part_number'] ?? '',
                'title' => $part['name'] ?? '',
                'price' => [
                    'value' => $part['price'] ?? 0,
                    'currency' => 'USD'
                ],
                'brand' => $part['brand'] ?? '',
                'part_number' => $part['part_number'] ?? '',
                'category' => $part['category'] ?? '',
                'availability' => $part['availability'] ?? 'unknown',
                'image_url' => $part['image_url'] ?? '',
                'item_url' => $part['url'] ?? '',
                'marketplace' => 'AutoZone',
                'data_source' => 'autozone_api'
            ];
        }

        return $normalized;
    }

    /**
     * Normalize RockAuto data
     */
    private function normalizeRockAutoData(array $data): array
    {
        $normalized = [
            'marketplace' => 'rockauto',
            'parts' => [],
            'total_results' => $data['total'] ?? 0,
            'data_source' => 'rockauto_api'
        ];

        foreach ($data['parts'] ?? [] as $part) {
            $normalized['parts'][] = [
                'id' => $part['part_id'] ?? '',
                'title' => $part['name'] ?? '',
                'price' => [
                    'value' => $part['price'] ?? 0,
                    'currency' => 'USD'
                ],
                'brand' => $part['brand'] ?? '',
                'part_number' => $part['part_number'] ?? '',
                'category' => $part['category'] ?? '',
                'availability' => $part['availability'] ?? 'unknown',
                'image_url' => $part['image_url'] ?? '',
                'item_url' => $part['url'] ?? '',
                'marketplace' => 'RockAuto',
                'data_source' => 'rockauto_api'
            ];
        }

        return $normalized;
    }

    /**
     * Normalize PartsGeek data
     */
    private function normalizePartsGeekData(array $data): array
    {
        $normalized = [
            'marketplace' => 'partsgeek',
            'parts' => [],
            'total_results' => $data['total'] ?? 0,
            'data_source' => 'partsgeek_api'
        ];

        foreach ($data['parts'] ?? [] as $part) {
            $normalized['parts'][] = [
                'id' => $part['part_number'] ?? '',
                'title' => $part['name'] ?? '',
                'price' => [
                    'value' => $part['price'] ?? 0,
                    'currency' => 'USD'
                ],
                'brand' => $part['brand'] ?? '',
                'part_number' => $part['part_number'] ?? '',
                'type' => $part['type'] ?? '', // OEM, Aftermarket
                'category' => $part['category'] ?? '',
                'availability' => $part['availability'] ?? 'unknown',
                'image_url' => $part['image_url'] ?? '',
                'item_url' => $part['url'] ?? '',
                'marketplace' => 'PartsGeek',
                'data_source' => 'partsgeek_api'
            ];
        }

        return $normalized;
    }

    /**
     * Aggregate parts results from multiple marketplaces
     */
    private function aggregatePartsResults(array $marketplaceResults): array
    {
        $aggregated = [
            'parts' => [],
            'price_range' => [
                'min' => null,
                'max' => null,
                'average' => null
            ],
            'brands' => [],
            'categories' => [],
            'marketplaces' => []
        ];

        $allParts = [];
        $prices = [];

        foreach ($marketplaceResults as $marketplace => $data) {
            if (empty($data['parts'])) continue;

            $aggregated['marketplaces'][] = $marketplace;
            
            foreach ($data['parts'] as $part) {
                $allParts[] = $part;
                
                if (isset($part['price']['value'])) {
                    $prices[] = $part['price']['value'];
                }
                
                if (!empty($part['brand'])) {
                    $aggregated['brands'][] = $part['brand'];
                }
                
                if (!empty($part['category'])) {
                    $aggregated['categories'][] = $part['category'];
                }
            }
        }

        // Sort by price
        usort($allParts, function($a, $b) {
            $priceA = $a['price']['value'] ?? 0;
            $priceB = $b['price']['value'] ?? 0;
            return $priceA <=> $priceB;
        });

        $aggregated['parts'] = $allParts;
        $aggregated['brands'] = array_unique($aggregated['brands']);
        $aggregated['categories'] = array_unique($aggregated['categories']);

        if (!empty($prices)) {
            $aggregated['price_range']['min'] = min($prices);
            $aggregated['price_range']['max'] = max($prices);
            $aggregated['price_range']['average'] = array_sum($prices) / count($prices);
        }

        return $aggregated;
    }

    /**
     * Get mock parts data for testing
     */
    private function getMockPartsData(string $marketplace, array $searchParams): array
    {
        $mockParts = [
            [
                'id' => 'mock_' . uniqid(),
                'title' => 'Mock ' . ($searchParams['query'] ?? 'Part') . ' - ' . ucfirst($marketplace),
                'price' => [
                    'value' => rand(50, 500),
                    'currency' => 'USD'
                ],
                'brand' => 'Mock Brand',
                'part_number' => 'MOCK-' . rand(1000, 9999),
                'category' => 'Engine Parts',
                'availability' => 'In Stock',
                'image_url' => 'https://via.placeholder.com/300x300?text=Mock+Part',
                'item_url' => 'https://example.com/part/' . uniqid(),
                'marketplace' => ucfirst(str_replace('_', ' ', $marketplace)),
                'data_source' => 'mock_data'
            ]
        ];

        return [
            'marketplace' => $marketplace,
            'parts' => $mockParts,
            'total_results' => count($mockParts),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get supported marketplaces
     */
    public function getSupportedMarketplaces(): array
    {
        return array_keys($this->marketplaceConfigs);
    }

    /**
     * Get marketplace configuration
     */
    public function getMarketplaceConfig(string $marketplace): array
    {
        return $this->marketplaceConfigs[$marketplace] ?? [];
    }

    /**
     * Get service status
     */
    public function getStatus(): array
    {
        $status = [];
        
        foreach ($this->marketplaceConfigs as $marketplace => $config) {
            $status[$marketplace] = [
                'enabled' => $config['enabled'],
                'configured' => !empty($config['api_key'] ?? $config['app_id'] ?? null),
                'base_url' => $config['base_url']
            ];
        }

        return $status;
    }

    /**
     * Test marketplace API connection
     */
    public function testMarketplaceAPI(string $marketplace): bool
    {
        if (!$this->isMarketplaceEnabled($marketplace)) {
            return false;
        }

        $config = $this->marketplaceConfigs[$marketplace];
        $testUrl = $config['base_url'] . '/health'; // Most APIs have a health endpoint

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . ($config['api_key'] ?? $config['app_id'] ?? ''),
                    'Accept' => 'application/json'
                ])
                ->get($testUrl);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Marketplace API test failed', [
                'marketplace' => $marketplace,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}














