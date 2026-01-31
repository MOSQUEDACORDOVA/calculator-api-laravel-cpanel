<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class OperationController extends Controller
{
    /**
     * Redondear hacia arriba a 2 decimales.
     */
    private function roundUp(float $value, int $decimals = 2): float
    {
        $multiplier = pow(10, $decimals);
        return ceil($value * $multiplier) / $multiplier;
    }

    /**
     * Formato de respuesta uniforme.
     */
    private function apiResponse(bool $success, ?string $message = null, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Realizar una operación matemática.
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'num1' => 'required|numeric|between:-999.99,999.99',
            'operator' => ['required', Rule::in(['+', '-', '*', '/'])],
            'num2' => 'required|numeric|between:-999.99,999.99',
        ]);

        $num1 = $this->roundUp((float) $validated['num1']);
        $num2 = $this->roundUp((float) $validated['num2']);
        $operator = $validated['operator'];

        // Validar división por cero
        if ($operator === '/' && $num2 == 0) {
            return $this->apiResponse(false, 'División por cero no permitida', null, 400);
        }

        // Buscar si la operación ya existe
        $existingOperation = Operation::where('num1', $num1)
            ->where('operator', $operator)
            ->where('num2', $num2)
            ->first();

        if ($existingOperation) {
            return $this->apiResponse(true, 'Operación recuperada de caché', [
                'id' => $existingOperation->id,
                'result' => (float) $existingOperation->result,
                'cached' => true,
            ], 200);
        }

        // Calcular el resultado
        $result = match ($operator) {
            '+' => $num1 + $num2,
            '-' => $num1 - $num2,
            '*' => $num1 * $num2,
            '/' => $num1 / $num2,
        };

        $result = $this->roundUp($result);

        $operation = Operation::create([
            'num1' => $num1,
            'operator' => $operator,
            'num2' => $num2,
            'result' => $result,
        ]);

        return $this->apiResponse(true, 'Operación calculada correctamente', [
            'id' => $operation->id,
            'result' => $result,
            'cached' => false,
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
            ->map(fn($op) => [
                'id' => $op->id,
                'num1' => (float) $op->num1,
                'operator' => $op->operator,
                'num2' => (float) $op->num2,
                'result' => (float) $op->result,
                'created_at' => $op->created_at,
            ]);

        return $this->apiResponse(true, null, [
            'count' => $operations->count(),
            'operations' => $operations,
        ]);
    }

    /**
     * Obtener una operación específica por ID.
     */
    public function show(int $id): JsonResponse
    {
        $operation = Operation::find($id);

        if (!$operation) {
            return $this->apiResponse(false, 'Operación no encontrada', null, 404);
        }

        return $this->apiResponse(true, null, [
            'id' => $operation->id,
            'num1' => (float) $operation->num1,
            'operator' => $operation->operator,
            'num2' => (float) $operation->num2,
            'result' => (float) $operation->result,
            'created_at' => $operation->created_at,
        ]);
    }

    /**
     * Eliminar una operación específica por ID.
     */
    public function destroy(int $id): JsonResponse
    {
        $operation = Operation::find($id);

        if (!$operation) {
            return $this->apiResponse(false, 'Operación no encontrada', null, 404);
        }

        $operation->delete();

        return $this->apiResponse(true, 'Operación eliminada correctamente', [
            'deleted_id' => $id,
        ]);
    }

    /**
     * Eliminar todo el historial.
     */
    public function clearHistory(): JsonResponse
    {
        Operation::truncate();

        return $this->apiResponse(true, 'Historial eliminado correctamente', null);
    }
}
