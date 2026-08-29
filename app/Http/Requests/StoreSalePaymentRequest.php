<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalePaymentRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_date' => ['required', 'date'],
            'method' => ['nullable', 'string', 'in:cash,transfer,card,check'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'El monto del pago es obligatorio.',
            'amount.numeric' => 'El monto del pago debe ser un número.',
            'amount.gt' => 'El monto del pago debe ser mayor que cero.',
            'payment_date.required' => 'La fecha del pago es obligatoria.',
            'payment_date.date' => 'La fecha del pago no es válida.',
            'method.in' => 'El método de pago no es válido.',
            'notes.max' => 'Las notas no pueden superar 1000 caracteres.',
        ];
    }
}