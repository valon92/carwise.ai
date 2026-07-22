# Diagnostic Ecosystem — Frontend Module

Additive Vue module for the AI Vehicle Diagnostic Ecosystem.

**Does not modify** existing views (`MyCars.vue`, `Diagnose.vue`, etc.).

## Planned structure (Phase 1+)

```
resources/js/diagnostic-ecosystem/
├── views/
│   ├── VehicleTwinList.vue
│   ├── VehicleTwinRegister.vue
│   ├── VehicleTwinDetail.vue
│   └── VehicleTwinHistory.vue
├── components/
│   ├── VinDecodeCard.vue
│   ├── DiagnosticScanPanel.vue
│   ├── AiAnalysisPanel.vue
│   └── HistoryTimeline.vue
├── composables/
│   ├── useVehicleTwin.js
│   ├── useVinIdentification.js
│   └── useVehicleHistory.js
├── services/
│   └── diagnosticEcosystemAPI.js   # Axios client for /api/de/*
└── router.js                        # Sub-routes under /vehicle-twin
```

## Mounting (additive route in app.js)

```javascript
{
  path: '/vehicle-twin',
  component: () => import('./diagnostic-ecosystem/layouts/EcosystemLayout.vue'),
  meta: { requiresAuth: true, title: 'Vehicle Twin' },
  children: [ /* lazy sub-routes */ ]
}
```

## Auth

Reuses existing Sanctum bearer token from `localStorage` via `api.js` interceptor — no auth changes.

See `docs/diagnostic-ecosystem/ARCHITECTURE.md` for full specification.
