# CarWise.ai — AI Vehicle Diagnostic Ecosystem

**Status:** Specification & architecture preparation (additive module)  
**Constraint:** Do not modify, remove, or refactor any existing CarWise.ai functionality.

---

## Purpose

Transform CarWise.ai incrementally into an **AI-powered Digital Vehicle Twin** platform. Each registered vehicle maintains a permanent VIN-based profile, diagnostic history, AI repair guidance, and (future) predictive maintenance — without altering the current diagnosis flow, auth system, UI, or database tables.

---

## Design Principles

| Principle | Rule |
|-----------|------|
| **Additive only** | New code lives under dedicated namespaces; zero edits to existing controllers, models, routes, or Vue views |
| **No auth coupling** | Reuse Sanctum bearer tokens via middleware; do not replace or wrap `AuthController` |
| **No schema coupling** | New tables use `de_*` prefix; optional soft link to `cars.id` via nullable FK — never alter `cars` columns |
| **Bridge, don't fork** | Read from existing services (`PublicAPIService`, `CarAPIService`, `AIDiagnosisService`) through adapter classes in the new module |
| **Feature-flagged** | `DE_ENABLED=false` by default until each phase is ready |
| **Incremental** | Each step (1–8) ships independently behind flags |

---

## Module Layout

```
app/DiagnosticEcosystem/
├── Contracts/              # Interfaces (architecture stubs)
├── DTO/                    # Value objects — no Eloquent
├── Adapters/               # Bridge to existing app services (Phase 1+)
├── Services/               # Module business logic (Phase 1+)
├── Http/Controllers/Api/   # Module API controllers (Phase 1+)
├── Models/                 # Eloquent models for de_* tables (Phase 1+)
├── Providers/
│   └── DiagnosticEcosystemServiceProvider.php
└── README.md

routes/
└── diagnostic-ecosystem.php    # NEW file — loaded by provider only

config/
└── diagnostic-ecosystem.php      # Feature flags & provider config

resources/js/diagnostic-ecosystem/
├── views/                  # New Vue pages (lazy-loaded)
├── components/
├── composables/
├── services/               # Axios client for /api/de/*
└── router.js               # Sub-routes mounted under /vehicle-twin/*

docs/diagnostic-ecosystem/
├── ARCHITECTURE.md         # This file
├── DATA_MODEL.md
├── API_CONTRACT.md
└── IMPLEMENTATION_ROADMAP.md
```

### Bootstrap registration (Phase 0 — one line, additive)

```php
// bootstrap/providers.php — append only
App\DiagnosticEcosystem\Providers\DiagnosticEcosystemServiceProvider::class,
```

The provider loads `routes/diagnostic-ecosystem.php` and `config/diagnostic-ecosystem.php`. **No changes to `routes/api.php` or `routes/web.php`.**

### Frontend mounting (Phase 1+ — additive route in `app.js`)

```javascript
// Lazy-loaded; does not replace existing routes
{
  path: '/vehicle-twin',
  component: () => import('./diagnostic-ecosystem/layouts/EcosystemLayout.vue'),
  meta: { requiresAuth: true },
  children: [ /* sub-routes */ ]
}
```

Existing `/my-cars`, `/diagnose`, `/diagnosis-history` remain untouched.

---

## Relationship to Existing Code

```
┌─────────────────────────────────────────────────────────────┐
│                    EXISTING (unchanged)                      │
│  Auth (Sanctum) │ Car model │ DiagnosisSession │ MyCars UI  │
└──────────────────────────┬──────────────────────────────────┘
                           │ read-only bridges (Adapters)
┌──────────────────────────▼──────────────────────────────────┐
│              DiagnosticEcosystem (NEW module)                │
│                                                              │
│  Step 1  Vehicle Registration (VIN-first)                    │
│  Step 2  VIN Identification (multi-provider)                 │
│  Step 3  Smart Connector architecture (contracts only)       │
│  Step 4  Diagnostic reading architecture (contracts only)   │
│  Step 5  AI Diagnostic Assistant                             │
│  Step 6  Vehicle History ledger                              │
│  Step 7  Predictive Maintenance                              │
│  Step 8  Marketplace integration hooks                       │
└─────────────────────────────────────────────────────────────┘
```

### Existing integrations reused via Adapters

| Existing service | Reuse for |
|------------------|-----------|
| `PublicAPIService` (NHTSA VPIC) | VIN decode — primary free provider |
| `CarAPIService` | Specs, recalls, maintenance schedules |
| `CarManufacturerAPIService` | OEM data when licensed |
| `AIDiagnosisService` + `AIProviderManager` | AI analysis enrichment (Step 5) |
| `DiagnosisEnhancementService` | Parts recommendations |
| `LicensedPartsAPIService` | Future marketplace bridge (Step 8) |

Adapters live in `app/DiagnosticEcosystem/Adapters/` and **never modify** the source services.

---

## Step-by-Step Architecture

### Step 1 — Vehicle Registration

**Goal:** After login, user registers one or more vehicles with VIN as the primary key.

- New table: `de_vehicle_profiles` (see DATA_MODEL.md)
- Required field: `vin` (17 chars, validated, unique per user)
- Optional (future): `license_plate`, `nickname`, `current_mileage`
- Optional nullable FK: `legacy_car_id` → `cars.id` for users who already have cars in the old system — **no migration on `cars` table**
- API prefix: `/api/de/vehicles`

### Step 2 — Automatic VIN Identification

**Goal:** On VIN entry, resolve manufacturer, model, year, engine, fuel, transmission, HP, specs, options, service schedule, recalls, warranty.

- Contract: `VinIdentificationProviderInterface`
- Orchestrator: `VinIdentificationService` tries providers in priority order:
  1. NHTSA (via `NhtsaVinAdapter` → existing `PublicAPIService`)
  2. CarAPI.app (via `CarApiVinAdapter` → existing `CarAPIService`)
  3. Manufacturer API (via `ManufacturerVinAdapter` → existing `CarManufacturerAPIService`)
- Results stored in `de_vin_decodes` (immutable snapshot per decode)
- VIN is the **permanent identifier** on `de_vehicle_profiles`

### Step 3 — CarWise Smart Connector (architecture only)

**Goal:** Prepare for future OBD-II Wi-Fi/Bluetooth adapter — **no hardware protocol**.

- Contract: `SmartConnectorInterface`
  - `discover()`, `connect()`, `disconnect()`, `getStatus()`, `getCapabilities()`
- DTO: `ConnectorStatus`, `ConnectorCapabilities`
- Future implementation: `Obd2WifiConnector`, `Obd2BleConnector`
- Config: `DE_CONNECTOR_ENABLED=false`

### Step 4 — Vehicle Diagnostic Reading (architecture only)

**Goal:** Prepare for DTC codes, ABS, airbag, transmission, battery, oil life, TPMS, live sensors, ECU info — **no protocol yet**.

- Contract: `DiagnosticReadCapabilityInterface`
  - `readDtcs()`, `readLiveData()`, `readEcuInfo()`, `readVehicleStatus()`
- DTO: `DiagnosticSnapshot` (normalized payload regardless of connector)
- Storage target (future): `de_diagnostic_scans`
- Config: `DE_DIAGNOSTIC_READ_ENABLED=false`

### Step 5 — AI Diagnostic Assistant

**Goal:** Translate raw codes into human-readable guidance.

- Contract: `DiagnosticAnalysisProviderInterface`
- Orchestrator: `DiagnosticAnalysisService`
  - Input: `DiagnosticSnapshot` + vehicle profile + VIN specs
  - Output: `DiagnosticAnalysis` DTO with:
    - problem description, severity, possible causes
    - repair procedure, estimated cost/time
    - recommended parts, safety recommendation
    - `can_continue_driving` boolean
- AI bridge: `AiDiagnosisAdapter` wraps existing `AIDiagnosisService` with a **new prompt template** in the module — does not change `AIDiagnosisService`
- Future: confidence scoring, predictive diagnostics flags on `de_ai_analyses`

### Step 6 — Vehicle History

**Goal:** Permanent digital maintenance record per vehicle.

- Tables: `de_vehicle_history_events` (append-only ledger)
- Event types: `scan`, `ai_analysis`, `repair`, `part_replaced`, `service_note`, `mileage_update`
- Each diagnostic session creates linked events; never delete — soft-archive only
- API: `/api/de/vehicles/{id}/history`

### Step 7 — Predictive Maintenance

**Goal:** AI-driven preventive recommendations that improve over time.

- Contract: `PredictiveMaintenanceInterface`
- Engine: `PredictiveMaintenanceService` reads history + VIN service schedule + mileage
- Output: `de_maintenance_recommendations` with priority, due date/mileage, reasoning
- Config: `DE_PREDICTIVE_ENABLED=false`

### Step 8 — Future Marketplace (hooks only)

**Goal:** Architecture compatible with parts, shops, dealers, insurance, roadside, inspection.

- Contract: `MarketplaceIntegrationInterface`
  - `searchParts()`, `findShops()`, `findDealers()`, `getInsuranceQuotes()`, `requestRoadside()`, `bookInspection()`
- Stub implementations return `not_implemented` until Phase 8+
- Existing `PartsMarketplaceAPIService` and `LicensedPartsAPIService` bridged via `PartsMarketplaceAdapter`

---

## Long-Term Vision: Digital Vehicle Twin

```
VIN ──► Vehicle Profile ──► VIN Decode Cache
                │
                ├── Smart Connector (future) ──► Diagnostic Scans
                │                                      │
                │                                      ▼
                │                              AI Analysis
                │                                      │
                ▼                                      ▼
         History Ledger ◄────────────────── Maintenance Events
                │
                ▼
    Predictive Recommendations ──► Marketplace Integrations
```

Each vehicle becomes an intelligent, persistent profile independent of any single diagnostic session.

---

## Security & Privacy

- All `/api/de/*` routes: `auth:sanctum` + `throttle`
- VIN stored encrypted at rest (optional Phase 2 enhancement)
- Connector pairing tokens scoped per user + vehicle
- Outbound marketplace links follow existing secure redirect pattern (`InventoryOutboundController` pattern)
- No PII in diagnostic scan payloads sent to AI — vehicle ID + codes only

---

## Feature Flags (`config/diagnostic-ecosystem.php`)

```php
'enabled' => env('DE_ENABLED', false),
'vin_identification' => env('DE_VIN_IDENTIFICATION', false),
'smart_connector' => env('DE_CONNECTOR_ENABLED', false),
'diagnostic_read' => env('DE_DIAGNOSTIC_READ_ENABLED', false),
'ai_assistant' => env('DE_AI_ASSISTANT', false),
'vehicle_history' => env('DE_VEHICLE_HISTORY', false),
'predictive_maintenance' => env('DE_PREDICTIVE_ENABLED', false),
'marketplace_hooks' => env('DE_MARKETPLACE_HOOKS', false),
```

Each phase enables its flag independently. Master `DE_ENABLED` gates the entire module.

---

## What This Document Does NOT Do

- Does not modify `cars`, `diagnosis_sessions`, or any existing table
- Does not change `routes/api.php`, existing Vue views, or `AIDiagnosisService`
- Does not implement OBD-II, BLE, or Wi-Fi protocols
- Does not replace Sanctum authentication

See `IMPLEMENTATION_ROADMAP.md` for phased delivery and `API_CONTRACT.md` for endpoint definitions.
