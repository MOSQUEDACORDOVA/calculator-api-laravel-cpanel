<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperationController;

/*
|--------------------------------------------------------------------------
| API Routes - Calculator API
|--------------------------------------------------------------------------
|
| ⚠️  AVISO DE SEGURIDAD
| Este proyecto es una DEMOSTRACIÓN de cómo implementar una API Laravel
| en infraestructura legacy como cPanel. NO incluye autenticación ni
| medidas de seguridad intencionalmente para enfocarse en el despliegue.
|
| Para producción, implementar:
| - Autenticación (Sanctum, Passport)
| - Rate limiting
| - CORS apropiado
| - HTTPS
|
|--------------------------------------------------------------------------
| Endpoints disponibles:
|--------------------------------------------------------------------------
| POST   /api/calculate      - Realizar una operación matemática
| GET    /api/history        - Listar historial de operaciones
| GET    /api/history/{id}   - Obtener una operación específica
| DELETE /api/history/{id}   - Eliminar una operación específica
| DELETE /api/history        - Eliminar todo el historial
| GET    /api/health         - Health check del servicio
|
*/

// Realizar operación matemática
Route::post('/calculate', [OperationController::class, 'calculate']);

// Historial de operaciones
Route::get('/history', [OperationController::class, 'history']);
Route::get('/history/{id}', [OperationController::class, 'show']);
Route::delete('/history/{id}', [OperationController::class, 'destroy']);
Route::delete('/history', [OperationController::class, 'clearHistory']);

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'Calculator API',
        'timestamp' => now(),
    ]);
});
