<?php

namespace App\DiagnosticEcosystem\Adapters;

use App\DiagnosticEcosystem\Contracts\DiagnosticAnalysisProviderInterface;
use App\DiagnosticEcosystem\DTO\DiagnosticAnalysis;
use App\DiagnosticEcosystem\DTO\DiagnosticSnapshot;
use App\DiagnosticEcosystem\DTO\VehicleContext;
use App\Services\AIDiagnosisService;

/**
 * Bridges Diagnostic Ecosystem analysis to the existing AIDiagnosisService.
 * Does not modify AIDiagnosisService.
 */
class AiDiagnosisAdapter implements DiagnosticAnalysisProviderInterface
{
    public function __construct(
        private readonly AIDiagnosisService $aiDiagnosisService,
    ) {}

    public function analyze(DiagnosticSnapshot $snapshot, VehicleContext $vehicle): DiagnosticAnalysis
    {
        $payload = $this->buildPayload($snapshot, $vehicle);
        $raw = $this->aiDiagnosisService->analyzeDiagnosis($payload);

        return $this->mapToAnalysis($raw, $snapshot);
    }

    private function buildPayload(DiagnosticSnapshot $snapshot, VehicleContext $vehicle): array
    {
        $codes = array_values(array_unique(array_filter(array_merge(
            $snapshot->engineDtcs,
            $snapshot->absErrors,
            $snapshot->airbagErrors,
            $snapshot->transmissionErrors,
        ))));

        $symptoms = [];
        if ($codes !== []) {
            $symptoms[] = 'Warning lights on';
            $symptoms[] = 'Diagnostic trouble codes present: '.implode(', ', $codes);
        }
        if ($snapshot->absErrors !== []) {
            $symptoms[] = 'ABS system faults';
        }
        if ($snapshot->airbagErrors !== []) {
            $symptoms[] = 'Airbag / SRS faults';
        }
        if ($snapshot->transmissionErrors !== []) {
            $symptoms[] = 'Transmission faults';
        }

        $descriptionParts = [
            'OBD / diagnostic scan analysis request.',
            'Scan source: '.$snapshot->source.'.',
        ];

        if ($codes !== []) {
            $descriptionParts[] = 'Trouble codes: '.implode(', ', $codes).'.';
        } else {
            $descriptionParts[] = 'No DTC codes reported; perform general health assessment.';
        }

        if ($snapshot->engineDtcs !== []) {
            $descriptionParts[] = 'Engine DTCs: '.implode(', ', $snapshot->engineDtcs).'.';
        }
        if ($snapshot->absErrors !== []) {
            $descriptionParts[] = 'ABS errors: '.implode(', ', $snapshot->absErrors).'.';
        }
        if ($snapshot->airbagErrors !== []) {
            $descriptionParts[] = 'Airbag errors: '.implode(', ', $snapshot->airbagErrors).'.';
        }
        if ($snapshot->transmissionErrors !== []) {
            $descriptionParts[] = 'Transmission errors: '.implode(', ', $snapshot->transmissionErrors).'.';
        }

        return [
            'symptoms' => $symptoms,
            'description' => implode(' ', $descriptionParts),
            'problem_description' => implode(' ', $descriptionParts),
            'car_brand' => $vehicle->manufacturer ?? 'Unknown',
            'car_model' => $vehicle->model ?? 'Unknown',
            'car_year' => $vehicle->year ?? 'Unknown',
            'mileage' => $snapshot->mileage ?? $vehicle->currentMileage ?? 'Unknown',
            'engine_type' => 'Unknown',
            'vin' => $vehicle->vin,
            'user_language' => 'en',
            'diagnostic_codes' => $codes,
            'vehicle_info' => [
                'make' => $vehicle->manufacturer ?? 'Unknown',
                'model' => $vehicle->model ?? 'Unknown',
                'year' => $vehicle->year ?? 'Unknown',
                'mileage' => $snapshot->mileage ?? $vehicle->currentMileage ?? 'Unknown',
                'vin' => $vehicle->vin,
            ],
        ];
    }

    private function mapToAnalysis(array $raw, DiagnosticSnapshot $snapshot): DiagnosticAnalysis
    {
        $possibleCauses = $this->normalizeCauses($raw['likely_causes'] ?? $raw['possible_causes'] ?? []);
        $recommendedParts = $this->normalizeParts($raw['recommended_actions'] ?? $raw['recommended_parts'] ?? []);
        [$costMin, $costMax] = $this->extractCostRange($raw['estimated_costs'] ?? []);

        $severity = strtolower((string) ($raw['severity'] ?? 'medium'));
        if (! in_array($severity, ['low', 'medium', 'high', 'critical'], true)) {
            $severity = 'medium';
        }

        $requiresImmediate = (bool) ($raw['requires_immediate_attention'] ?? false);
        $canContinueDriving = ! $requiresImmediate && ! in_array($severity, ['high', 'critical'], true);

        if ($snapshot->airbagErrors !== [] || $snapshot->absErrors !== []) {
            $canContinueDriving = false;
            if ($severity === 'low' || $severity === 'medium') {
                $severity = 'high';
            }
        }

        $confidence = $raw['confidence_score'] ?? null;
        if (is_numeric($confidence) && (float) $confidence > 1) {
            $confidence = ((float) $confidence) / 100;
        }

        $repairProcedure = null;
        if (! empty($raw['recommended_actions']) && is_array($raw['recommended_actions'])) {
            $steps = [];
            foreach ($raw['recommended_actions'] as $action) {
                if (is_array($action)) {
                    $title = $action['title'] ?? '';
                    $description = $action['description'] ?? '';
                    $steps[] = trim($title.($description ? ': '.$description : ''));
                } elseif (is_string($action)) {
                    $steps[] = $action;
                }
            }
            $repairProcedure = implode("\n", array_filter($steps));
        }

        $safety = $raw['safety_recommendation'] ?? null;
        if (! $safety) {
            $safety = $canContinueDriving
                ? 'Vehicle may continue limited driving, but schedule inspection soon.'
                : 'Do not continue normal driving until a qualified technician inspects the vehicle.';
        }

        return new DiagnosticAnalysis(
            problemDescription: (string) ($raw['problem_description'] ?? $raw['problem_title'] ?? 'Diagnostic analysis completed.'),
            severity: $severity,
            possibleCauses: $possibleCauses,
            repairProcedure: $repairProcedure,
            estimatedRepairCostMin: $costMin,
            estimatedRepairCostMax: $costMax,
            estimatedRepairTimeHours: $this->estimateHours($severity),
            recommendedParts: $recommendedParts,
            safetyRecommendation: $safety,
            canContinueDriving: $canContinueDriving,
            confidenceScore: is_numeric($confidence) ? (float) $confidence : null,
        );
    }

    private function normalizeCauses(array $causes): array
    {
        return array_values(array_filter(array_map(function ($cause) {
            if (is_string($cause)) {
                return $cause;
            }
            if (! is_array($cause)) {
                return null;
            }

            $title = $cause['title'] ?? $cause['name'] ?? null;
            $description = $cause['description'] ?? null;
            $probability = $cause['probability'] ?? null;

            if (! $title && ! $description) {
                return null;
            }

            $label = trim(($title ?? '').($description ? ' — '.$description : ''));
            if ($probability !== null) {
                $label .= " ({$probability}%)";
            }

            return $label;
        }, $causes)));
    }

    private function normalizeParts(array $actions): array
    {
        return array_values(array_filter(array_map(function ($action) {
            if (is_string($action)) {
                return $action;
            }
            if (! is_array($action)) {
                return null;
            }

            return $action['title'] ?? $action['name'] ?? $action['service'] ?? null;
        }, $actions)));
    }

    private function extractCostRange(array $costs): array
    {
        $mins = [];
        $maxs = [];

        foreach ($costs as $cost) {
            if (! is_array($cost)) {
                continue;
            }
            if (isset($cost['min']) && is_numeric($cost['min'])) {
                $mins[] = (float) $cost['min'];
            }
            if (isset($cost['max']) && is_numeric($cost['max'])) {
                $maxs[] = (float) $cost['max'];
            }
        }

        return [
            $mins === [] ? null : min($mins),
            $maxs === [] ? null : max($maxs),
        ];
    }

    private function estimateHours(string $severity): float
    {
        return match ($severity) {
            'low' => 1.0,
            'medium' => 2.5,
            'high' => 4.0,
            'critical' => 6.0,
            default => 2.0,
        };
    }
}
