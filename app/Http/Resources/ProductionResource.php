<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'code' => $this->product->code,
                ];
            }),
            'recipe' => $this->whenLoaded('recipe', function () {
                return [
                    'id' => $this->recipe?->id,
                    'recipe_name' => $this->recipe?->recipe_name,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'lastname' => $this->user->lastname,
                ];
            }),
            'batch_number' => $this->batch_number,
            'production_date' => $this->production_date?->format('Y-m-d'),
            'expected_quantity' => $this->expected_quantity,
            'produced_quantity' => $this->produced_quantity,
            'unit_measure' => $this->unit_measure,
            'labor_cost' => $this->labor_cost,
            'energy_cost' => $this->energy_cost,
            'indirect_cost' => $this->indirect_cost,
            'waste_quantity' => $this->waste_quantity,
            'total_cost' => $this->total_cost,
            'unit_cost' => $this->unit_cost,
            'notes' => $this->notes,
            'status' => $this->status,
            'details' => ProductionDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at,
        ];
    }
}
