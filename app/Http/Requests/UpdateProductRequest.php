<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'currency_id' => 'sometimes|exists:currencies,id',
            'tax_cost' => 'sometimes|numeric|min:0',
            'manufacturing_cost' => 'sometimes|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'price.numeric' => 'El precio debe ser un valor numérico',
            'price.min' => 'El precio debe ser mayor o igual a 0',
            'currency_id.exists' => 'La divisa seleccionada no existe',
            'tax_cost.numeric' => 'El costo de impuesto debe ser numérico',
            'tax_cost.min' => 'El costo de impuesto debe ser mayor o igual a 0',
            'manufacturing_cost.numeric' => 'El costo de fabricación debe ser numérico',
            'manufacturing_cost.min' => 'El costo de fabricación debe ser mayor o igual a 0',
        ];
    }
}
