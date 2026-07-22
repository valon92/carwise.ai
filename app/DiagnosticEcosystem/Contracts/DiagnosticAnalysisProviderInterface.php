<?php

namespace App\DiagnosticEcosystem\Contracts;

use App\DiagnosticEcosystem\DTO\DiagnosticAnalysis;
use App\DiagnosticEcosystem\DTO\DiagnosticSnapshot;
use App\DiagnosticEcosystem\DTO\VehicleContext;

/**
 * AI-powered interpretation of diagnostic data (Step 5).
 *
 * Implementation delegates to AiDiagnosisAdapter → existing AIDiagnosisService.
 */
interface DiagnosticAnalysisProviderInterface
{
    public function analyze(DiagnosticSnapshot $snapshot, VehicleContext $vehicle): DiagnosticAnalysis;
}
