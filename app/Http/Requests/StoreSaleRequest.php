<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSaleRequest extends FormRequest
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
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'seller_id' => ['nullable', 'integer', 'exists:sellers,id'],
            'sale_date' => ['required', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'in:cash,transfer,card,check'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['required', 'numeric', 'gte:0'],
        ];
    }

    /**
     * Cross-field checks: discount cannot exceed subtotal and the initial
     * payment cannot exceed the computed total.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $subtotal = 0.0;

                foreach ($this->input('items', []) as $item) {
                    $subtotal += (float) $item['quantity'] * (float) $item['unit_price'];
                }

                $discount = (float) $this->input('discount', 0);

                if ($discount > $subtotal) {
                    $validator->errors()->add('discount', 'El descuento no puede superar el subtotal de la venta.');
                }

                $taxable = $subtotal - $discount;
                $total = round($taxable + $taxable * (float) config('sales.tax_rate'), 2);
                $paymentAmount = (float) $this->input('payment_amount', 0);

                if ($paymentAmount > $total) {
                    $validator->errors()->add('payment_amount', 'El pago inicial no puede superar el total de la venta.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'Debe seleccionar un cliente.',
            'client_id.exists' => 'El cliente seleccionado no existe.',
            'seller_id.exists' => 'El vendedor seleccionado no existe.',
            'sale_date.required' => 'La fecha de la venta es obligatoria.',
            'sale_date.date' => 'La fecha de la venta no es válida.',
            'discount.numeric' => 'El descuento debe ser un número.',
            'discount.min' => 'El descuento no puede ser negativo.',
            'payment_amount.numeric' => 'El pago inicial debe ser un número.',
            'payment_amount.min' => 'El pago inicial no puede ser negativo.',
            'payment_method.in' => 'El método de pago no es válido.',
            'notes.max' => 'Las notas no pueden superar 1000 caracteres.',
            'items.required' => 'Debe agregar al menos un producto a la venta.',
            'items.min' => 'Debe agregar al menos un producto a la venta.',
            'items.*.product_id.required' => 'Debe seleccionar un producto en cada línea.',
            'items.*.product_id.exists' => 'Uno de los productos seleccionados no existe.',
            'items.*.quantity.required' => 'La cantidad es obligatoria en cada línea.',
            'items.*.quantity.numeric' => 'La cantidad debe ser un número.',
            'items.*.quantity.gt' => 'La cantidad debe ser mayor que cero.',
            'items.*.unit_price.required' => 'El precio unitario es obligatorio en cada línea.',
            'items.*.unit_price.numeric' => 'El precio unitario debe ser un número.',
            'items.*.unit_price.gte' => 'El precio unitario no puede ser negativo.',
        ];
    }
}