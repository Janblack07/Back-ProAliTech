<?php

namespace App\Http\Requests\Production;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('productions.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'recipe_id' => ['nullable', 'integer', 'exists:recipes,id'],
            'batch_number' => ['required', 'string', 'max:100', 'unique:productions,batch_number'],
            'production_date' => ['required', 'date'],
            'expected_quantity' => ['required', 'numeric', 'gt:0'],
            'produced_quantity' => ['required', 'numeric', 'gt:0'],
            'unit_measure' => ['required', 'string', 'max:30'],
            'labor_cost' => ['nullable', 'numeric', 'min:0'],
            'energy_cost' => ['nullable', 'numeric', 'min:0'],
            'indirect_cost' => ['nullable', 'numeric', 'min:0'],
            'waste_quantity' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],

            'details' => ['required', 'array', 'min:1'],
            'details.*.raw_material_id' => ['required', 'integer', 'exists:raw_materials,id'],
            'details.*.quantity_used' => ['required', 'numeric', 'gt:0'],
            'details.*.unit_measure' => ['required', 'string', 'max:30'],
            'details.*.batch_number' => ['nullable', 'string', 'max:100'],
            'details.*.expiration_date' => ['nullable', 'date'],
        ];
    }
}
