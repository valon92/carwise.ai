<?php

namespace App\DiagnosticEcosystem\Services;

use App\DiagnosticEcosystem\Models\DeConnectorPairing;
use App\DiagnosticEcosystem\Models\DeDiagnosticScan;
use App\DiagnosticEcosystem\Models\DeVehicleProfile;

class DiagnosticScanService
{
    public function listForVehicle(DeVehicleProfile $profile)
    {
        return $profile->diagnosticScans()->latest('scan_date')->get();
    }

    public function createManualScan(DeVehicleProfile $profile, array $data): DeDiagnosticScan
    {
        return DeDiagnosticScan::create([
            'vehicle_profile_id' => $profile->id,
            'connector_pairing_id' => $data['connector_pairing_id'] ?? null,
            'scan_date' => $data['scan_date'] ?? now(),
            'mileage' => $data['mileage'] ?? null,
            'source' => 'manual',
            'engine_dtcs' => $this->normalizeCodes($data['engine_dtcs'] ?? []),
            'abs_errors' => $this->normalizeCodes($data['abs_errors'] ?? []),
            'airbag_errors' => $this->normalizeCodes($data['airbag_errors'] ?? []),
            'transmission_errors' => $this->normalizeCodes($data['transmission_errors'] ?? []),
            'battery_health' => $data['battery_health'] ?? null,
            'oil_life' => $data['oil_life'] ?? null,
            'tire_pressure' => $data['tire_pressure'] ?? null,
            'live_sensor_data' => $data['live_sensor_data'] ?? [],
            'ecu_info' => $data['ecu_info'] ?? null,
            'vehicle_status' => $data['vehicle_status'] ?? null,
            'raw_payload' => [
                'manual' => true,
                'submitted_at' => now()->toIso8601String(),
            ],
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function createConnectorScan(DeVehicleProfile $profile, ?DeConnectorPairing $pairing = null): array
    {
        return [
            'success' => false,
            'message' => 'Connector-based diagnostic reading is not implemented yet. Use manual scan input for now.',
            'pairing_id' => $pairing?->id,
            'vehicle_profile_id' => $profile->id,
        ];
    }

    private function normalizeCodes(array|string|null $codes): array
    {
        if (is_string($codes)) {
            $codes = preg_split('/[\s,;]+/', trim($codes)) ?: [];
        }

        return array_values(array_filter(array_map(
            fn ($code) => strtoupper(trim((string) $code)),
            is_array($codes) ? $codes : []
        )));
    }
}
