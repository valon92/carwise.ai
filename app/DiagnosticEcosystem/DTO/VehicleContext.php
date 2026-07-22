<?php

namespace App\DiagnosticEcosystem\DTO;

readonly class VehicleContext
{
    public function __construct(
        public int $vehicleProfileId,
        public string $vin,
        public ?string $manufacturer = null,
        public ?string $model = null,
        public ?int $year = null,
        public ?int $currentMileage = null,
    ) {}
}
