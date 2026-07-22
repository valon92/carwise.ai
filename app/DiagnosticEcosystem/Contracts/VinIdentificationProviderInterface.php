<?php

namespace App\DiagnosticEcosystem\Contracts;

use App\DiagnosticEcosystem\DTO\VinDecodeResult;

/**
 * Resolves vehicle identity and specifications from a VIN.
 *
 * Implementations: NhtsaVinProvider, CarApiVinProvider, ManufacturerVinProvider.
 * Orchestrated by VinIdentificationService (Phase 2).
 */
interface VinIdentificationProviderInterface
{
    public function getName(): string;

    public function isAvailable(): bool;

    public function decode(string $vin): ?VinDecodeResult;
}
