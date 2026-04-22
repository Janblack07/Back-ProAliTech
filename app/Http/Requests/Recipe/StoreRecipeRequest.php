<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('recipes.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'recipe_name' => ['required', 'string', 'max:150'],
            'expected_yield' => ['required', 'numeric', 'gt:0'],
            'unit_measure' => ['required', 'string', 'max:30'],
            'estimated_labor_cost' => ['nullable', 'numeric', 'min:0'],
            'estimated_energy_cost' => ['nullable', 'numeric', 'min:0'],
            'estimated_indirect_cost' => ['nullable', 'numeric', 'min:0'],
            'estimated_waste_percent' => ['nullable', 'numeric', 'min:0'],
            'instructions' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],

            'details' => ['required', 'array', 'min:1'],
            'details.*.raw_material_id' => ['required', 'integer', 'exists:raw_materials,id'],
            'details.*.quantity' => ['required', 'numeric', 'gt:0'],
            'details.*.unit_measure' => ['required', 'string', 'max:30'],
        ];
    }
}
