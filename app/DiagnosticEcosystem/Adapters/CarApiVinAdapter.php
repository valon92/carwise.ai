<?php

namespace App\DiagnosticEcosystem\Adapters;

use App\DiagnosticEcosystem\Contracts\VinIdentificationProviderInterface;
use App\DiagnosticEcosystem\DTO\VinDecodeResult;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CarApiVinAdapter implements VinIdentificationProviderInterface
{
    public function getName(): string
    {
        return 'carapi';
    }

    public function isAvailable(): bool
    {
        return (bool) env('CARAPI_ENABLED', false)
            && filled(env('CARAPI_TOKEN'))
            && filled(env('CARAPI_SECRET'));
    }

    public function decode(string $vin): ?VinDecodeResult
    {
        if (! $this->isAvailable()) {
            return null;
        }

        return Cache::remember(
            'de_carapi_vin_'.strtoupper($vin),
            (int) config('diagnostic-ecosystem.vin_cache_ttl', 86400),
            function () use ($vin) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.env('CARAPI_TOKEN'),
                    'X-API-Secret' => env('CARAPI_SECRET'),
                ])->timeout(10)->get(rtrim((string) env('CARAPI_BASE_URL', 'https://carapi.app/api'), '/').'/vin', [
                    'vin' => strtoupper($vin),
                ]);

                if (! $response->successful()) {
                    return null;
                }

                $payload = $response->json();
                $data = $payload['data'] ?? $payload;

                if (! is_array($data)) {
                    return null;
                }

                return new VinDecodeResult(
                    vin: strtoupper($vin),
                    provider: $this->getName(),
                    manufacturer: $data['manufacturer'] ?? $data['make'] ?? null,
                    brand: $data['make'] ?? $data['brand'] ?? null,
                    model: $data['model'] ?? null,
                    year: isset($data['year']) ? (int) $data['year'] : null,
                    engine: $data['engine'] ?? $data['engine_type'] ?? null,
                    fuelType: $data['fuel_type'] ?? null,
                    transmission: $data['transmission'] ?? null,
                    horsepower: isset($data['horsepower']) ? (int) $data['horsepower'] : null,
                    specifications: is_array($data['specifications'] ?? null) ? $data['specifications'] : [],
                    factoryEquipment: is_array($data['factory_equipment'] ?? null) ? $data['factory_equipment'] : [],
                    vehicleOptions: is_array($data['vehicle_options'] ?? null) ? $data['vehicle_options'] : [],
                    serviceSchedule: is_array($data['service_schedule'] ?? null) ? $data['service_schedule'] : [],
                    recalls: is_array($data['recalls'] ?? null) ? $data['recalls'] : [],
                    warranty: is_array($data['warranty'] ?? null) ? $data['warranty'] : null,
                    rawResponse: $payload,
                );
            }
        );
    }
}
