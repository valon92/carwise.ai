<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Diagnostic Ecosystem Master Switch
    |--------------------------------------------------------------------------
    |
    | When false, all /api/de/* routes return 503. No impact on existing app.
    |
    */

    'enabled' => filter_var(env('DE_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Feature Flags (Steps 1–8)
    |--------------------------------------------------------------------------
    */

    'vin_identification' => filter_var(env('DE_VIN_IDENTIFICATION', false), FILTER_VALIDATE_BOOLEAN),

    'smart_connector' => filter_var(env('DE_CONNECTOR_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'diagnostic_read' => filter_var(env('DE_DIAGNOSTIC_READ_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'ai_assistant' => filter_var(env('DE_AI_ASSISTANT', false), FILTER_VALIDATE_BOOLEAN),

    'vehicle_history' => filter_var(env('DE_VEHICLE_HISTORY', false), FILTER_VALIDATE_BOOLEAN),

    'predictive_maintenance' => filter_var(env('DE_PREDICTIVE_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'marketplace_hooks' => filter_var(env('DE_MARKETPLACE_HOOKS', false), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | VIN Provider Priority
    |--------------------------------------------------------------------------
    |
    | Order in which VIN identification providers are attempted.
    | Adapters bridge to existing PublicAPIService, CarAPIService, etc.
    |
    */

    'vin_providers' => [
        'nhtsa',
        'carapi',
        'manufacturer',
    ],

    'vin_cache_ttl' => env('DE_VIN_CACHE_TTL', 86400),

];
