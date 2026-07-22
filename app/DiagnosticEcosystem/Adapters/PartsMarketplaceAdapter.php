<?php

namespace App\DiagnosticEcosystem\Adapters;

use App\DiagnosticEcosystem\Contracts\MarketplaceIntegrationInterface;
use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use App\Models\CarPart;
use Illuminate\Support\Facades\Schema;

/**
 * Bridges Diagnostic Ecosystem to existing marketplace/parts data.
 * Does not modify existing marketplace controllers.
 */
class PartsMarketplaceAdapter implements MarketplaceIntegrationInterface
{
    public function searchParts(int $vehicleProfileId, string $query): array
    {
        $profile = DeVehicleProfile::findOrFail($vehicleProfileId);
        $parts = [];

        if (class_exists(CarPart::class) && Schema::hasTable('car_parts')) {
            $builder = CarPart::query();

            if ($query !== '') {
                $builder->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('part_number', 'like', "%{$query}%");
                });
            }

            if ($profile->brand) {
                $builder->where(function ($q) use ($profile) {
                    $q->where('compatible_brands', 'like', '%'.$profile->brand.'%')
                        ->orWhere('manufacturer', 'like', '%'.$profile->brand.'%')
                        ->orWhereNull('compatible_brands');
                });
            }

            $parts = $builder->limit(12)->get()->map(fn ($part) => [
                'id' => $part->id,
                'name' => $part->name ?? 'Part',
                'part_number' => $part->part_number ?? null,
                'price' => $part->price ?? null,
                'source' => 'car_parts',
                'vehicle_hint' => trim(($profile->year ? $profile->year.' ' : '').($profile->brand ?? '').' '.($profile->model ?? '')),
            ])->all();
        }

        if ($parts === []) {
            $recommended = is_array($query) ? $query : preg_split('/[\s,;]+/', $query) ?: [];
            foreach (array_filter($recommended) as $term) {
                $parts[] = [
                    'id' => null,
                    'name' => trim((string) $term),
                    'part_number' => null,
                    'price' => null,
                    'source' => 'suggested',
                    'vehicle_hint' => trim(($profile->year ? $profile->year.' ' : '').($profile->brand ?? '').' '.($profile->model ?? '')),
                    'cta' => '/car-parts',
                ];
            }
        }

        return [
            'available' => true,
            'vehicle_profile_id' => $vehicleProfileId,
            'query' => $query,
            'parts' => $parts,
            'cta' => '/car-parts',
        ];
    }

    public function findShops(int $vehicleProfileId, array $location): array
    {
        return $this->placeholder('repair_shops', $vehicleProfileId, [
            'location' => $location,
            'message' => 'Repair shop locator will be connected in a later marketplace release.',
        ]);
    }

    public function findDealers(int $vehicleProfileId): array
    {
        return $this->placeholder('authorized_dealers', $vehicleProfileId, [
            'message' => 'Authorized dealer directory hook is prepared.',
        ]);
    }

    public function getInsuranceQuotes(int $vehicleProfileId): array
    {
        return $this->placeholder('insurance', $vehicleProfileId, [
            'message' => 'Insurance quote integrations are reserved for a future phase.',
        ]);
    }

    public function requestRoadside(int $vehicleProfileId): array
    {
        return $this->placeholder('roadside_assistance', $vehicleProfileId, [
            'message' => 'Roadside assistance request hook is prepared.',
        ]);
    }

    public function bookInspection(int $vehicleProfileId): array
    {
        return $this->placeholder('vehicle_inspection', $vehicleProfileId, [
            'message' => 'Inspection booking hook is prepared.',
        ]);
    }

    private function placeholder(string $service, int $vehicleProfileId, array $extra = []): array
    {
        return array_merge([
            'available' => false,
            'service' => $service,
            'vehicle_profile_id' => $vehicleProfileId,
            'status' => 'not_implemented',
        ], $extra);
    }
}
