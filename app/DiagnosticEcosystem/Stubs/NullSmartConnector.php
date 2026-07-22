<?php

namespace App\DiagnosticEcosystem\Stubs;

use App\DiagnosticEcosystem\Contracts\SmartConnectorInterface;
use App\DiagnosticEcosystem\DTO\ConnectorCapabilities;
use App\DiagnosticEcosystem\DTO\ConnectorStatus;

/**
 * Placeholder until CarWise Smart Connector hardware SDK is integrated.
 */
class NullSmartConnector implements SmartConnectorInterface
{
    public function discover(): array
    {
        return [];
    }

    public function connect(string $deviceIdentifier): ConnectorStatus
    {
        return new ConnectorStatus(
            state: 'not_implemented',
            message: 'CarWise Smart Connector hardware integration is not yet available.',
        );
    }

    public function disconnect(): ConnectorStatus
    {
        return new ConnectorStatus(state: 'disconnected');
    }

    public function getStatus(): ConnectorStatus
    {
        return new ConnectorStatus(
            state: 'not_configured',
            message: 'No Smart Connector paired for this vehicle.',
        );
    }

    public function getCapabilities(): ConnectorCapabilities
    {
        return new ConnectorCapabilities;
    }
}
