<?php

use App\DiagnosticEcosystem\Http\Controllers\Api\ConnectorPairingController;
use App\DiagnosticEcosystem\Http\Controllers\Api\DiagnosticAnalysisController;
use App\DiagnosticEcosystem\Http\Controllers\Api\DiagnosticScanController;
use App\DiagnosticEcosystem\Http\Controllers\Api\MarketplaceHookController;
use App\DiagnosticEcosystem\Http\Controllers\Api\PredictiveMaintenanceController;
use App\DiagnosticEcosystem\Http\Controllers\Api\VehicleHistoryController;
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
                    'version' => '0.3.0',
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

            Route::get('/vehicles/{id}/history', [VehicleHistoryController::class, 'index']);
            Route::post('/vehicles/{id}/history', [VehicleHistoryController::class, 'store']);
            Route::get('/vehicles/{id}/history/export.json', [VehicleHistoryController::class, 'exportJson']);
            Route::get('/vehicles/{id}/history/export.pdf', [VehicleHistoryController::class, 'exportPdf']);

            Route::get('/vehicles/{id}/maintenance', [PredictiveMaintenanceController::class, 'index']);
            Route::post('/vehicles/{id}/maintenance/generate', [PredictiveMaintenanceController::class, 'generate']);
            Route::patch('/maintenance/{recommendationId}', [PredictiveMaintenanceController::class, 'update']);

            Route::get('/vehicles/{id}/marketplace/parts', [MarketplaceHookController::class, 'searchParts']);
            Route::get('/analyses/{analysisId}/parts', [MarketplaceHookController::class, 'partsForAnalysis']);
            Route::get('/vehicles/{id}/marketplace/shops', [MarketplaceHookController::class, 'shops']);
            Route::get('/vehicles/{id}/marketplace/dealers', [MarketplaceHookController::class, 'dealers']);
            Route::get('/vehicles/{id}/marketplace/insurance', [MarketplaceHookController::class, 'insurance']);
            Route::get('/vehicles/{id}/marketplace/roadside', [MarketplaceHookController::class, 'roadside']);
            Route::get('/vehicles/{id}/marketplace/inspection', [MarketplaceHookController::class, 'inspection']);
        });
    });
