<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'code' => $this->product->code,
                ];
            }),
            'recipe_name' => $this->recipe_name,
            'expected_yield' => $this->expected_yield,
            'unit_measure' => $this->unit_measure,
            'estimated_labor_cost' => $this->estimated_labor_cost,
            'estimated_energy_cost' => $this->estimated_energy_cost,
            'estimated_indirect_cost' => $this->estimated_indirect_cost,
            'estimated_waste_percent' => $this->estimated_waste_percent,
            'instructions' => $this->instructions,
            'status' => $this->status,
            'details' => RecipeDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at,
        ];
    }
}
