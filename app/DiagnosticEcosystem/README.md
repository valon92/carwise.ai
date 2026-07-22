# Diagnostic Ecosystem Module

Additive feature module for CarWise.ai — **does not modify existing code**.

## Documentation

| Document | Path |
|----------|------|
| Architecture | `docs/diagnostic-ecosystem/ARCHITECTURE.md` |
| Data model (proposed) | `docs/diagnostic-ecosystem/DATA_MODEL.md` |
| API contract | `docs/diagnostic-ecosystem/API_CONTRACT.md` |
| Implementation roadmap | `docs/diagnostic-ecosystem/IMPLEMENTATION_ROADMAP.md` |

## Namespace

```
App\DiagnosticEcosystem\
├── Contracts/     # Interfaces for each step (1–8)
├── DTO/           # Value objects (no Eloquent yet)
├── Stubs/         # Null implementations for future hardware
├── Http/          # Controllers & middleware (Phase 1+)
├── Services/      # Business logic (Phase 1+)
├── Adapters/      # Bridges to existing app services (Phase 2+)
├── Models/        # Eloquent for de_* tables (Phase 1+)
└── Providers/
```

## Activation

1. Register provider in `bootstrap/providers.php` (one line, additive)
2. Set `DE_ENABLED=true` in `.env` when ready to test
3. Enable sub-features per phase (`DE_VIN_IDENTIFICATION`, etc.)

## Status endpoint

`GET /api/de/status` — returns module version and feature flags (503 when `DE_ENABLED=false`).
