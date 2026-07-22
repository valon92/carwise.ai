# Diagnostic Ecosystem — Implementation Roadmap

Phased delivery matching Steps 1–8. Each phase is independently deployable behind feature flags.

---

## Phase 0 — Architecture Scaffold (current)

**Goal:** Prepare module structure without affecting production behavior.

| Task | Status |
|------|--------|
| Architecture documentation (`docs/diagnostic-ecosystem/`) | Done |
| Contract interfaces (`app/DiagnosticEcosystem/Contracts/`) | Done |
| DTO value objects (`app/DiagnosticEcosystem/DTO/`) | Done |
| `config/diagnostic-ecosystem.php` with all flags `false` | Done |
| `DiagnosticEcosystemServiceProvider` (registers config + empty routes) | Done |
| Register provider in `bootstrap/providers.php` | Pending approval |
| Frontend folder scaffold `resources/js/diagnostic-ecosystem/README.md` | Done |

**Exit criteria:** `DE_ENABLED=false` — zero user-visible changes.

---

## Phase 1 — Vehicle Registration (Step 1)

**Flag:** `DE_ENABLED=true`, `DE_VIN_IDENTIFICATION=false`

| Deliverable |
|-------------|
| Migration: `de_vehicle_profiles` |
| Model: `DeVehicleProfile` |
| Controller: `VehicleProfileController` |
| Service: `VehicleRegistrationService` |
| Routes: CRUD `/api/de/vehicles` |
| Vue: `VehicleTwinRegister.vue`, `VehicleTwinList.vue` |
| Route: `/vehicle-twin` (lazy-loaded, auth required) |

**Does not touch:** `cars` table, `MyCars.vue`, existing car API.

---

## Phase 2 — VIN Identification (Step 2)

**Flag:** `DE_VIN_IDENTIFICATION=true`

| Deliverable |
|-------------|
| Migration: `de_vin_decodes` |
| Adapters: `NhtsaVinAdapter`, `CarApiVinAdapter`, `ManufacturerVinAdapter` |
| Service: `VinIdentificationService` (provider chain) |
| Auto-identify on vehicle create |
| Endpoints: `identify`, `vin-history`, `vin/preview` |
| Vue: VIN decode result card on vehicle detail |

**Reuses:** `PublicAPIService`, `CarAPIService`, `CarManufacturerAPIService` via adapters.

---

## Phase 3 — Smart Connector Architecture (Step 3)

**Flag:** `DE_CONNECTOR_ENABLED=true` (stubs return structured not-implemented)

| Deliverable |
|-------------|
| Migration: `de_connector_pairings` |
| Stub: `NullSmartConnector` implementing `SmartConnectorInterface` |
| Endpoints: connector status/pair/revoke (501 until hardware SDK) |
| Vue: "Connect CarWise Smart Connector" placeholder UI |
| Documentation: OBD-II protocol integration guide |

**No hardware communication.**

---

## Phase 4 — Diagnostic Reading Architecture (Step 4)

**Flag:** `DE_DIAGNOSTIC_READ_ENABLED=true`

| Deliverable |
|-------------|
| Migration: `de_diagnostic_scans` |
| Stub: `NullDiagnosticReader` implementing `DiagnosticReadCapabilityInterface` |
| Manual scan entry endpoint (DTC codes + notes) |
| DTO normalization: `DiagnosticSnapshot` |
| Vue: manual scan form + scan history list |

**No OBD protocol implementation.**

---

## Phase 5 — AI Diagnostic Assistant (Step 5)

**Flag:** `DE_AI_ASSISTANT=true`

| Deliverable |
|-------------|
| Migration: `de_ai_analyses` |
| Adapter: `AiDiagnosisAdapter` → existing `AIDiagnosisService` |
| Service: `DiagnosticAnalysisService` with module-specific prompts |
| Endpoint: `POST /scans/{id}/analyze` |
| Vue: AI analysis panel with severity, causes, cost, safety |

**Does not modify:** `AIDiagnosisService`, `Diagnose.vue`, existing diagnosis routes.

---

## Phase 6 — Vehicle History (Step 6)

**Flag:** `DE_VEHICLE_HISTORY=true`

| Deliverable |
|-------------|
| Migration: `de_vehicle_history_events` |
| Service: `VehicleHistoryService` (append-only) |
| Auto-log events from scans + AI analyses |
| Manual event entry |
| History timeline Vue component |
| Export JSON/PDF |

---

## Phase 7 — Predictive Maintenance (Step 7)

**Flag:** `DE_PREDICTIVE_ENABLED=true`

| Deliverable |
|-------------|
| Migration: `de_maintenance_recommendations` |
| Service: `PredictiveMaintenanceService` |
| AI + VIN service schedule + mileage inputs |
| Recommendations dashboard on vehicle detail |
| Dismiss/schedule/complete workflow |

---

## Phase 8 — Marketplace Integrations (Step 8)

**Flag:** `DE_MARKETPLACE_HOOKS=true`

| Deliverable |
|-------------|
| Adapters: `PartsMarketplaceAdapter`, `ShopLocatorAdapter` (stubs) |
| Parts search bridged to existing marketplace API |
| Placeholder endpoints for shops, dealers, insurance, roadside |
| "Find parts for this vehicle" CTA on AI analysis |

---

## Dependency Graph

```
Phase 0 (scaffold)
    │
    ▼
Phase 1 (registration) ──► Phase 2 (VIN ID)
    │                            │
    │                            ▼
    │                       Phase 3 (connector arch)
    │                            │
    │                            ▼
    │                       Phase 4 (diagnostic arch)
    │                            │
    │                            ▼
    └────────────────────► Phase 5 (AI assistant)
                                 │
                                 ▼
                            Phase 6 (history)
                                 │
                                 ▼
                            Phase 7 (predictive)
                                 │
                                 ▼
                            Phase 8 (marketplace)
```

Phases 3 and 4 can proceed in parallel after Phase 2. Phase 5 requires Phase 4 (at least manual scans). Phase 6 requires Phase 5 for auto-logging AI events.

---

## Testing Strategy (per phase)

| Layer | Approach |
|-------|----------|
| Contracts | Unit tests with mock implementations |
| Adapters | Integration tests against existing services (mocked HTTP) |
| API | Feature tests in `tests/Feature/DiagnosticEcosystem/` |
| Frontend | Vitest for composables; no changes to existing test suites |
| Regression | Existing `DiagnosisApiTest`, `AIDiagnosisServiceTest` must pass unchanged |

---

## Rollback Plan

Each phase is isolated:

1. Set feature flag to `false`
2. Module routes return 503 — existing app unaffected
3. New tables can remain (no data dependency from old system)
4. Remove provider registration to fully disable module
