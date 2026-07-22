<?php

namespace App\DiagnosticEcosystem\Stubs;

use App\DiagnosticEcosystem\Contracts\DiagnosticReadCapabilityInterface;
use App\DiagnosticEcosystem\DTO\DiagnosticSnapshot;

/**
 * Placeholder until OBD-II diagnostic protocol is implemented.
 */
class NullDiagnosticReader implements DiagnosticReadCapabilityInterface
{
    public function __construct(
        private readonly int $vehicleProfileId,
    ) {}

    public function readDtcs(): array
    {
        return [];
    }

    public function readLiveData(): array
    {
        return [];
    }

    public function readEcuInfo(): array
    {
        return [];
    }

    public function readVehicleStatus(): array
    {
        return [];
    }

    public function captureSnapshot(): DiagnosticSnapshot
    {
        return new DiagnosticSnapshot(
            vehicleProfileId: $this->vehicleProfileId,
            source: 'not_implemented',
        );
    }
}
