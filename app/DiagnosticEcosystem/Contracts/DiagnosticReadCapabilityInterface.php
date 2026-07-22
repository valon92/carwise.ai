<?php

namespace App\DiagnosticEcosystem\Contracts;

use App\DiagnosticEcosystem\DTO\DiagnosticSnapshot;

/**
 * Reads diagnostic data from a connected vehicle via Smart Connector.
 *
 * Architecture stub only. No OBD-II/ELM327 protocol in Phase 0–4.
 */
interface DiagnosticReadCapabilityInterface
{
    public function readDtcs(): array;

    public function readLiveData(): array;

    public function readEcuInfo(): array;

    public function readVehicleStatus(): array;

    public function captureSnapshot(): DiagnosticSnapshot;
}
