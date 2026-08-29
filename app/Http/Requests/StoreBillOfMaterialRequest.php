<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBillOfMaterialRequest extends FormRequest
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
        $productId = $this->route('product')->id;

        return [
            'raw_material_id' => [
                'required',
                'exists:raw_materials,id',
                Rule::unique('bill_of_materials', 'raw_material_id')
                    ->where('product_id', $productId),
            ],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'raw_material_id.required' => 'Debe seleccionar una materia prima.',
            'raw_material_id.exists' => 'La materia prima seleccionada no existe.',
            'raw_material_id.unique' => 'Esa materia prima ya está agregada a la lista de materiales de este producto.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.min' => 'La cantidad debe ser mayor a cero.',
        ];
    }
}