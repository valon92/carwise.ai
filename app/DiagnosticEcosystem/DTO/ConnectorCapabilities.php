<?php

namespace App\DiagnosticEcosystem\DTO;

readonly class ConnectorCapabilities
{
    public function __construct(
        public array $supportedProtocols = [],
        public array $supportedPids = [],
        public bool $supportsLiveData = false,
        public bool $supportsDtcs = false,
    ) {}

    public function toArray(): array
    {
        return [
            'supported_protocols' => $this->supportedProtocols,
            'supported_pids' => $this->supportedPids,
            'supports_live_data' => $this->supportsLiveData,
            'supports_dtcs' => $this->supportsDtcs,
        ];
    }
}
