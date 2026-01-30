<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Product;

class StoreProductPriceRequest extends FormRequest
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
            'currency_id' => 'required|exists:currencies,id',
            'price' => 'required|numeric|min:0|max:99999999.99',
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
            'currency_id.required' => 'El ID de divisa es obligatorio',
            'currency_id.exists' => 'La divisa seleccionada no existe',
            'price.required' => 'El precio es obligatorio',
            'price.numeric' => 'El precio debe ser un valor numérico',
            'price.min' => 'El precio debe ser mayor o igual a 0',
            'price.max' => 'El precio excede el límite máximo',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $product = Product::find($this->route('product'));
            
            if ($product && $this->currency_id == $product->currency_id) {
                $validator->errors()->add(
                    'currency_id',
                    'No puedes agregar un precio en la divisa base del producto. Usa la API de productos para actualizar el precio base.'
                );
            }
        });
    }
}
