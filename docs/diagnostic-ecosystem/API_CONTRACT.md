# Diagnostic Ecosystem — API Contract

**Base prefix:** `/api/de`  
**Auth:** `auth:sanctum` on all endpoints  
**Route file:** `routes/diagnostic-ecosystem.php` (loaded by module provider — **not** `routes/api.php`)

**Response envelope (consistent with existing API style):**

```json
{
  "success": true,
  "data": { },
  "message": "Optional human-readable message"
}
```

---

## Feature Gate Middleware

All routes pass through `de.enabled` middleware checking `config('diagnostic-ecosystem.enabled')`. Returns `503` with `{ "success": false, "message": "Module not enabled" }` when off.

Sub-feature middleware: `de.vin`, `de.connector`, `de.diagnostic`, `de.ai`, `de.history`, `de.predictive`, `de.marketplace`.

---

## Step 1 — Vehicle Registration

### `GET /api/de/vehicles`

List authenticated user's ecosystem vehicle profiles.

**Response `data`:** array of vehicle profile objects.

---

### `POST /api/de/vehicles`

Register a new vehicle.

**Body:**

```json
{
  "vin": "1HGBH41JXMN109186",
  "license_plate": "AB-123-CD",
  "nickname": "Family SUV",
  "current_mileage": 84200,
  "legacy_car_id": null
}
```

| Field | Required | Notes |
|-------|----------|-------|
| `vin` | yes | 17 chars, validated |
| `license_plate` | no | |
| `nickname` | no | |
| `current_mileage` | no | |
| `legacy_car_id` | no | Optional link to existing `cars` row owned by user |

**Side effect:** Triggers VIN identification (Step 2) if `DE_VIN_IDENTIFICATION=true`.

---

### `GET /api/de/vehicles/{id}`

Single vehicle profile with latest VIN decode summary.

---

### `PATCH /api/de/vehicles/{id}`

Update optional fields only (`license_plate`, `nickname`, `current_mileage`). **VIN is immutable.**

---

### `DELETE /api/de/vehicles/{id}`

Soft-archive (`status = archived`). History preserved.

---

## Step 2 — VIN Identification

### `POST /api/de/vehicles/{id}/identify`

Re-run VIN identification against configured providers.

**Body (optional):**

```json
{
  "force_refresh": false
}
```

**Response `data`:**

```json
{
  "vin": "1HGBH41JXMN109186",
  "provider": "nhtsa",
  "manufacturer": "Honda",
  "brand": "Honda",
  "model": "Civic",
  "year": 2021,
  "engine": "2.0L I4",
  "fuel_type": "Gasoline",
  "transmission": "CVT",
  "horsepower": 158,
  "specifications": {},
  "factory_equipment": [],
  "vehicle_options": [],
  "service_schedule": [],
  "recalls": [],
  "warranty": null,
  "decoded_at": "2026-07-21T10:00:00Z"
}
```

---

### `GET /api/de/vehicles/{id}/vin-history`

All `de_vin_decodes` snapshots for the vehicle.

---

### `POST /api/de/vin/preview` (pre-registration)

Decode a VIN before creating a profile (same response shape as identify).

---

## Step 3 — Smart Connector (architecture stubs)

### `GET /api/de/vehicles/{id}/connector`

Current connector status. Returns `not_configured` until hardware phase.

---

### `POST /api/de/vehicles/{id}/connector/pair`

**Future.** Pair CarWise Smart Connector. Returns `501 Not Implemented` with architecture message until Step 3 implementation.

---

### `DELETE /api/de/vehicles/{id}/connector`

Revoke pairing.

---

## Step 4 — Diagnostic Reading (architecture stubs)

### `POST /api/de/vehicles/{id}/scans`

**Future.** Trigger diagnostic read via connector. Returns `501` until Step 4.

---

### `POST /api/de/vehicles/{id}/scans/manual`

Submit manually entered DTC codes / symptoms (bridge to existing diagnosis flow without modifying it).

**Body:**

```json
{
  "mileage": 85000,
  "engine_dtcs": ["P0301", "P0420"],
  "notes": "Rough idle at cold start"
}
```

---

### `GET /api/de/vehicles/{id}/scans`

List diagnostic scans for vehicle.

---

### `GET /api/de/scans/{scanId}`

Single scan with linked AI analysis if available.

---

## Step 5 — AI Diagnostic Assistant

### `POST /api/de/scans/{scanId}/analyze`

Run AI analysis on a diagnostic scan.

**Response `data`:**

```json
{
  "problem_description": "Cylinder 1 misfire detected",
  "severity": "medium",
  "possible_causes": ["Faulty spark plug", "Ignition coil", "Fuel injector"],
  "repair_procedure": "1. Inspect spark plug on cylinder 1...",
  "estimated_repair_cost_min": 80,
  "estimated_repair_cost_max": 350,
  "estimated_repair_time_hours": 1.5,
  "recommended_parts": [
    { "name": "Ignition Coil", "part_number": "...", "estimated_price": 45 }
  ],
  "safety_recommendation": "Avoid extended highway driving until repaired.",
  "can_continue_driving": true,
  "confidence_score": null
}
```

---

## Step 6 — Vehicle History

### `GET /api/de/vehicles/{id}/history`

Timeline of all history events.

**Query params:** `event_type`, `from`, `to`, `page`, `per_page`

---

### `POST /api/de/vehicles/{id}/history`

Add manual event (repair, service note, part replaced).

**Body:**

```json
{
  "event_type": "repair",
  "event_date": "2026-07-15",
  "mileage": 84000,
  "title": "Brake pad replacement",
  "description": "Front pads replaced at local shop",
  "metadata": { "cost": 180, "shop": "AutoFix Prishtina" }
}
```

---

### `GET /api/de/vehicles/{id}/history/export`

Export full history as JSON or PDF (new export template in module only).

---

## Step 7 — Predictive Maintenance

### `GET /api/de/vehicles/{id}/recommendations`

Active maintenance recommendations.

---

### `POST /api/de/vehicles/{id}/recommendations/generate`

Trigger AI predictive analysis. Returns `501` until Step 7 enabled.

---

### `PATCH /api/de/recommendations/{id}`

Update status: `scheduled`, `completed`, `dismissed`.

---

## Step 8 — Marketplace Hooks (future)

### `GET /api/de/vehicles/{id}/parts/search?q=...`

Bridge to parts marketplace for vehicle-specific parts.

### `GET /api/de/vehicles/{id}/shops/nearby`

Future — returns `501`.

### `GET /api/de/vehicles/{id}/dealers`

Future — returns `501`.

---

## Error Codes

| HTTP | When |
|------|------|
| 401 | Missing/invalid Sanctum token |
| 403 | Vehicle belongs to another user |
| 404 | Resource not found |
| 422 | Validation error (invalid VIN, etc.) |
| 501 | Feature flag off or not yet implemented |
| 503 | `DE_ENABLED=false` |

---

## Rate Limits

| Group | Limit |
|-------|-------|
| VIN decode | 30/min per user |
| AI analysis | 10/min per user |
| General DE API | 120/min per user |

Implemented via Laravel `throttle` middleware on route groups.
