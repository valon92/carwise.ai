<?php

namespace App\DiagnosticEcosystem\DTO;

readonly class DiagnosticAnalysis
{
    public function __construct(
        public string $problemDescription,
        public string $severity,
        public array $possibleCauses,
        public ?string $repairProcedure = null,
        public ?float $estimatedRepairCostMin = null,
        public ?float $estimatedRepairCostMax = null,
        public ?float $estimatedRepairTimeHours = null,
        public array $recommendedParts = [],
        public ?string $safetyRecommendation = null,
        public bool $canContinueDriving = true,
        public ?float $confidenceScore = null,
    ) {}

    public function toArray(): array
    {
        return [
            'problem_description' => $this->problemDescription,
            'severity' => $this->severity,
            'possible_causes' => $this->possibleCauses,
            'repair_procedure' => $this->repairProcedure,
            'estimated_repair_cost_min' => $this->estimatedRepairCostMin,
            'estimated_repair_cost_max' => $this->estimatedRepairCostMax,
            'estimated_repair_time_hours' => $this->estimatedRepairTimeHours,
            'recommended_parts' => $this->recommendedParts,
            'safety_recommendation' => $this->safetyRecommendation,
            'can_continue_driving' => $this->canContinueDriving,
            'confidence_score' => $this->confidenceScore,
        ];
    }
}
