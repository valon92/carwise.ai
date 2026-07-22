<?php

namespace App\DiagnosticEcosystem\Contracts;

use App\DiagnosticEcosystem\DTO\MaintenanceRecommendation;

/**
 * AI-driven preventive maintenance recommendations (Step 7).
 */
interface PredictiveMaintenanceInterface
{
    /**
     * @return MaintenanceRecommendation[]
     */
    public function generateForVehicle(int $vehicleProfileId): array;
}
