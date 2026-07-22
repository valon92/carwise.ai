<?php

namespace App\DiagnosticEcosystem\Services;

use App\DiagnosticEcosystem\Contracts\PredictiveMaintenanceInterface;
use App\DiagnosticEcosystem\DTO\MaintenanceRecommendation as MaintenanceRecommendationDto;
use App\DiagnosticEcosystem\Models\DeMaintenanceRecommendation;
use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use RuntimeException;

class PredictiveMaintenanceService implements PredictiveMaintenanceInterface
{
    public function generateForVehicle(int $vehicleProfileId): array
    {
        $profile = DeVehicleProfile::findOrFail($vehicleProfileId);

        return $this->generateAndPersist($profile)
            ->map(fn (DeMaintenanceRecommendation $item) => new MaintenanceRecommendationDto(
                recommendationType: $item->recommendation_type,
                title: $item->title,
                description: $item->description,
                priority: $item->priority,
                dueAtMileage: $item->due_at_mileage,
                dueAtDate: optional($item->due_at_date)?->toDateString(),
                reasoning: $item->reasoning,
                status: $item->status,
                source: $item->source,
                id: $item->id,
            ))
            ->all();
    }

    public function generateAndPersist(DeVehicleProfile $profile)
    {
        if (! config('diagnostic-ecosystem.predictive_maintenance')) {
            throw new RuntimeException('Predictive maintenance is disabled. Set DE_PREDICTIVE_ENABLED=true.');
        }

        $mileage = (int) ($profile->current_mileage ?? 0);
        $candidates = $this->buildCandidates($mileage, $profile);

        foreach ($candidates as $candidate) {
            $exists = DeMaintenanceRecommendation::where('vehicle_profile_id', $profile->id)
                ->where('recommendation_type', $candidate['recommendation_type'])
                ->whereIn('status', ['pending', 'scheduled'])
                ->exists();

            if ($exists) {
                continue;
            }

            DeMaintenanceRecommendation::create([
                ...$candidate,
                'vehicle_profile_id' => $profile->id,
                'status' => 'pending',
            ]);
        }

        return DeMaintenanceRecommendation::where('vehicle_profile_id', $profile->id)
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->latest()
            ->get();
    }

    public function listForVehicle(DeVehicleProfile $profile, ?string $status = null)
    {
        $query = DeMaintenanceRecommendation::where('vehicle_profile_id', $profile->id)->latest();

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function updateStatus(DeMaintenanceRecommendation $recommendation, string $status): DeMaintenanceRecommendation
    {
        if (! in_array($status, ['pending', 'scheduled', 'completed', 'dismissed'], true)) {
            throw new RuntimeException('Invalid recommendation status.');
        }

        $recommendation->update(['status' => $status]);

        return $recommendation->fresh();
    }

    private function buildCandidates(int $mileage, DeVehicleProfile $profile): array
    {
        $brand = $profile->brand ?? $profile->manufacturer ?? 'Vehicle';
        $items = [
            [
                'recommendation_type' => 'oil_service',
                'title' => 'Oil service',
                'description' => 'Replace engine oil and filter according to service interval.',
                'priority' => $mileage >= 10000 ? 'high' : 'medium',
                'due_at_mileage' => $mileage > 0 ? $mileage + max(1000, 15000 - ($mileage % 15000)) : 15000,
                'due_at_date' => now()->addMonths(6)->toDateString(),
                'reasoning' => 'Standard oil interval based on mileage progression for '.$brand.'.',
                'source' => 'vin_schedule',
            ],
            [
                'recommendation_type' => 'brake_inspection',
                'title' => 'Brake inspection',
                'description' => 'Inspect pads, discs, and brake fluid condition.',
                'priority' => $mileage >= 40000 ? 'high' : 'medium',
                'due_at_mileage' => $mileage + 5000,
                'due_at_date' => now()->addMonths(8)->toDateString(),
                'reasoning' => 'Preventive brake checks reduce safety risk as mileage increases.',
                'source' => 'ai_predictive',
            ],
            [
                'recommendation_type' => 'battery',
                'title' => 'Battery health check',
                'description' => 'Test battery voltage and charging system.',
                'priority' => 'medium',
                'due_at_mileage' => null,
                'due_at_date' => now()->addMonths(4)->toDateString(),
                'reasoning' => 'Battery failures are common after seasonal temperature changes.',
                'source' => 'ai_predictive',
            ],
        ];

        if ($mileage >= 80000) {
            $items[] = [
                'recommendation_type' => 'timing_chain',
                'title' => 'Timing chain / belt inspection',
                'description' => 'Inspect timing components for wear and noise.',
                'priority' => 'high',
                'due_at_mileage' => $mileage + 2000,
                'due_at_date' => now()->addMonths(3)->toDateString(),
                'reasoning' => 'Higher mileage vehicles benefit from proactive timing system checks.',
                'source' => 'ai_predictive',
            ];
        }

        if (str_contains(strtolower((string) $profile->fuel_type), 'diesel') || $mileage >= 60000) {
            $items[] = [
                'recommendation_type' => 'dpf_cleaning',
                'title' => 'DPF / emissions system check',
                'description' => 'Check DPF load and emissions-related sensors.',
                'priority' => 'medium',
                'due_at_mileage' => $mileage + 3000,
                'due_at_date' => now()->addMonths(5)->toDateString(),
                'reasoning' => 'Emission systems degrade gradually and benefit from early inspection.',
                'source' => 'ai_predictive',
            ];
        }

        if ($mileage >= 90000) {
            $items[] = [
                'recommendation_type' => 'turbo_inspection',
                'title' => 'Turbo inspection',
                'description' => 'Inspect turbocharger play, oil feed, and boost leaks.',
                'priority' => 'medium',
                'due_at_mileage' => $mileage + 4000,
                'due_at_date' => now()->addMonths(6)->toDateString(),
                'reasoning' => 'Turbo wear risk rises with sustained high mileage operation.',
                'source' => 'ai_predictive',
            ];
        }

        return $items;
    }
}
