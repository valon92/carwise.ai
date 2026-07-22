<?php

namespace App\DiagnosticEcosystem\Services;

use App\DiagnosticEcosystem\Contracts\VehicleHistoryStoreInterface;
use App\DiagnosticEcosystem\DTO\HistoryEvent;
use App\DiagnosticEcosystem\Models\DeAiAnalysis;
use App\DiagnosticEcosystem\Models\DeDiagnosticScan;
use App\DiagnosticEcosystem\Models\DeVehicleHistoryEvent;
use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use RuntimeException;

class VehicleHistoryService implements VehicleHistoryStoreInterface
{
    public function append(int $vehicleProfileId, HistoryEvent $event): HistoryEvent
    {
        $record = DeVehicleHistoryEvent::create([
            'vehicle_profile_id' => $vehicleProfileId,
            'event_type' => $event->eventType,
            'event_date' => now(),
            'mileage' => $event->mileage,
            'title' => $event->title,
            'description' => $event->description,
            'metadata' => $event->metadata,
            'diagnostic_scan_id' => $event->metadata['diagnostic_scan_id'] ?? null,
            'ai_analysis_id' => $event->metadata['ai_analysis_id'] ?? null,
            'created_by_user_id' => $event->metadata['created_by_user_id'] ?? auth()->id(),
            'created_at' => now(),
        ]);

        return new HistoryEvent(
            eventType: $record->event_type,
            title: $record->title,
            description: $record->description,
            mileage: $record->mileage,
            metadata: $record->metadata ?? [],
            id: $record->id,
        );
    }

    public function listForVehicle(int $vehicleProfileId, array $filters = []): array
    {
        $query = DeVehicleHistoryEvent::where('vehicle_profile_id', $vehicleProfileId)
            ->orderByDesc('event_date');

        if (! empty($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        return $query->get()->map(fn (DeVehicleHistoryEvent $event) => new HistoryEvent(
            eventType: $event->event_type,
            title: $event->title,
            description: $event->description,
            mileage: $event->mileage,
            metadata: array_merge($event->metadata ?? [], [
                'event_date' => optional($event->event_date)?->toIso8601String(),
                'diagnostic_scan_id' => $event->diagnostic_scan_id,
                'ai_analysis_id' => $event->ai_analysis_id,
            ]),
            id: $event->id,
        ))->all();
    }

    public function recordsForVehicle(DeVehicleProfile $profile, array $filters = [])
    {
        $query = $profile->historyEvents()->orderByDesc('event_date');

        if (! empty($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        return $query->get();
    }

    public function createManualEvent(DeVehicleProfile $profile, array $data, ?int $userId = null): DeVehicleHistoryEvent
    {
        if (! config('diagnostic-ecosystem.vehicle_history')) {
            throw new RuntimeException('Vehicle history is disabled. Set DE_VEHICLE_HISTORY=true.');
        }

        return DeVehicleHistoryEvent::create([
            'vehicle_profile_id' => $profile->id,
            'event_type' => $data['event_type'] ?? 'service_note',
            'event_date' => $data['event_date'] ?? now(),
            'mileage' => $data['mileage'] ?? $profile->current_mileage,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'metadata' => $data['metadata'] ?? [],
            'created_by_user_id' => $userId ?? auth()->id(),
            'created_at' => now(),
        ]);
    }

    public function logScan(DeVehicleProfile $profile, DeDiagnosticScan $scan, ?int $userId = null): void
    {
        if (! config('diagnostic-ecosystem.vehicle_history')) {
            return;
        }

        $codes = array_merge(
            $scan->engine_dtcs ?? [],
            $scan->abs_errors ?? [],
            $scan->airbag_errors ?? [],
            $scan->transmission_errors ?? [],
        );

        DeVehicleHistoryEvent::create([
            'vehicle_profile_id' => $profile->id,
            'event_type' => 'scan',
            'event_date' => $scan->scan_date ?? now(),
            'mileage' => $scan->mileage ?? $profile->current_mileage,
            'title' => 'Diagnostic scan ('.$scan->source.')',
            'description' => $codes === []
                ? 'Scan saved without DTC codes.'
                : 'Codes: '.implode(', ', $codes),
            'metadata' => [
                'source' => $scan->source,
                'engine_dtcs' => $scan->engine_dtcs,
                'notes' => $scan->notes,
            ],
            'diagnostic_scan_id' => $scan->id,
            'created_by_user_id' => $userId ?? $profile->user_id,
            'created_at' => now(),
        ]);
    }

    public function logAnalysis(DeVehicleProfile $profile, DeAiAnalysis $analysis, ?int $userId = null): void
    {
        if (! config('diagnostic-ecosystem.vehicle_history')) {
            return;
        }

        DeVehicleHistoryEvent::create([
            'vehicle_profile_id' => $profile->id,
            'event_type' => 'ai_analysis',
            'event_date' => now(),
            'mileage' => $profile->current_mileage,
            'title' => 'AI analysis: '.$analysis->severity,
            'description' => $analysis->problem_description,
            'metadata' => [
                'severity' => $analysis->severity,
                'can_continue_driving' => $analysis->can_continue_driving,
                'confidence_score' => $analysis->confidence_score,
            ],
            'diagnostic_scan_id' => $analysis->diagnostic_scan_id,
            'ai_analysis_id' => $analysis->id,
            'created_by_user_id' => $userId ?? $profile->user_id,
            'created_at' => now(),
        ]);
    }

    public function exportJson(DeVehicleProfile $profile): array
    {
        return [
            'vehicle' => [
                'id' => $profile->id,
                'vin' => $profile->vin,
                'nickname' => $profile->nickname,
                'brand' => $profile->brand,
                'model' => $profile->model,
                'year' => $profile->year,
                'current_mileage' => $profile->current_mileage,
            ],
            'exported_at' => now()->toIso8601String(),
            'events' => $this->recordsForVehicle($profile)->toArray(),
        ];
    }

    public function exportPdf(DeVehicleProfile $profile)
    {
        $payload = $this->exportJson($profile);
        $html = view('diagnostic-ecosystem.history-export', [
            'vehicle' => $payload['vehicle'],
            'events' => $payload['events'],
            'exportedAt' => $payload['exported_at'],
        ])->render();

        return Pdf::loadHTML($html)->download('vehicle-twin-'.$profile->id.'-history.pdf');
    }
}
