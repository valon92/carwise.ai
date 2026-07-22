<?php

namespace App\DiagnosticEcosystem\DTO;

readonly class VinDecodeResult
{
    public function __construct(
        public string $vin,
        public string $provider,
        public ?string $manufacturer = null,
        public ?string $brand = null,
        public ?string $model = null,
        public ?int $year = null,
        public ?string $engine = null,
        public ?string $fuelType = null,
        public ?string $transmission = null,
        public ?int $horsepower = null,
        public array $specifications = [],
        public array $factoryEquipment = [],
        public array $vehicleOptions = [],
        public array $serviceSchedule = [],
        public array $recalls = [],
        public ?array $warranty = null,
        public array $rawResponse = [],
    ) {}

    public function toArray(): array
    {
        return [
            'vin' => $this->vin,
            'provider' => $this->provider,
            'manufacturer' => $this->manufacturer,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'engine' => $this->engine,
            'fuel_type' => $this->fuelType,
            'transmission' => $this->transmission,
            'horsepower' => $this->horsepower,
            'specifications' => $this->specifications,
            'factory_equipment' => $this->factoryEquipment,
            'vehicle_options' => $this->vehicleOptions,
            'service_schedule' => $this->serviceSchedule,
            'recalls' => $this->recalls,
            'warranty' => $this->warranty,
        ];
    }
}
