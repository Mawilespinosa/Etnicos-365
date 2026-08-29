<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRawMaterialRequest extends FormRequest
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
        $rawMaterialId = $this->route('raw_material')->id;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('raw_materials', 'code')->ignore($rawMaterialId)],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'unit' => ['required', 'string', 'in:unit,meter,kg,roll'],
            'stock_qty' => ['nullable', 'numeric', 'min:0'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'El código de la materia prima es obligatorio.',
            'code.unique' => 'Ya existe una materia prima con ese código.',
            'name.required' => 'El nombre de la materia prima es obligatorio.',
            'unit.required' => 'La unidad de medida es obligatoria.',
            'unit.in' => 'La unidad de medida seleccionada no es válida.',
            'stock_qty.min' => 'El stock no puede ser negativo.',
            'min_stock.min' => 'El stock mínimo no puede ser negativo.',
            'cost.min' => 'El costo no puede ser negativo.',
        ];
    }
}