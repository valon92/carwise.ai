# Diagnostic Ecosystem — Proposed Data Model

**Status:** Specification only — migrations will be created per phase when explicitly approved.  
**Prefix:** All new tables use `de_` to avoid collision with existing schema.

---

## Entity Relationship Overview

```
users (existing)
  │
  └── de_vehicle_profiles
        ├── de_vin_decodes
        ├── de_connector_pairings (future)
        ├── de_diagnostic_scans (future)
        │     └── de_ai_analyses
        ├── de_vehicle_history_events
        └── de_maintenance_recommendations
```

**Link to existing data (optional, nullable):**

- `de_vehicle_profiles.legacy_car_id` → `cars.id` (read-only bridge; no ALTER on `cars`)

---

## Tables

### `de_vehicle_profiles`

Primary vehicle registry for the ecosystem. VIN is the permanent identifier.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `user_id` | FK → users | Owner |
| `vin` | char(17) | Required, indexed |
| `license_plate` | varchar nullable | Phase 1 optional |
| `nickname` | varchar nullable | Phase 1 optional |
| `current_mileage` | int unsigned nullable | Phase 1 optional |
| `legacy_car_id` | FK → cars nullable | Soft bridge to existing My Cars |
| `manufacturer` | varchar nullable | Populated by VIN decode |
| `brand` | varchar nullable | |
| `model` | varchar nullable | |
| `year` | smallint nullable | |
| `engine` | varchar nullable | |
| `fuel_type` | varchar nullable | |
| `transmission` | varchar nullable | |
| `horsepower` | smallint nullable | |
| `factory_equipment` | json nullable | |
| `vehicle_options` | json nullable | |
| `last_vin_decode_id` | FK → de_vin_decodes nullable | Latest snapshot ref |
| `status` | enum active/archived | Default active |
| `created_at`, `updated_at` | timestamps | |

**Unique:** `(user_id, vin)`

---

### `de_vin_decodes`

Immutable snapshot each time a VIN is identified.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `vehicle_profile_id` | FK → de_vehicle_profiles | |
| `vin` | char(17) | |
| `provider` | varchar | nhtsa, carapi, manufacturer |
| `raw_response` | json | Full API payload |
| `manufacturer` | varchar nullable | |
| `brand` | varchar nullable | |
| `model` | varchar nullable | |
| `year` | smallint nullable | |
| `engine` | varchar nullable | |
| `fuel_type` | varchar nullable | |
| `transmission` | varchar nullable | |
| `horsepower` | smallint nullable | |
| `specifications` | json nullable | VIN specs |
| `factory_equipment` | json nullable | |
| `vehicle_options` | json nullable | |
| `service_schedule` | json nullable | |
| `recalls` | json nullable | |
| `warranty` | json nullable | If available |
| `decoded_at` | timestamp | |
| `created_at` | timestamp | |

---

### `de_connector_pairings` (Step 3 — future)

CarWise Smart Connector registration per vehicle.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `vehicle_profile_id` | FK | |
| `connector_type` | varchar | obd2_wifi, obd2_ble |
| `device_identifier` | varchar | Hashed device ID |
| `pairing_token` | varchar encrypted | |
| `capabilities` | json | Supported PIDs/protocols |
| `last_connected_at` | timestamp nullable | |
| `status` | enum paired/disconnected/revoked | |
| `created_at`, `updated_at` | timestamps | |

---

### `de_diagnostic_scans` (Step 4 — future)

Raw diagnostic read from connector or manual entry.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `vehicle_profile_id` | FK | |
| `connector_pairing_id` | FK nullable | Null if manual |
| `scan_date` | timestamp | |
| `mileage` | int unsigned nullable | |
| `source` | varchar | connector, manual, import |
| `engine_dtcs` | json nullable | |
| `abs_errors` | json nullable | |
| `airbag_errors` | json nullable | |
| `transmission_errors` | json nullable | |
| `battery_health` | json nullable | |
| `oil_life` | json nullable | |
| `tire_pressure` | json nullable | |
| `live_sensor_data` | json nullable | |
| `ecu_info` | json nullable | |
| `vehicle_status` | json nullable | |
| `raw_payload` | json | Normalized snapshot |
| `created_at` | timestamp | |

---

### `de_ai_analyses` (Step 5)

AI-generated interpretation of a diagnostic scan.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `diagnostic_scan_id` | FK → de_diagnostic_scans | |
| `vehicle_profile_id` | FK | |
| `problem_description` | text | |
| `severity` | enum low/medium/high/critical | |
| `possible_causes` | json | |
| `repair_procedure` | text nullable | |
| `estimated_repair_cost_min` | decimal nullable | |
| `estimated_repair_cost_max` | decimal nullable | |
| `estimated_repair_time_hours` | decimal nullable | |
| `recommended_parts` | json nullable | |
| `safety_recommendation` | text nullable | |
| `can_continue_driving` | boolean | |
| `confidence_score` | decimal nullable | Future |
| `ai_provider` | varchar | openai, claude, etc. |
| `ai_model` | varchar nullable | |
| `raw_ai_response` | json nullable | |
| `created_at` | timestamp | |

---

### `de_vehicle_history_events` (Step 6)

Append-only ledger — the Digital Vehicle Twin timeline.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `vehicle_profile_id` | FK | |
| `event_type` | varchar | scan, ai_analysis, repair, part_replaced, service_note, mileage_update |
| `event_date` | timestamp | |
| `mileage` | int unsigned nullable | |
| `title` | varchar | |
| `description` | text nullable | |
| `metadata` | json nullable | Codes, parts, costs, links |
| `diagnostic_scan_id` | FK nullable | |
| `ai_analysis_id` | FK nullable | |
| `created_by_user_id` | FK → users | |
| `created_at` | timestamp | |

**No `deleted_at`** — archive via `vehicle_profile.status` only.

---

### `de_maintenance_recommendations` (Step 7)

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `vehicle_profile_id` | FK | |
| `recommendation_type` | varchar | oil_service, brake_inspection, battery, timing_chain, dpf_cleaning, turbo_inspection, custom |
| `title` | varchar | |
| `description` | text | |
| `priority` | enum low/medium/high | |
| `due_at_mileage` | int unsigned nullable | |
| `due_at_date` | date nullable | |
| `reasoning` | text nullable | AI explanation |
| `status` | enum pending/scheduled/completed/dismissed | |
| `source` | varchar | ai_predictive, vin_schedule, manual |
| `created_at`, `updated_at` | timestamps | |

---

## Migration Naming Convention

Follow existing project pattern:

```
YYYY_MM_DD_HHMMSS_create_de_vehicle_profiles_table.php
YYYY_MM_DD_HHMMSS_create_de_vin_decodes_table.php
...
```

One migration per table. No `ALTER` migrations on existing tables.

---

## Index Strategy

| Table | Index |
|-------|-------|
| `de_vehicle_profiles` | `(user_id, vin)` unique, `vin`, `legacy_car_id` |
| `de_vin_decodes` | `vehicle_profile_id`, `vin` |
| `de_diagnostic_scans` | `vehicle_profile_id`, `scan_date` |
| `de_vehicle_history_events` | `vehicle_profile_id`, `event_date`, `event_type` |
| `de_maintenance_recommendations` | `vehicle_profile_id`, `status` |
