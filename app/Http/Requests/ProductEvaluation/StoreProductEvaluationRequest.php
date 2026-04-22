<?php

namespace App\Http\Requests\ProductEvaluation;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product_evaluations.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'estimated_total_cost' => ['required', 'numeric', 'gt:0'],
            'estimated_unit_cost' => ['required', 'numeric', 'gt:0'],
            'proposed_sale_price' => ['required', 'numeric', 'gt:0'],
            'fixed_costs' => ['nullable', 'numeric', 'min:0'],
            'recommendation_notes' => ['nullable', 'string'],
        ];
    }
}
