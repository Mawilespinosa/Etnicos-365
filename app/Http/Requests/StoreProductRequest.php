<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:50', 'unique:products,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'size' => ['nullable', 'string', 'max:20'],
            'color' => ['nullable', 'string', 'max:50'],
            'model' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'cost' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_qty' => ['nullable', 'numeric', 'min:0'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'El código del producto es obligatorio.',
            'code.unique' => 'Ya existe un producto con ese código.',
            'name.required' => 'El nombre del producto es obligatorio.',
            'cost.required' => 'El costo del producto es obligatorio.',
            'cost.min' => 'El costo no puede ser negativo.',
            'price.required' => 'El precio del producto es obligatorio.',
            'price.min' => 'El precio no puede ser negativo.',
            'stock_qty.min' => 'El stock no puede ser negativo.',
            'min_stock.min' => 'El stock mínimo no puede ser negativo.',
        ];
    }
}