<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'tax_cost' => 'required|numeric|min:0',
            'manufacturing_cost' => 'required|numeric|min:0',
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
            'name.required' => 'El nombre del producto es obligatorio',
            'price.required' => 'El precio es obligatorio',
            'price.min' => 'El precio debe ser mayor o igual a 0',
            'currency_id.required' => 'El ID de divisa es obligatorio',
            'currency_id.exists' => 'La divisa seleccionada no existe',
            'tax_cost.required' => 'El costo de impuesto es obligatorio',
            'manufacturing_cost.required' => 'El costo de fabricación es obligatorio',
        ];
    }
}
