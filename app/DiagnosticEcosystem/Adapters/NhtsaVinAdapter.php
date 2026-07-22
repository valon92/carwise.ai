<?php

namespace App\DiagnosticEcosystem\Adapters;

use App\DiagnosticEcosystem\Contracts\VinIdentificationProviderInterface;
use App\DiagnosticEcosystem\DTO\VinDecodeResult;
use App\Services\PublicAPIService;

class NhtsaVinAdapter implements VinIdentificationProviderInterface
{
    public function __construct(
        private readonly PublicAPIService $publicApiService,
    ) {}

    public function getName(): string
    {
        return 'nhtsa';
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function decode(string $vin): ?VinDecodeResult
    {
        $data = $this->publicApiService->getVehicleByVIN($vin);

        if (! is_array($data) || empty($data['vin'])) {
            return null;
        }

        return new VinDecodeResult(
            vin: $vin,
            provider: $this->getName(),
            manufacturer: $data['manufacturer'] ?? $data['make'] ?? null,
            brand: $data['make'] ?? null,
            model: $data['model'] ?? null,
            year: isset($data['year']) ? (int) $data['year'] : null,
            engine: $data['engine'] ?? null,
            fuelType: $data['fuel_type'] ?? null,
            transmission: $data['transmission'] ?? null,
            specifications: array_filter([
                'body_class' => $data['body_class'] ?? null,
                'drive_type' => $data['drive_type'] ?? null,
                'doors' => $data['doors'] ?? null,
                'cylinders' => $data['cylinders'] ?? null,
                'displacement' => $data['displacement'] ?? null,
                'plant_country' => $data['plant_country'] ?? null,
                'plant_state' => $data['plant_state'] ?? null,
                'plant_city' => $data['plant_city'] ?? null,
                'series' => $data['series'] ?? null,
                'trim' => $data['trim'] ?? null,
                'gvwr' => $data['gvwr'] ?? null,
                'vehicle_type' => $data['vehicle_type'] ?? null,
            ]),
            rawResponse: $data['raw_data'] ?? $data,
        );
    }
}
