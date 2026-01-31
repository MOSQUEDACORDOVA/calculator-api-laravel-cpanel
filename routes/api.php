<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperationController;

/*
|--------------------------------------------------------------------------
| API Routes - Calculator API
|--------------------------------------------------------------------------
|
| Endpoints disponibles:
| POST   /api/calculate     - Realizar una operación
| GET    /api/history       - Listar historial de operaciones
| GET    /api/history/{id}  - Obtener una operación específica
| DELETE /api/history       - Eliminar todo el historial
|
*/

// Realizar operación matemática
Route::post('/calculate', [OperationController::class, 'calculate']);

// Historial de operaciones
Route::get('/history', [OperationController::class, 'history']);
Route::get('/history/{id}', [OperationController::class, 'show']);
Route::delete('/history', [OperationController::class, 'clearHistory']);

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'Calculator API',
        'timestamp' => now(),
    ]);
});
