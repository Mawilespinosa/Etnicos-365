<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
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
            'category' => ['required', 'in:raw_material,labor,services,other'],
            'description' => ['required', 'string', 'max:1000'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'expense_date' => ['required', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category.required' => 'Debe seleccionar la categoría del egreso.',
            'category.in' => 'La categoría del egreso no es válida.',
            'description.required' => 'La descripción del egreso es obligatoria.',
            'description.string' => 'La descripción debe ser un texto.',
            'description.max' => 'La descripción no puede superar 1000 caracteres.',
            'amount.required' => 'El monto del egreso es obligatorio.',
            'amount.numeric' => 'El monto debe ser un número.',
            'amount.gt' => 'El monto debe ser mayor que cero.',
            'expense_date.required' => 'La fecha del egreso es obligatoria.',
            'expense_date.date' => 'La fecha del egreso no es válida.',
        ];
    }
}
