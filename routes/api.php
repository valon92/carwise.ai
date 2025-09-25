<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CarBrandController;
use App\Http\Controllers\Api\CarModelController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\DiagnosisController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MechanicController;
use App\Http\Controllers\Api\AdminController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Car brands routes (public)
Route::get('/car-brands', [CarBrandController::class, 'index']);
Route::get('/car-brands/popular', [CarBrandController::class, 'popular']);
Route::get('/car-brands/countries', [CarBrandController::class, 'countries']);
Route::get('/car-brands/specialties', [CarBrandController::class, 'specialties']);
Route::get('/car-brands/search', [CarBrandController::class, 'search']);
Route::get('/car-brands/country/{country}', [CarBrandController::class, 'byCountry']);
Route::get('/car-brands/specialty/{specialty}', [CarBrandController::class, 'bySpecialty']);
Route::get('/car-brands/{id}', [CarBrandController::class, 'show']);

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

// Language routes (public)
Route::get('/languages', [LanguageController::class, 'index']);
Route::get('/languages/current', [LanguageController::class, 'current']);
Route::get('/languages/translations', [LanguageController::class, 'translations']);
Route::get('/languages/detect', [LanguageController::class, 'detect']);
Route::get('/languages/{code}', [LanguageController::class, 'info']);
Route::post('/languages/set', [LanguageController::class, 'setLanguage']);

// Currency routes (public)
Route::get('/currencies', [CurrencyController::class, 'index']);
Route::get('/currencies/default', [CurrencyController::class, 'default']);
Route::get('/currencies/popular', [CurrencyController::class, 'popular']);
Route::get('/currencies/search', [CurrencyController::class, 'search']);
Route::get('/currencies/{code}', [CurrencyController::class, 'show']);
Route::post('/currencies/convert', [CurrencyController::class, 'convert']);

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
Route::post('/transcribe-audio', [DiagnosisController::class, 'transcribeAudio']);

// Dashboard routes
Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);
Route::get('/dashboard/notifications', [DashboardController::class, 'notifications']);
    
    // Mechanic routes
            Route::get('/mechanics', [MechanicController::class, 'index']);
            Route::get('/mechanics/{id}', [MechanicController::class, 'show']);
            Route::post('/mechanics', [MechanicController::class, 'store']);
            Route::post('/mechanics/{id}', [MechanicController::class, 'update']);
            Route::put('/mechanics/{id}', [MechanicController::class, 'update']);
    
    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::put('/users/{id}/status', [AdminController::class, 'updateUserStatus']);
        Route::get('/cars', [AdminController::class, 'cars']);
        Route::get('/diagnoses', [AdminController::class, 'diagnoses']);
        Route::get('/car-brands', [AdminController::class, 'carBrands']);
        Route::put('/car-brands/{id}/status', [AdminController::class, 'updateCarBrandStatus']);
        Route::get('/car-models', [AdminController::class, 'carModels']);
        Route::put('/car-models/{id}/status', [AdminController::class, 'updateCarModelStatus']);
        Route::get('/currencies', [AdminController::class, 'currencies']);
        Route::put('/currencies/{id}/rate', [AdminController::class, 'updateCurrencyRate']);
        Route::get('/system-logs', [AdminController::class, 'systemLogs']);
    });
});
