<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class OperationController extends Controller
{
    /**
     * Redondear a 2 decimales usando round() estándar (half-up).
     * Solo se aplica al resultado final, no a los operandos.
     */
    private function roundResult(float $value, int $decimals = 2): float
    {
        return round($value, $decimals);
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
     * - Los operandos se usan tal como se reciben (sin redondear)
     * - Solo el resultado final se redondea a 2 decimales
     * - Para el cache, se redondean los operandos a 2 decimales para comparación
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'num1' => 'required|numeric|between:-999.99,999.99',
            'operator' => ['required', Rule::in(['+', '-', '*', '/'])],
            'num2' => 'required|numeric|between:-999.99,999.99',
        ]);

        // Valores originales para el cálculo
        $num1 = (float) $validated['num1'];
        $num2 = (float) $validated['num2'];
        $operator = $validated['operator'];

        // Valores redondeados para almacenamiento y cache (BD solo soporta 2 decimales)
        $num1Stored = $this->roundResult($num1);
        $num2Stored = $this->roundResult($num2);

        // Validar división por cero
        if ($operator === '/' && $num2 == 0) {
            return $this->apiResponse(false, 'División por cero no permitida', null, 400);
        }

        // Buscar si la operación ya existe (usando valores redondeados)
        $existingOperation = Operation::where('num1', $num1Stored)
            ->where('operator', $operator)
            ->where('num2', $num2Stored)
            ->first();

        if ($existingOperation) {
            return $this->apiResponse(true, 'Operación recuperada de caché', [
                'id' => $existingOperation->id,
                'result' => (float) $existingOperation->result,
                'cached' => true,
            ], 200);
        }

        // Calcular con valores originales
        $result = match ($operator) {
            '+' => $num1 + $num2,
            '-' => $num1 - $num2,
            '*' => $num1 * $num2,
            '/' => $num1 / $num2,
        };

        // Solo redondear el resultado final
        $result = $this->roundResult($result);

        // Guardar con valores redondeados (restricción de BD)
        $operation = Operation::create([
            'num1' => $num1Stored,
            'operator' => $operator,
            'num2' => $num2Stored,
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

        return $this->apiResponse(true, 'Operación eliminada correctamente', null);
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
