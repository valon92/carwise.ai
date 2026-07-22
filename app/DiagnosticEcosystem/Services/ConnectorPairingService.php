<?php

namespace App\DiagnosticEcosystem\Services;

use App\DiagnosticEcosystem\Contracts\SmartConnectorInterface;
use App\DiagnosticEcosystem\Models\DeConnectorPairing;
use App\DiagnosticEcosystem\Models\DeVehicleProfile;

class ConnectorPairingService
{
    public function __construct(
        private readonly SmartConnectorInterface $smartConnector,
    ) {}

    public function getOrCreateForVehicle(DeVehicleProfile $profile): DeConnectorPairing
    {
        return DeConnectorPairing::firstOrCreate(
            ['vehicle_profile_id' => $profile->id],
            [
                'connector_type' => 'carwise_smart_connector',
                'status' => $this->smartConnector->getStatus()->state,
                'capabilities' => $this->smartConnector->getCapabilities()->toArray(),
            ]
        );
    }

    public function getStatus(DeVehicleProfile $profile): array
    {
        $pairing = $this->getOrCreateForVehicle($profile);
        $status = $this->smartConnector->getStatus();
        $capabilities = $this->smartConnector->getCapabilities();

        $pairing->update([
            'status' => $status->state,
            'capabilities' => $capabilities->toArray(),
        ]);

        return [
            'pairing' => $pairing->fresh(),
            'status' => $status->toArray(),
            'capabilities' => $capabilities->toArray(),
        ];
    }

    public function pair(DeVehicleProfile $profile, ?string $deviceIdentifier = null): array
    {
        $pairing = $this->getOrCreateForVehicle($profile);
        $status = $deviceIdentifier
            ? $this->smartConnector->connect($deviceIdentifier)
            : $this->smartConnector->getStatus();

        $pairing->update([
            'device_identifier' => $deviceIdentifier,
            'status' => $status->state,
            'pairing_token' => $status->message,
        ]);

        return [
            'pairing' => $pairing->fresh(),
            'status' => $status->toArray(),
        ];
    }

    public function revoke(DeVehicleProfile $profile): array
    {
        $pairing = $this->getOrCreateForVehicle($profile);
        $status = $this->smartConnector->disconnect();

        $pairing->update([
            'status' => 'revoked',
            'pairing_token' => null,
            'last_connected_at' => null,
        ]);

        return [
            'pairing' => $pairing->fresh(),
            'status' => $status->toArray(),
        ];
    }
}
