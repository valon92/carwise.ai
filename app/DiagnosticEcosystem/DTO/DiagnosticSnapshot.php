<?php

namespace App\DiagnosticEcosystem\DTO;

readonly class DiagnosticSnapshot
{
    public function __construct(
        public int $vehicleProfileId,
        public string $source,
        public ?int $mileage = null,
        public array $engineDtcs = [],
        public array $absErrors = [],
        public array $airbagErrors = [],
        public array $transmissionErrors = [],
        public ?array $batteryHealth = null,
        public ?array $oilLife = null,
        public ?array $tirePressure = null,
        public array $liveSensorData = [],
        public ?array $ecuInfo = null,
        public ?array $vehicleStatus = null,
        public array $rawPayload = [],
    ) {}

    public function toArray(): array
    {
        return [
            'vehicle_profile_id' => $this->vehicleProfileId,
            'source' => $this->source,
            'mileage' => $this->mileage,
            'engine_dtcs' => $this->engineDtcs,
            'abs_errors' => $this->absErrors,
            'airbag_errors' => $this->airbagErrors,
            'transmission_errors' => $this->transmissionErrors,
            'battery_health' => $this->batteryHealth,
            'oil_life' => $this->oilLife,
            'tire_pressure' => $this->tirePressure,
            'live_sensor_data' => $this->liveSensorData,
            'ecu_info' => $this->ecuInfo,
            'vehicle_status' => $this->vehicleStatus,
            'raw_payload' => $this->rawPayload,
        ];
    }
}
