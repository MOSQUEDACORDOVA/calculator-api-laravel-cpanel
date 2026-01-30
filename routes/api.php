<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPriceController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Ruta pública de autenticación
Route::post('login', [AuthController::class, 'login'])->name('login');

// Rutas protegidas con autenticación
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    
    // Resources
    Route::get('currencies', [CurrencyController::class, 'index']);
    
    Route::apiResource('products', ProductController::class);
    
    Route::get('products/{product}/prices', [ProductPriceController::class, 'index']);
    Route::post('products/{product}/prices', [ProductPriceController::class, 'store']);
    Route::delete('products/{product}/prices/{price}', [ProductPriceController::class, 'destroy']);
});
