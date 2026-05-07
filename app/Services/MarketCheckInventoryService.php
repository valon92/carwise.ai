<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MarketCheckInventoryService
{
    private const ACTIVE_SEARCH_PATH = '/v2/search/car/active';

    public function isConfigured(): bool
    {
        if (! config('services.marketcheck.enabled', false)) {
            return false;
        }

        return Str::of((string) config('services.marketcheck.api_key'))->trim()->isNotEmpty();
    }

    /**
     * Live carousel rows from MarketCheck. Call only when {@see isConfigured()} is true.
     *
     * @return list<array<string, mixed>>
     */
    public function getFeaturedCarouselVehicles(): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $key = 'marketcheck:featured_carousel_v3_'.$this->inventorySearchCacheFingerprint();
        $ttl = max(60, (int) config('services.marketcheck.cache_ttl', 21600));

        $cached = Cache::get($key);
        if (is_array($cached)) {
            return $cached;
        }

        $fresh = $this->fetchActiveInventory();
        if ($fresh !== []) {
            Cache::put($key, $fresh, $ttl);
        } else {
            $emptyTtl = max(60, min($ttl, (int) config('services.marketcheck.cache_empty_ttl', 600)));
            Cache::put($key, [], $emptyTtl);
        }

        return $fresh;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchActiveInventory(): array
    {
        $base = rtrim((string) config('services.marketcheck.base_url', 'https://api.marketcheck.com'), '/');
        $url = $base.self::ACTIVE_SEARCH_PATH;

        $year = (int) date('Y');

        $query = [
            'api_key' => config('services.marketcheck.api_key'),
            'rows' => min(50, max(1, (int) config('services.marketcheck.rows', 20))),
            'start' => 0,
            'sort_by' => 'first_seen',
            'sort_order' => 'desc',
            'year_range' => ($year - 4).'-'.($year + 1),
            'append_api_key' => 'true',
            'dedup' => 'true',
        ];

        if (config('services.marketcheck.inventory_photo_links', false)) {
            $query['photo_links'] = 'true';
        }

        $carType = config('services.marketcheck.carousel_car_type');
        if (is_string($carType) && $carType !== '') {
            $query['car_type'] = $carType;
        }

        $country = config('services.marketcheck.country', 'us');
        if (is_string($country) && $country !== '') {
            $query['country'] = $country;
        }

        $zip = config('services.marketcheck.zip');
        $lat = config('services.marketcheck.latitude');
        $lon = config('services.marketcheck.longitude');
        $radius = (int) config('services.marketcheck.radius', 100);
        $radius = max(1, min(100, $radius));

        if (! empty($zip)) {
            $query['zip'] = $zip;
            $query['radius'] = $radius;
        } elseif ($lat !== null && $lat !== '' && $lon !== null && $lon !== '') {
            $query['latitude'] = (float) $lat;
            $query['longitude'] = (float) $lon;
            $query['radius'] = $radius;
        }

        $make = config('services.marketcheck.search_make');
        if (is_string($make) && $make !== '') {
            $query['make'] = $make;
        }

        $model = config('services.marketcheck.search_model');
        if (is_string($model) && $model !== '') {
            $query['model'] = $model;
        }

        $priceRange = config('services.marketcheck.price_range');
        if (is_string($priceRange) && $priceRange !== '') {
            $query['price_range'] = $priceRange;
        } else {
            $priceMax = config('services.marketcheck.price_max');
            if ($priceMax !== null && $priceMax !== '' && is_numeric($priceMax)) {
                $query['price_range'] = '0-'.(int) $priceMax;
            }
        }

        if (config('services.marketcheck.luxury_showcase', false)) {
            $query['sort_by'] = 'price';
            $query['sort_order'] = 'desc';
            $query['has_price'] = 'true';
            $query['price_range'] = (string) config(
                'services.marketcheck.luxury_price_range',
                '125000-10000000'
            );
            $luxuryMakes = config('services.marketcheck.luxury_makes');
            if (is_string($luxuryMakes) && trim($luxuryMakes) !== '') {
                $query['make'] = $luxuryMakes;
            }
        }

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->get($url, $query);

            if (! $response->successful()) {
                Log::warning('MarketCheck inventory search failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            $listings = $response->json('listings');

            if (! is_array($listings) || $listings === []) {
                return [];
            }

            $mapped = [];

            foreach ($listings as $listing) {
                if (! is_array($listing)) {
                    continue;
                }
                $row = $this->mapListingToCarouselRow($listing);
                if ($row !== null) {
                    $mapped[] = $row;
                }
            }

            if ($mapped === [] && count($listings) > 0) {
                Log::warning('MarketCheck: listings returned but none passed mapping (check photos/build fields).', [
                    'raw_count' => count($listings),
                    'first_keys' => array_keys($listings[0]),
                ]);
            }

            return $mapped;
        } catch (\Throwable $e) {
            Log::error('MarketCheck inventory search error', ['error' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * @param  array<string, mixed>  $listing
     * @return array<string, mixed>|null
     */
    private function mapListingToCarouselRow(array $listing): ?array
    {
        $build = is_array($listing['build'] ?? null) ? $listing['build'] : [];
        $media = is_array($listing['media'] ?? null) ? $listing['media'] : [];
        $dealer = is_array($listing['dealer'] ?? null) ? $listing['dealer'] : [];

        $make = (string) ($build['make'] ?? $listing['make'] ?? '');
        $model = (string) ($build['model'] ?? $listing['model'] ?? '');
        $year = isset($build['year']) ? (int) $build['year'] : null;
        if (($year === null || $year < 1990) && isset($listing['year']) && is_numeric($listing['year'])) {
            $year = (int) $listing['year'];
        }

        if ($make === '' || $year === null || $year < 1990) {
            return null;
        }

        $photos = array_values(array_filter([
            ...(Arr::wrap($media['photo_links_cached'] ?? [])),
            ...(Arr::wrap($media['photo_links'] ?? [])),
        ], fn ($u) => is_string($u) && $u !== ''));

        $imageUrl = $photos[0] ?? null;
        if ($imageUrl === null) {
            $imageUrl = asset('icons/icon1.png');
        }

        $heading = isset($listing['heading']) && is_string($listing['heading'])
            ? $listing['heading']
            : trim($year.' '.$make.' '.$model);

        $price = isset($listing['price']) && is_numeric($listing['price'])
            ? (float) $listing['price']
            : null;

        $engineSize = $build['engine_size'] ?? null;
        $engineSizeLabel = is_numeric($engineSize)
            ? rtrim(rtrim(number_format((float) $engineSize, 1, '.', ''), '0'), '.').'L'
            : (isset($build['engine']) ? (string) $build['engine'] : null);

        $seats = null;
        if (isset($build['std_seating']) && is_numeric($build['std_seating'])) {
            $seats = (int) $build['std_seating'];
        }

        $features = [];
        if (isset($listing['high_value_features']) && is_array($listing['high_value_features'])) {
            foreach ($listing['high_value_features'] as $f) {
                if (is_string($f) && $f !== '') {
                    $features[] = $f;
                }
            }
        }

        $descriptionParts = array_filter([
            $heading,
            isset($listing['inventory_type']) ? 'Inventory: '.(string) $listing['inventory_type'] : null,
            isset($dealer['name']) ? 'Dealer: '.(string) $dealer['name'] : null,
            isset($dealer['city'], $dealer['state']) ? 'Location: '.$dealer['city'].', '.$dealer['state'] : null,
            'Live listing data from MarketCheck (US/CA inventory).',
        ]);

        $specs = array_filter([
            'vin' => isset($listing['vin']) && is_string($listing['vin']) ? $listing['vin'] : null,
            'exterior_color' => isset($listing['exterior_color']) ? (string) $listing['exterior_color'] : null,
            'interior_color' => isset($listing['interior_color']) ? (string) $listing['interior_color'] : null,
            'miles' => isset($listing['miles']) && is_numeric($listing['miles']) ? (string) $listing['miles'] : null,
            'msrp' => isset($listing['msrp']) && is_numeric($listing['msrp']) ? (string) $listing['msrp'] : null,
            'city_mpg' => isset($build['city_mpg']) ? (string) $build['city_mpg'] : null,
            'highway_mpg' => isset($build['highway_mpg']) ? (string) $build['highway_mpg'] : null,
        ], fn ($v) => $v !== null && $v !== '');

        $releasedAt = null;
        if (! empty($listing['first_seen_at_mc_date']) && is_string($listing['first_seen_at_mc_date'])) {
            $releasedAt = $listing['first_seen_at_mc_date'];
        } elseif (! empty($listing['first_seen_at_date']) && is_string($listing['first_seen_at_date'])) {
            $releasedAt = $listing['first_seen_at_date'];
        }

        $rowId = 'mc-'.(string) ($listing['id'] ?? uniqid('', true));
        $vdpUrl = isset($listing['vdp_url']) && is_string($listing['vdp_url']) ? $listing['vdp_url'] : null;

        return [
            'id' => $rowId,
            'manufacturer' => $make,
            'model' => $model,
            'year' => $year,
            'name' => $heading,
            'description' => implode("\n\n", $descriptionParts),
            'image_url' => $imageUrl,
            'gallery_images' => array_slice($photos, 1, 12),
            'price' => $price,
            'currency' => 'USD',
            'engine_type' => isset($build['powertrain_type']) ? (string) $build['powertrain_type'] : (isset($build['fuel_type']) ? (string) $build['fuel_type'] : null),
            'engine_size' => $engineSizeLabel,
            'horsepower' => null,
            'torque' => null,
            'transmission' => isset($build['transmission']) ? (string) $build['transmission'] : null,
            'drivetrain' => isset($build['drivetrain']) ? (string) $build['drivetrain'] : null,
            'seats' => $seats,
            'doors' => isset($build['doors']) && is_numeric($build['doors']) ? (int) $build['doors'] : null,
            'fuel_type' => isset($build['fuel_type']) ? (string) $build['fuel_type'] : null,
            'fuel_consumption' => null,
            'co2_emissions' => null,
            'body_type' => isset($build['body_type']) ? (string) $build['body_type'] : null,
            'features' => array_slice($features, 0, 20),
            'specifications' => $specs,
            'status' => 'available',
            'is_featured' => true,
            'view_count' => 0,
            'order' => 0,
            'released_at' => $releasedAt,
            'vdp_url' => $vdpUrl,
            'cta_url' => $this->buildTrackedListingUrl($vdpUrl, $rowId),
            'data_source' => 'marketcheck',
            'inventory_type' => isset($listing['inventory_type']) ? (string) $listing['inventory_type'] : null,
        ];
    }

    private function buildTrackedListingUrl(?string $vdpUrl, string $listingRef): ?string
    {
        if ($vdpUrl === null || $vdpUrl === '') {
            return null;
        }

        $token = Str::random(48);
        $hours = max(1, min(168, (int) config('services.marketcheck.outbound_token_ttl_hours', 72)));
        Cache::put('inventory_out:'.$token, [
            'v' => $vdpUrl,
            'ref' => Str::limit($listingRef, 80),
        ], now()->addHours($hours));

        return url('/inventory/out/'.$token);
    }

    /**
     * Cache key fragment when search filters change (.env).
     */
    private function inventorySearchCacheFingerprint(): string
    {
        $payload = [
            'make' => (string) (config('services.marketcheck.search_make') ?? ''),
            'model' => (string) (config('services.marketcheck.search_model') ?? ''),
            'price_range' => (string) (config('services.marketcheck.price_range') ?? ''),
            'price_max' => (string) (config('services.marketcheck.price_max') ?? ''),
            'zip' => (string) (config('services.marketcheck.zip') ?? ''),
            'lat' => (string) (config('services.marketcheck.latitude') ?? ''),
            'lon' => (string) (config('services.marketcheck.longitude') ?? ''),
            'rows' => (int) config('services.marketcheck.rows', 20),
            'car_type' => (string) (config('services.marketcheck.carousel_car_type') ?? ''),
            'country' => (string) (config('services.marketcheck.country') ?? ''),
            'photo_links' => (bool) config('services.marketcheck.inventory_photo_links', false),
            'luxury' => (bool) config('services.marketcheck.luxury_showcase', false),
            'luxury_price_range' => (string) (config('services.marketcheck.luxury_price_range') ?? ''),
            'luxury_makes' => (string) (config('services.marketcheck.luxury_makes') ?? ''),
        ];

        return substr(hash('sha256', json_encode($payload)), 0, 20);
    }
}
