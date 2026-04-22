<?php

namespace App\Http\Requests\RawMaterial;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRawMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('raw_materials.update') ?? false;
    }

    public function rules(): array
    {
        $rawMaterialId = $this->route('raw_material')->id;

        return [
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('raw_materials', 'code')->ignore($rawMaterialId),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'material_type' => ['required', 'string', 'max:50'],
            'unit_measure' => ['required', 'string', 'max:30'],
            'cost_per_unit' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['nullable', 'numeric', 'min:0'],
            'expiration_date' => ['nullable', 'date'],
            'status' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
