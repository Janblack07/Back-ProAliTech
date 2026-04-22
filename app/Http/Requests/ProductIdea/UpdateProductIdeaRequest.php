<?php

namespace App\Http\Requests\ProductIdea;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductIdeaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product_ideas.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'idea_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'proposed_sale_price' => ['nullable', 'numeric', 'min:0'],
            'expected_demand' => ['nullable', 'string', 'max:30'],
            'competition_level' => ['nullable', 'string', 'max:30'],
            'estimated_labor_cost' => ['nullable', 'numeric', 'min:0'],
            'estimated_energy_cost' => ['nullable', 'numeric', 'min:0'],
            'estimated_indirect_cost' => ['nullable', 'numeric', 'min:0'],
            'estimated_waste_percent' => ['nullable', 'numeric', 'min:0'],
            'observations' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:30'],
        ];
    }
}
