<?php

namespace App\DiagnosticEcosystem\Contracts;

use App\DiagnosticEcosystem\DTO\ConnectorCapabilities;
use App\DiagnosticEcosystem\DTO\ConnectorStatus;

/**
 * CarWise Smart Connector — OBD-II Wi-Fi/Bluetooth adapter.
 *
 * Architecture stub only. No hardware protocol in Phase 0–3.
 * Future: Obd2WifiConnector, Obd2BleConnector.
 */
interface SmartConnectorInterface
{
    public function discover(): array;

    public function connect(string $deviceIdentifier): ConnectorStatus;

    public function disconnect(): ConnectorStatus;

    public function getStatus(): ConnectorStatus;

    public function getCapabilities(): ConnectorCapabilities;
}
