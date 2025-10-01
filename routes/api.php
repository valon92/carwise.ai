<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CarBrandController;
use App\Http\Controllers\Api\CarModelController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\DiagnosisController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CarMaintenanceController;
use App\Http\Controllers\Api\CarImageController;
use App\Http\Controllers\Api\AIImageController;
use App\Http\Controllers\Api\PerformanceController;
use App\Http\Controllers\Api\LogMonitoringController;
use App\Http\Controllers\Api\BackupController;
use App\Http\Controllers\Api\CarPartController;
use App\Http\Controllers\Api\AuthorizedCompanyController;
use App\Http\Controllers\Api\AffiliateController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\PublicAPIController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Car brands routes (public)
Route::get('/car-brands', [CarBrandController::class, 'index']);
Route::get('/car-brands/popular', [CarBrandController::class, 'popular']);
Route::get('/car-brands/{brandId}/models', [CarBrandController::class, 'models']);

// Car models routes (public)
Route::get('/car-models', [CarModelController::class, 'index']);
Route::get('/car-models/popular', [CarModelController::class, 'popular']);
Route::get('/car-models/search', [CarModelController::class, 'search']);
Route::get('/car-models/body-types', [CarModelController::class, 'bodyTypes']);
Route::get('/car-models/segments', [CarModelController::class, 'segments']);
Route::get('/car-models/fuel-types', [CarModelController::class, 'fuelTypes']);
Route::get('/car-models/brand/{brandId}', [CarModelController::class, 'byBrand']);
Route::get('/car-models/brand-slug/{brandSlug}', [CarModelController::class, 'byBrandSlug']);
Route::get('/car-models/body-type/{bodyType}', [CarModelController::class, 'byBodyType']);
Route::get('/car-models/segment/{segment}', [CarModelController::class, 'bySegment']);
Route::get('/car-models/fuel-type/{fuelType}', [CarModelController::class, 'byFuelType']);
Route::get('/car-models/{id}', [CarModelController::class, 'show']);
Route::get('/car-models/slug/{slug}', [CarModelController::class, 'showBySlug']);

// Car parts routes (public)
Route::get('/car-parts', [CarPartController::class, 'index']);
Route::get('/car-parts/featured', [CarPartController::class, 'getFeatured']);
Route::get('/car-parts/search', [CarPartController::class, 'search']);
Route::get('/car-parts/category/{category}', [CarPartController::class, 'getByCategory']);
Route::get('/car-parts/{id}', [CarPartController::class, 'show']);

// Affiliate routes
Route::post('/affiliate/track-click', [AffiliateController::class, 'trackClick']);
Route::post('/affiliate/track-purchase', [AffiliateController::class, 'trackPurchase']);
Route::get('/affiliate/stats', [AffiliateController::class, 'getStats']);

// Partner API routes
Route::get('/partners', [PartnerController::class, 'getPartners']);
Route::get('/partners/stats', [PartnerController::class, 'getPartnerStats']);
Route::post('/partners/search', [PartnerController::class, 'searchParts']);
Route::post('/partners/compare-prices', [PartnerController::class, 'comparePrices']);
Route::get('/partners/{partnerId}/parts/{partId}', [PartnerController::class, 'getPartDetails']);
Route::get('/partners/{partnerId}/parts/{partId}/availability', [PartnerController::class, 'getPartAvailability']);
Route::get('/partners/{partnerId}/parts/{partId}/pricing', [PartnerController::class, 'getPartPricing']);
Route::get('/partners/{partnerId}/parts/{partId}/affiliate-link', [PartnerController::class, 'generateAffiliateLink']);
Route::post('/partners/sync', [PartnerController::class, 'syncParts']);
Route::post('/partners/{partnerId}/sync', [PartnerController::class, 'syncPartnerParts']);

// Public API routes (NHTSA, eBay, Amazon)
Route::prefix('public')->group(function () {
    // NHTSA Vehicle API
    Route::get('/vehicle/vin/{vin}', [PublicAPIController::class, 'getVehicleByVIN']);
    Route::get('/vehicle/make/{make}/model/{model}/year/{year}', [PublicAPIController::class, 'getVehicleByMakeModelYear']);
    Route::get('/vehicle/makes', [PublicAPIController::class, 'getAllMakes']);
    
    // eBay Motors API
    Route::post('/ebay/search', [PublicAPIController::class, 'searchEbayParts']);
    Route::get('/ebay/item/{itemId}', [PublicAPIController::class, 'getEbayItemDetails']);
    
    // Amazon Product Advertising API
    Route::post('/amazon/search', [PublicAPIController::class, 'searchAmazonParts']);
    
    // Multi-API search
    Route::post('/search', [PublicAPIController::class, 'searchAllAPIs']);
    
    // API status and configuration
    Route::get('/status', [PublicAPIController::class, 'getAPIStatus']);
    Route::get('/categories', [PublicAPIController::class, 'getSupportedCategories']);
});

// Cart routes (public for now, will be protected later)
Route::post('/cart/add', function(Request $request) {
    // Simple cart implementation for affiliate tracking
    return response()->json([
        'success' => true,
        'message' => 'Part added to cart successfully',
        'data' => [
            'part_id' => $request->part_id,
            'quantity' => $request->quantity,
            'affiliate_source' => $request->affiliate_source
        ]
    ]);
});

// Authorized companies routes (public)
Route::get('/authorized-companies', [AuthorizedCompanyController::class, 'index']);
Route::get('/authorized-companies/featured', [AuthorizedCompanyController::class, 'getFeatured']);
Route::get('/authorized-companies/search', [AuthorizedCompanyController::class, 'search']);
Route::get('/authorized-companies/{id}', [AuthorizedCompanyController::class, 'show']);
Route::get('/authorized-companies/{id}/car-parts', [AuthorizedCompanyController::class, 'getCarParts']);


// Currency routes (public)
Route::get('/currencies', [CurrencyController::class, 'index']);
Route::get('/currencies/default', [CurrencyController::class, 'default']);
Route::get('/currencies/popular', [CurrencyController::class, 'popular']);
Route::get('/currencies/search', [CurrencyController::class, 'search']);
Route::get('/currencies/{code}', [CurrencyController::class, 'show']);
Route::post('/currencies/convert', [CurrencyController::class, 'convert']);

// Car image routes (public)
Route::get('/car-images/primary', [CarImageController::class, 'getPrimaryImage']);
Route::get('/car-images/car', [CarImageController::class, 'getCarImages']);
Route::get('/car-images/brand-fallback', [CarImageController::class, 'getBrandFallback']);
Route::get('/car-images/default', [CarImageController::class, 'getDefaultImage']);

// AI image generation routes (public)
Route::get('/ai-image/providers', [AIImageController::class, 'getAvailableProviders']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    
    // Car routes
    Route::apiResource('cars', CarController::class);
    Route::get('/cars/statistics', [CarController::class, 'statistics']);
    
            // Diagnosis routes
            Route::post('/diagnosis/start', [DiagnosisController::class, 'startDiagnosis']);
            Route::post('/diagnosis/submit', [DiagnosisController::class, 'submitDiagnosis']);
            Route::get('/diagnosis/result/{sessionId}', [DiagnosisController::class, 'getResult']);
            Route::get('/diagnosis/history', [DiagnosisController::class, 'getHistory']);
            Route::get('/diagnosis/car/{carId}/history', [DiagnosisController::class, 'getCarDiagnosisHistory']);
            Route::post('/transcribe-audio', [DiagnosisController::class, 'transcribeAudio']);
            
            // Car parts routes (protected)
            Route::get('/diagnosis/{diagnosisResultId}/suggested-parts', [CarPartController::class, 'getSuggestedParts']);

    // Dashboard routes
    Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);
    Route::get('/dashboard/notifications', [DashboardController::class, 'notifications']);


            // Car maintenance routes
            Route::prefix('cars/{carId}/maintenance')->group(function () {
                Route::get('/history', [CarMaintenanceController::class, 'getMaintenanceHistory']);
                Route::post('/records', [CarMaintenanceController::class, 'addMaintenanceRecord']);
                Route::get('/notifications', [CarMaintenanceController::class, 'getMaintenanceNotifications']);
                Route::put('/mileage', [CarMaintenanceController::class, 'updateMileage']);
                Route::get('/stats', [CarMaintenanceController::class, 'getMaintenanceStats']);
            });

            // AI image generation routes (protected)
            Route::prefix('ai-image')->group(function () {
                Route::post('/generate', [AIImageController::class, 'generateCarImage']);
                Route::post('/generate-if-needed', [AIImageController::class, 'generateImageIfNeeded']);
                Route::post('/generate-all', [AIImageController::class, 'generateImagesForAllCars']);
                Route::get('/status/{carId}', [AIImageController::class, 'getGenerationStatus']);
            });

    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/analytics', [AdminController::class, 'analytics']);
        Route::get('/system-health', [AdminController::class, 'systemHealth']);
        
        // Performance monitoring routes
        Route::prefix('performance')->group(function () {
            Route::get('/dashboard', [PerformanceController::class, 'dashboard']);
            Route::get('/metrics', [PerformanceController::class, 'metrics']);
            Route::get('/slow-queries', [PerformanceController::class, 'slowQueries']);
            Route::get('/endpoints', [PerformanceController::class, 'endpoints']);
            Route::post('/cache/clear', [PerformanceController::class, 'clearCache']);
            Route::post('/cache/warmup', [PerformanceController::class, 'warmupCache']);
        });

            // Log monitoring routes
            Route::prefix('logs')->group(function () {
                Route::get('/dashboard', [LogMonitoringController::class, 'dashboard']);
                Route::get('/recent', [LogMonitoringController::class, 'recentLogs']);
                Route::get('/trends', [LogMonitoringController::class, 'errorTrends']);
                Route::get('/patterns', [LogMonitoringController::class, 'criticalPatterns']);
                Route::get('/health', [LogMonitoringController::class, 'systemHealth']);
                Route::get('/statistics', [LogMonitoringController::class, 'statistics']);
                Route::get('/alerts', [LogMonitoringController::class, 'alerts']);
                Route::get('/search', [LogMonitoringController::class, 'searchLogs']);
                Route::post('/archive', [LogMonitoringController::class, 'archiveLogs']);
                Route::post('/export', [LogMonitoringController::class, 'exportLogs']);
                Route::post('/clear-cache', [LogMonitoringController::class, 'clearCache']);
            });

            // Database backup routes
            Route::prefix('backups')->group(function () {
                Route::get('/dashboard', [BackupController::class, 'dashboard']);
                Route::get('/', [BackupController::class, 'index']);
                Route::post('/', [BackupController::class, 'store']);
                Route::get('/statistics', [BackupController::class, 'statistics']);
                Route::get('/{filename}/download', [BackupController::class, 'download']);
                Route::get('/{filename}/verify', [BackupController::class, 'verify']);
                Route::delete('/{filename}', [BackupController::class, 'destroy']);
            });
        
        // User management
        Route::get('/users', [AdminController::class, 'users']);
        Route::put('/users/{id}/status', [AdminController::class, 'updateUserStatus']);
        
        // Content management
        Route::get('/cars', [AdminController::class, 'cars']);
        Route::get('/diagnoses', [AdminController::class, 'diagnoses']);
        Route::get('/car-brands', [AdminController::class, 'carBrands']);
        Route::put('/car-brands/{id}/status', [AdminController::class, 'updateCarBrandStatus']);
        Route::get('/car-models', [AdminController::class, 'carModels']);
        Route::put('/car-models/{id}/status', [AdminController::class, 'updateCarModelStatus']);
        Route::get('/currencies', [AdminController::class, 'currencies']);
        Route::put('/currencies/{id}/rate', [AdminController::class, 'updateCurrencyRate']);
        
        // Review moderation
        Route::get('/reviews/pending', [AdminController::class, 'pendingReviews']);
        Route::post('/reviews/{id}/moderate', [AdminController::class, 'moderateReview']);
        Route::get('/content/flagged', [AdminController::class, 'flaggedContent']);
        
        // System monitoring
        Route::get('/system-logs', [AdminController::class, 'systemLogs']);
    });
});
