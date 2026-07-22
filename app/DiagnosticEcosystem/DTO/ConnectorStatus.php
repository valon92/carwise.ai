<?php

namespace App\DiagnosticEcosystem\DTO;

readonly class ConnectorStatus
{
    public function __construct(
        public string $state,
        public ?string $deviceIdentifier = null,
        public ?string $message = null,
    ) {}

    public function toArray(): array
    {
        return [
            'state' => $this->state,
            'device_identifier' => $this->deviceIdentifier,
            'message' => $this->message,
        ];
    }
}
