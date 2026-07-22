<?php

namespace App\DiagnosticEcosystem\Services;

use App\DiagnosticEcosystem\Contracts\DiagnosticAnalysisProviderInterface;
use App\DiagnosticEcosystem\DTO\DiagnosticSnapshot;
use App\DiagnosticEcosystem\DTO\VehicleContext;
use App\DiagnosticEcosystem\Models\DeAiAnalysis;
use App\DiagnosticEcosystem\Models\DeDiagnosticScan;
use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use RuntimeException;

class DiagnosticAnalysisService
{
    public function __construct(
        private readonly DiagnosticAnalysisProviderInterface $analysisProvider,
    ) {}

    public function analyzeScan(DeDiagnosticScan $scan, DeVehicleProfile $profile): DeAiAnalysis
    {
        if (! config('diagnostic-ecosystem.ai_assistant')) {
            throw new RuntimeException('AI Diagnostic Assistant is disabled. Set DE_AI_ASSISTANT=true.');
        }

        $snapshot = new DiagnosticSnapshot(
            vehicleProfileId: $profile->id,
            source: $scan->source ?? 'manual',
            mileage: $scan->mileage,
            engineDtcs: $scan->engine_dtcs ?? [],
            absErrors: $scan->abs_errors ?? [],
            airbagErrors: $scan->airbag_errors ?? [],
            transmissionErrors: $scan->transmission_errors ?? [],
            batteryHealth: $scan->battery_health,
            oilLife: $scan->oil_life,
            tirePressure: $scan->tire_pressure,
            liveSensorData: $scan->live_sensor_data ?? [],
            ecuInfo: $scan->ecu_info,
            vehicleStatus: $scan->vehicle_status,
            rawPayload: $scan->raw_payload ?? [],
        );

        $context = new VehicleContext(
            vehicleProfileId: $profile->id,
            vin: $profile->vin,
            manufacturer: $profile->brand ?? $profile->manufacturer,
            model: $profile->model,
            year: $profile->year,
            currentMileage: $profile->current_mileage,
        );

        $analysis = $this->analysisProvider->analyze($snapshot, $context);
        $payload = $analysis->toArray();

        $record = DeAiAnalysis::create([
            'diagnostic_scan_id' => $scan->id,
            'vehicle_profile_id' => $profile->id,
            'provider' => 'ai_diagnosis_service',
            'problem_description' => $payload['problem_description'],
            'severity' => $payload['severity'],
            'possible_causes' => $payload['possible_causes'],
            'repair_procedure' => $payload['repair_procedure'],
            'estimated_repair_cost_min' => $payload['estimated_repair_cost_min'],
            'estimated_repair_cost_max' => $payload['estimated_repair_cost_max'],
            'estimated_repair_time_hours' => $payload['estimated_repair_time_hours'],
            'recommended_parts' => $payload['recommended_parts'],
            'safety_recommendation' => $payload['safety_recommendation'],
            'can_continue_driving' => $payload['can_continue_driving'],
            'confidence_score' => $payload['confidence_score'],
            'raw_response' => $payload,
        ]);

        app(VehicleHistoryService::class)->logAnalysis($profile, $record);

        return $record;
    }

    public function latestForScan(DeDiagnosticScan $scan): ?DeAiAnalysis
    {
        return DeAiAnalysis::where('diagnostic_scan_id', $scan->id)
            ->latest()
            ->first();
    }

    public function listForVehicle(DeVehicleProfile $profile)
    {
        return DeAiAnalysis::where('vehicle_profile_id', $profile->id)
            ->latest()
            ->get();
    }
}
