<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdjustInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('inventory_adjustments.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'movement_type' => [
                'required',
                'string',
                Rule::in(['entry', 'exit', 'adjustment', 'waste', 'return']),
            ],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
