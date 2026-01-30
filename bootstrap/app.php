<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Auth\AuthenticationException;
use App\Http\Middleware\Authenticate;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                // Verificar si es un modelo no encontrado
                $previous = $e->getPrevious();
                if ($previous instanceof ModelNotFoundException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Recurso no encontrado',
                        'error' => 'El recurso solicitado no existe en la base de datos'
                    ], 404);
                }
                
                // Si no es un modelo, es una ruta no encontrada
                return response()->json([
                    'success' => false,
                    'message' => 'Ruta no encontrada',
                    'error' => 'La ruta solicitada no existe'
                ], 404);
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Método no permitido',
                    'error' => 'Usa el método HTTP correcto para este endpoint'
                ], 405);
            }
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado',
                    'error' => 'Debes enviar un token Bearer válido'
                ], 401);
            }
        });

        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
        });

        $exceptions->render(function (UniqueConstraintViolationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registro duplicado',
                    'error' => 'Ya existe un registro con estos datos. Intenta actualizar en lugar de crear uno nuevo.'
                ], 409);
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*') && config('app.debug') === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error del servidor',
                    'error' => 'Ha ocurrido un error inesperado'
                ], 500);
            }
        });
    })->create();
