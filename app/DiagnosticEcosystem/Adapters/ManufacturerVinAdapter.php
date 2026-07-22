<?php

namespace App\DiagnosticEcosystem\Adapters;

use App\DiagnosticEcosystem\Contracts\VinIdentificationProviderInterface;
use App\DiagnosticEcosystem\DTO\VinDecodeResult;

class ManufacturerVinAdapter implements VinIdentificationProviderInterface
{
    public function getName(): string
    {
        return 'manufacturer';
    }

    public function isAvailable(): bool
    {
        return false;
    }

    public function decode(string $vin): ?VinDecodeResult
    {
        return null;
    }
}
