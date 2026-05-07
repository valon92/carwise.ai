<?php

use App\Http\Controllers\InventoryOutboundController;
use Illuminate\Support\Facades\Route;

Route::get('/inventory/out/{token}', [InventoryOutboundController::class, 'redirect'])
    ->middleware('throttle:45,1')
    ->name('inventory.outbound');

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
