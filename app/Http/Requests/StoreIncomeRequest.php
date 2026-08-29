<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncomeRequest extends FormRequest
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
            'description' => ['required', 'string', 'max:1000'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'income_date' => ['required', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'description.required' => 'La descripción del ingreso es obligatoria.',
            'description.string' => 'La descripción debe ser un texto.',
            'description.max' => 'La descripción no puede superar 1000 caracteres.',
            'amount.required' => 'El monto del ingreso es obligatorio.',
            'amount.numeric' => 'El monto debe ser un número.',
            'amount.gt' => 'El monto debe ser mayor que cero.',
            'income_date.required' => 'La fecha del ingreso es obligatoria.',
            'income_date.date' => 'La fecha del ingreso no es válida.',
        ];
    }
}
