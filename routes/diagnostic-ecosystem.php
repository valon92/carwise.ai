<?php

use App\DiagnosticEcosystem\Http\Controllers\Api\ConnectorPairingController;
use App\DiagnosticEcosystem\Http\Controllers\Api\DiagnosticAnalysisController;
use App\DiagnosticEcosystem\Http\Controllers\Api\DiagnosticScanController;
use App\DiagnosticEcosystem\Http\Controllers\Api\VehicleProfileController;
use App\DiagnosticEcosystem\Http\Controllers\Api\VinIdentificationController;
use App\DiagnosticEcosystem\Http\Middleware\EnsureDiagnosticEcosystemEnabled;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Diagnostic Ecosystem API Routes
|--------------------------------------------------------------------------
|
| Additive module — does not modify routes/api.php.
| Full contract: docs/diagnostic-ecosystem/API_CONTRACT.md
|
*/

Route::prefix('api/de')
    ->middleware(['api', EnsureDiagnosticEcosystemEnabled::class])
    ->group(function () {
        Route::get('/status', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'module' => 'diagnostic-ecosystem',
                    'version' => '0.1.0',
                    'features' => [
                        'vehicle_registration' => config('diagnostic-ecosystem.enabled'),
                        'vin_identification' => config('diagnostic-ecosystem.vin_identification'),
                        'smart_connector' => config('diagnostic-ecosystem.smart_connector'),
                        'diagnostic_read' => config('diagnostic-ecosystem.diagnostic_read'),
                        'ai_assistant' => config('diagnostic-ecosystem.ai_assistant'),
                        'vehicle_history' => config('diagnostic-ecosystem.vehicle_history'),
                        'predictive_maintenance' => config('diagnostic-ecosystem.predictive_maintenance'),
                        'marketplace_hooks' => config('diagnostic-ecosystem.marketplace_hooks'),
                    ],
                ],
            ]);
        })->name('de.status');

        Route::post('/vin/preview', [VinIdentificationController::class, 'preview']);

        // Step 1 — Vehicle Registration (auth required)
        Route::middleware('auth:sanctum')->group(function () {
            Route::apiResource('vehicles', VehicleProfileController::class);
            Route::post('/vehicles/{id}/identify', [VinIdentificationController::class, 'identify']);
            Route::get('/vehicles/{id}/vin-history', [VinIdentificationController::class, 'history']);
            Route::get('/vehicles/{id}/connector', [ConnectorPairingController::class, 'show']);
            Route::post('/vehicles/{id}/connector/pair', [ConnectorPairingController::class, 'pair']);
            Route::delete('/vehicles/{id}/connector', [ConnectorPairingController::class, 'destroy']);
            Route::get('/vehicles/{id}/scans', [DiagnosticScanController::class, 'index']);
            Route::post('/vehicles/{id}/scans', [DiagnosticScanController::class, 'store']);
            Route::post('/vehicles/{id}/scans/manual', [DiagnosticScanController::class, 'manual']);
            Route::get('/scans/{scanId}', [DiagnosticScanController::class, 'show']);
            Route::post('/scans/{scanId}/analyze', [DiagnosticAnalysisController::class, 'analyze']);
            Route::get('/scans/{scanId}/analysis', [DiagnosticAnalysisController::class, 'show']);
            Route::get('/vehicles/{id}/analyses', [DiagnosticAnalysisController::class, 'index']);
        });
    });
