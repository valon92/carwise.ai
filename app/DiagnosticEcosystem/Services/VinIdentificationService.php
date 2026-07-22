<?php

namespace App\DiagnosticEcosystem\Services;

use App\DiagnosticEcosystem\Adapters\CarApiVinAdapter;
use App\DiagnosticEcosystem\Adapters\ManufacturerVinAdapter;
use App\DiagnosticEcosystem\Adapters\NhtsaVinAdapter;
use App\DiagnosticEcosystem\Contracts\VinIdentificationProviderInterface;
use App\DiagnosticEcosystem\DTO\VinDecodeResult;
use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use App\DiagnosticEcosystem\Models\DeVinDecode;
use Illuminate\Support\Facades\DB;

class VinIdentificationService
{
    public function __construct(
        private readonly NhtsaVinAdapter $nhtsaAdapter,
        private readonly CarApiVinAdapter $carApiAdapter,
        private readonly ManufacturerVinAdapter $manufacturerAdapter,
    ) {}

    public function preview(string $vin): ?VinDecodeResult
    {
        return $this->resolveVin($vin);
    }

    public function identifyProfile(DeVehicleProfile $profile): ?DeVinDecode
    {
        $result = $this->resolveVin($profile->vin);

        if (! $result) {
            return null;
        }

        return DB::transaction(function () use ($profile, $result) {
            $decode = DeVinDecode::create([
                'vehicle_profile_id' => $profile->id,
                'vin' => $result->vin,
                'provider' => $result->provider,
                'raw_response' => $result->rawResponse,
                'manufacturer' => $result->manufacturer,
                'brand' => $result->brand,
                'model' => $result->model,
                'year' => $result->year,
                'engine' => $result->engine,
                'fuel_type' => $result->fuelType,
                'transmission' => $result->transmission,
                'horsepower' => $result->horsepower,
                'specifications' => $result->specifications,
                'factory_equipment' => $result->factoryEquipment,
                'vehicle_options' => $result->vehicleOptions,
                'service_schedule' => $result->serviceSchedule,
                'recalls' => $result->recalls,
                'warranty' => $result->warranty,
                'decoded_at' => now(),
            ]);

            $profile->update([
                'manufacturer' => $result->manufacturer,
                'brand' => $result->brand,
                'model' => $result->model,
                'year' => $result->year,
                'engine' => $result->engine,
                'fuel_type' => $result->fuelType,
                'transmission' => $result->transmission,
                'horsepower' => $result->horsepower,
                'factory_equipment' => $result->factoryEquipment,
                'vehicle_options' => $result->vehicleOptions,
                'last_vin_decode_id' => $decode->id,
            ]);

            return $decode->fresh();
        });
    }

    /**
     * @return DeVinDecode[]
     */
    public function historyForProfile(DeVehicleProfile $profile)
    {
        return $profile->vinDecodes()->latest('decoded_at')->get();
    }

    private function resolveVin(string $vin): ?VinDecodeResult
    {
        foreach ($this->providers() as $provider) {
            if (! $provider->isAvailable()) {
                continue;
            }

            $result = $provider->decode($vin);

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @return VinIdentificationProviderInterface[]
     */
    private function providers(): array
    {
        $map = [
            'nhtsa' => $this->nhtsaAdapter,
            'carapi' => $this->carApiAdapter,
            'manufacturer' => $this->manufacturerAdapter,
        ];

        return array_values(array_filter(array_map(
            fn (string $provider) => $map[$provider] ?? null,
            config('diagnostic-ecosystem.vin_providers', ['nhtsa'])
        )));
    }
}
