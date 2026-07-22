# CarWise Smart Connector — Integration Guide

Status: architecture ready, hardware protocol not implemented.

## Scope

The Diagnostic Ecosystem supports future communication with a CarWise Smart Connector (OBD-II Wi-Fi/Bluetooth adapter).

Current software provides:

- `SmartConnectorInterface` contract
- `NullSmartConnector` stub
- `de_connector_pairings` persistence
- Pair / status / revoke API endpoints
- Vehicle Twin placeholder UI

## Planned device flow

1. User opens Vehicle Twin panel
2. App requests connector discovery (`discover`)
3. User selects device identifier
4. App pairs device (`connect`) and stores pairing row
5. App requests diagnostic snapshot via `DiagnosticReadCapabilityInterface`
6. Snapshot is stored in `de_diagnostic_scans`
7. AI analysis and history logging run automatically

## Not implemented yet

- BLE/Wi-Fi transport
- ELM327 / manufacturer OBD framing
- Live PID streaming
- Secure device attestation

## Feature flags

```env
DE_CONNECTOR_ENABLED=true
DE_DIAGNOSTIC_READ_ENABLED=true
```

Until a hardware SDK is integrated, connector scan endpoints return structured `not_implemented` / `501` responses. Manual scan entry remains the supported path.
