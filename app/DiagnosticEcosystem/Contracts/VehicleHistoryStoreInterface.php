<?php

namespace App\DiagnosticEcosystem\Contracts;

use App\DiagnosticEcosystem\DTO\HistoryEvent;

/**
 * Append-only vehicle history ledger (Step 6).
 */
interface VehicleHistoryStoreInterface
{
    public function append(int $vehicleProfileId, HistoryEvent $event): HistoryEvent;

    /**
     * @return HistoryEvent[]
     */
    public function listForVehicle(int $vehicleProfileId, array $filters = []): array;
}
