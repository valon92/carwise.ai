<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\DiagnosisController;
use App\Http\Controllers\Api\MechanicController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Car routes
    Route::apiResource('cars', CarController::class);
    
    // Diagnosis routes
    Route::apiResource('diagnoses', DiagnosisController::class)->except(['update', 'destroy']);
    
    // Mechanic routes
    Route::get('/mechanics', [MechanicController::class, 'index']);
    Route::get('/mechanics/{id}', [MechanicController::class, 'show']);
    Route::put('/mechanics/{id}', [MechanicController::class, 'update']);
});
