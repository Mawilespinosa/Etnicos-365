<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'numeric', function ($attribute, $value, $fail): void {
                $type = $this->input('type');
                $quantity = (float) $value;

                if ($type === 'adjustment') {
                    if ($quantity == 0) {
                        $fail('La cantidad del ajuste no puede ser cero.');
                    }
                } elseif ($quantity <= 0) {
                    $fail('La cantidad debe ser mayor que cero.');
                }
            }],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Debe seleccionar un producto.',
            'product_id.exists' => 'El producto seleccionado no existe.',
            'type.required' => 'Debe seleccionar el tipo de movimiento.',
            'type.in' => 'El tipo de movimiento no es válido.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'reason.required' => 'El motivo es obligatorio.',
            'reason.max' => 'El motivo no puede superar 1000 caracteres.',
        ];
    }
}