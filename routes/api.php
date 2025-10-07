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
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\PriceController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\LicensedPartsController;
use App\Http\Controllers\Api\DiagnosisExportController;
use App\Http\Controllers\Api\CarAPIController;
use App\Http\Controllers\Api\CompareController;

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

// Stock routes (public for demo, should be protected in production)
Route::prefix('stock')->group(function () {
    Route::get('/{partId}', [StockController::class, 'getStock']);
    Route::get('/statistics/overview', [StockController::class, 'getStatistics']);
    Route::post('/simulate', [StockController::class, 'simulateChanges']);
});

// Price routes (public for demo, should be protected in production)
Route::prefix('price')->group(function () {
    Route::get('/{partId}', [PriceController::class, 'getPrice']);
    Route::get('/{partId}/history', [PriceController::class, 'getPriceHistory']);
    Route::get('/statistics/overview', [PriceController::class, 'getStatistics']);
    Route::get('/promotions/active', [PriceController::class, 'getActivePromotions']);
    Route::post('/simulate', [PriceController::class, 'simulateChanges']);
    Route::post('/seasonal', [PriceController::class, 'applySeasonalPricing']);
    Route::post('/market-fluctuations', [PriceController::class, 'applyMarketFluctuations']);
});

// Chat routes (public for demo, should be protected in production)
Route::prefix('chat')->group(function () {
    Route::post('/conversations', [ChatController::class, 'startConversation']);
    Route::post('/upload', [ChatController::class, 'uploadFile']);
});

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
    Route::post('/user/profile', [AuthController::class, 'updateProfile']);
    
    // User preferences routes
    Route::prefix('preferences')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\UserPreferenceController::class, 'index']);
        Route::get('/{key}', [App\Http\Controllers\Api\UserPreferenceController::class, 'show']);
        Route::post('/', [App\Http\Controllers\Api\UserPreferenceController::class, 'store']);
        Route::put('/{key}', [App\Http\Controllers\Api\UserPreferenceController::class, 'update']);
        Route::delete('/{key}', [App\Http\Controllers\Api\UserPreferenceController::class, 'destroy']);
        
        // Cart-specific preferences
        Route::get('/cart/all', [App\Http\Controllers\Api\UserPreferenceController::class, 'getCartPreferences']);
        Route::post('/cart/set', [App\Http\Controllers\Api\UserPreferenceController::class, 'setCartPreferences']);
    });
    
    // Car routes
    Route::get('/cars/statistics', [CarController::class, 'statistics']);
    Route::apiResource('cars', CarController::class);
    
    // Order routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\OrderController::class, 'index']);
        Route::get('/statistics', [App\Http\Controllers\Api\OrderController::class, 'statistics']);
        Route::get('/{id}', [App\Http\Controllers\Api\OrderController::class, 'show']);
        Route::post('/', [App\Http\Controllers\Api\OrderController::class, 'store']);
        Route::put('/{id}/cancel', [App\Http\Controllers\Api\OrderController::class, 'cancel']);
    });
    
    // Wishlist routes
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\WishlistController::class, 'index']);
        Route::get('/statistics', [App\Http\Controllers\Api\WishlistController::class, 'statistics']);
        Route::get('/check', [App\Http\Controllers\Api\WishlistController::class, 'check']);
        Route::post('/', [App\Http\Controllers\Api\WishlistController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\Api\WishlistController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\Api\WishlistController::class, 'destroy']);
        Route::post('/{id}/move-to-cart', [App\Http\Controllers\Api\WishlistController::class, 'moveToCart']);
    });
    
    // Compare routes
    Route::prefix('compare')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\CompareController::class, 'index']);
        Route::get('/statistics', [App\Http\Controllers\Api\CompareController::class, 'statistics']);
        Route::get('/check', [App\Http\Controllers\Api\CompareController::class, 'check']);
        Route::post('/', [App\Http\Controllers\Api\CompareController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\Api\CompareController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\Api\CompareController::class, 'destroy']);
        Route::delete('/clear', [App\Http\Controllers\Api\CompareController::class, 'clear']);
        Route::post('/reorder', [App\Http\Controllers\Api\CompareController::class, 'reorder']);
    });
    
    // Search suggestions routes
    Route::prefix('search-suggestions')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\SearchSuggestionController::class, 'index']);
        Route::get('/popular', [App\Http\Controllers\Api\SearchSuggestionController::class, 'popular']);
        Route::get('/recent', [App\Http\Controllers\Api\SearchSuggestionController::class, 'recent']);
    });
    
    // Search history routes
    Route::prefix('search-history')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\SearchHistoryController::class, 'index']);
        Route::get('/statistics', [App\Http\Controllers\Api\SearchHistoryController::class, 'statistics']);
        Route::get('/popular', [App\Http\Controllers\Api\SearchHistoryController::class, 'popular']);
        Route::get('/trends', [App\Http\Controllers\Api\SearchHistoryController::class, 'trends']);
        Route::get('/recent', [App\Http\Controllers\Api\SearchHistoryController::class, 'recent']);
        Route::get('/analytics', [App\Http\Controllers\Api\SearchHistoryController::class, 'analytics']);
        Route::post('/', [App\Http\Controllers\Api\SearchHistoryController::class, 'store']);
        Route::delete('/', [App\Http\Controllers\Api\SearchHistoryController::class, 'clear']);
        Route::delete('/{id}', [App\Http\Controllers\Api\SearchHistoryController::class, 'destroy']);
    });
    
    // Saved searches routes
    Route::prefix('saved-searches')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\SavedSearchController::class, 'index']);
        Route::get('/statistics', [App\Http\Controllers\Api\SavedSearchController::class, 'statistics']);
        Route::get('/public', [App\Http\Controllers\Api\SavedSearchController::class, 'public']);
        Route::get('/trending', [App\Http\Controllers\Api\SavedSearchController::class, 'trending']);
        Route::get('/recommended', [App\Http\Controllers\Api\SavedSearchController::class, 'recommended']);
        Route::get('/search', [App\Http\Controllers\Api\SavedSearchController::class, 'search']);
        Route::post('/', [App\Http\Controllers\Api\SavedSearchController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\Api\SavedSearchController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\Api\SavedSearchController::class, 'destroy']);
        Route::post('/{id}/execute', [App\Http\Controllers\Api\SavedSearchController::class, 'execute']);
        Route::post('/{id}/toggle-favorite', [App\Http\Controllers\Api\SavedSearchController::class, 'toggleFavorite']);
        Route::post('/{id}/toggle-notification', [App\Http\Controllers\Api\SavedSearchController::class, 'toggleNotification']);
        Route::post('/{id}/duplicate', [App\Http\Controllers\Api\SavedSearchController::class, 'duplicate']);
    });
    
            // Diagnosis routes
            Route::post('/diagnosis/start', [DiagnosisController::class, 'startDiagnosis']);
            Route::post('/diagnosis/submit', [DiagnosisController::class, 'submitDiagnosis']);
            Route::get('/diagnosis/result/{sessionId}', [DiagnosisController::class, 'getResult']);
            Route::get('/diagnosis/history', [DiagnosisController::class, 'getHistory']);
            Route::get('/diagnosis/car/{carId}/history', [DiagnosisController::class, 'getCarDiagnosisHistory']);
            Route::post('/transcribe-audio', [DiagnosisController::class, 'transcribeAudio']);
            
            // Car parts routes (protected)
            Route::get('/diagnosis/{diagnosisResultId}/suggested-parts', [CarPartController::class, 'getSuggestedParts']);
            
    // Stock management routes (protected)
    Route::prefix('stock')->group(function () {
        Route::put('/{partId}', [StockController::class, 'updateStock']);
        Route::post('/bulk-update', [StockController::class, 'bulkUpdateStock']);
        Route::post('/{partId}/reserve', [StockController::class, 'reserveStock']);
        Route::post('/{partId}/release', [StockController::class, 'releaseStock']);
        Route::put('/thresholds', [StockController::class, 'updateThresholds']);
    });

    // Price management routes (protected)
    Route::prefix('price')->group(function () {
        Route::put('/{partId}', [PriceController::class, 'updatePrice']);
        Route::post('/bulk-update', [PriceController::class, 'bulkUpdatePrices']);
        Route::post('/promotions', [PriceController::class, 'setPromotionalPricing']);
        Route::post('/promotions/restore', [PriceController::class, 'restorePromotionalPricing']);
    });

    // Notification routes (protected)
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/statistics', [NotificationController::class, 'getStatistics']);
        Route::get('/templates', [NotificationController::class, 'getTemplates']);
        Route::post('/test', [NotificationController::class, 'sendTestNotification']);
        Route::post('/preferences', [NotificationController::class, 'updatePreferences']);
        Route::post('/{notificationId}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notificationId}', [NotificationController::class, 'delete']);
        Route::delete('/clear-all', [NotificationController::class, 'clearAll']);
    });

    // Chat routes (protected)
    Route::prefix('chat')->group(function () {
        Route::get('/conversations', [ChatController::class, 'getConversations']);
        Route::get('/conversations/{conversationId}/messages', [ChatController::class, 'getMessages']);
        Route::post('/conversations/{conversationId}/messages', [ChatController::class, 'sendMessage']);
        Route::post('/conversations/{conversationId}/mark-read', [ChatController::class, 'markAsRead']);
        Route::post('/conversations/{conversationId}/end', [ChatController::class, 'endConversation']);
        Route::post('/conversations/{conversationId}/typing', [ChatController::class, 'sendTypingIndicator']);
        Route::get('/statistics', [ChatController::class, 'getStatistics']);
    });

    // Dashboard routes
    Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);
    Route::get('/dashboard/notifications', [DashboardController::class, 'notifications']);


            // Car maintenance routes
            Route::prefix('cars/{carId}/maintenance')->group(function () {
                Route::get('/history', [CarMaintenanceController::class, 'getMaintenanceHistory']);
                Route::post('/', [CarMaintenanceController::class, 'addMaintenanceRecord']);
                Route::get('/notifications', [CarMaintenanceController::class, 'getMaintenanceNotifications']);
                Route::put('/mileage', [CarMaintenanceController::class, 'updateMileage']);
                Route::get('/stats', [CarMaintenanceController::class, 'getMaintenanceStats']);
                Route::get('/report', [CarMaintenanceController::class, 'generateMaintenanceReport']);
            });
            
            // Car diagnosis history routes
            Route::prefix('cars/{carId}')->group(function () {
                Route::get('/diagnosis-history', [DiagnosisController::class, 'getCarDiagnosisHistory']);
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

// Licensed Parts API routes (public)
Route::prefix('licensed-parts')->group(function () {
    Route::get('/search', [LicensedPartsController::class, 'search']);
    Route::get('/ai-suggestions', [LicensedPartsController::class, 'getAISuggestions']);
    Route::get('/popular', [LicensedPartsController::class, 'getPopularParts']);
    Route::get('/by-vehicle', [LicensedPartsController::class, 'getPartsByVehicle']);
    Route::get('/by-category/{category}', [LicensedPartsController::class, 'getPartsByCategory']);
    Route::get('/providers/stats', [LicensedPartsController::class, 'getProviderStats']);
    Route::get('/providers/{providerId}/parts/{partNumber}', [LicensedPartsController::class, 'getPartDetails']);
});

// CarAPI.app routes (public)
Route::prefix('carapi')->group(function () {
    Route::get('/status', [CarAPIController::class, 'getStatus']);
    Route::get('/makes', [CarAPIController::class, 'getAllMakes']);
    Route::get('/models', [CarAPIController::class, 'getModelsByMake']);
    Route::get('/years', [CarAPIController::class, 'getYearsByMakeModel']);
    Route::get('/vehicle-info', [CarAPIController::class, 'getVehicleInfo']);
    Route::get('/vehicle-specs', [CarAPIController::class, 'getVehicleSpecs']);
    Route::get('/vehicle-recalls', [CarAPIController::class, 'getVehicleRecalls']);
    Route::get('/maintenance-schedule', [CarAPIController::class, 'getMaintenanceSchedule']);
    Route::get('/compatible-parts', [CarAPIController::class, 'getCompatibleParts']);
    Route::get('/search-vehicles', [CarAPIController::class, 'searchVehiclesByMake']);
    Route::get('/real-parts', [CarAPIController::class, 'getRealCarParts']);
});

// Diagnosis Export routes (authenticated)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/diagnosis/export-pdf', [DiagnosisExportController::class, 'exportToPDF']);
    Route::post('/diagnosis/export-json', [DiagnosisExportController::class, 'exportToJSON']);
});
