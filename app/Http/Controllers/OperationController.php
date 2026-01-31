<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class OperationController extends Controller
{
    /**
     * Realizar una operación matemática.
     * Si la operación ya existe en la BD, devuelve el resultado existente.
     * Si no existe, la calcula y la guarda.
     */
    /**
     * Redondear hacia arriba a 2 decimales.
     */
    private function roundUp(float $value, int $decimals = 2): float
    {
        $multiplier = pow(10, $decimals);
        return ceil($value * $multiplier) / $multiplier;
    }

    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'num1' => 'required|numeric|between:-999.99,999.99',
            'operator' => ['required', Rule::in(['+', '-', '*', '/'])],
            'num2' => 'required|numeric|between:-999.99,999.99',
        ]);

        // Redondear hacia arriba a 2 decimales
        $num1 = $this->roundUp((float) $validated['num1']);
        $num2 = $this->roundUp((float) $validated['num2']);
        $operator = $validated['operator'];

        // Validar división por cero antes de buscar/calcular
        if ($operator === '/' && $num2 == 0) {
            return response()->json([
                'success' => false,
                'error' => 'División por cero no permitida',
                'num1' => $num1,
                'operator' => $operator,
                'num2' => $num2,
            ], 400);
        }

        // Buscar si la operación ya existe
        $existingOperation = Operation::where('num1', $num1)
            ->where('operator', $operator)
            ->where('num2', $num2)
            ->first();

        if ($existingOperation) {
            // Operación ya existe, devolver resultado cacheado
            return response()->json([
                'success' => true,
                'cached' => true,
                'data' => [
                    'id' => $existingOperation->id,
                    'num1' => (float) $existingOperation->num1,
                    'operator' => $existingOperation->operator,
                    'num2' => (float) $existingOperation->num2,
                    'result' => (float) $existingOperation->result,
                    'expression' => "{$num1} {$operator} {$num2} = {$existingOperation->result}",
                    'created_at' => $existingOperation->created_at,
                ],
            ], 200);
        }

        // Calcular el resultado
        $result = match ($operator) {
            '+' => $num1 + $num2,
            '-' => $num1 - $num2,
            '*' => $num1 * $num2,
            '/' => $num1 / $num2,
        };

        // Redondear resultado hacia arriba a 2 decimales
        $result = $this->roundUp($result);

        // Guardar en la base de datos
        $operation = Operation::create([
            'num1' => $num1,
            'operator' => $operator,
            'num2' => $num2,
            'result' => $result,
        ]);

        return response()->json([
            'success' => true,
            'cached' => false,
            'data' => [
                'id' => $operation->id,
                'num1' => $num1,
                'operator' => $operator,
                'num2' => $num2,
                'result' => $result,
                'expression' => "{$num1} {$operator} {$num2} = {$result}",
                'created_at' => $operation->created_at,
            ],
        ], 201);
    }

    /**
     * Listar el historial de operaciones.
     */
    public function history(Request $request): JsonResponse
    {
        $limit = $request->query('limit', 50);
        
        $operations = Operation::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($op) {
                return [
                    'id' => $op->id,
                    'num1' => $op->num1,
                    'operator' => $op->operator,
                    'num2' => $op->num2,
                    'result' => $op->result,
                    'expression' => "{$op->num1} {$op->operator} {$op->num2} = {$op->result}",
                    'created_at' => $op->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $operations->count(),
            'data' => $operations,
        ]);
    }

    /**
     * Obtener una operación específica por ID.
     */
    public function show(int $id): JsonResponse
    {
        $operation = Operation::find($id);

        if (!$operation) {
            return response()->json([
                'success' => false,
                'error' => 'Operación no encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $operation->id,
                'num1' => $operation->num1,
                'operator' => $operation->operator,
                'num2' => $operation->num2,
                'result' => $operation->result,
                'expression' => "{$operation->num1} {$operation->operator} {$operation->num2} = {$operation->result}",
                'created_at' => $operation->created_at,
            ],
        ]);
    }

    /**
     * Eliminar una operación específica por ID.
     */
    public function destroy(int $id): JsonResponse
    {
        $operation = Operation::find($id);

        if (!$operation) {
            return response()->json([
                'success' => false,
                'error' => 'Operación no encontrada',
            ], 404);
        }

        $operation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Operación eliminada correctamente',
            'deleted_id' => $id,
        ]);
    }

    /**
     * Eliminar todo el historial.
     */
    public function clearHistory(): JsonResponse
    {
        $deleted = Operation::truncate();

        return response()->json([
            'success' => true,
            'message' => 'Historial eliminado correctamente',
        ]);
    }
}
