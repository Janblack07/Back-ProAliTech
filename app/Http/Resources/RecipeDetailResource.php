<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'raw_material_id' => $this->raw_material_id,
            'raw_material' => $this->whenLoaded('rawMaterial', function () {
                return [
                    'id' => $this->rawMaterial->id,
                    'name' => $this->rawMaterial->name,
                    'code' => $this->rawMaterial->code,
                    'unit_measure' => $this->rawMaterial->unit_measure,
                ];
            }),
            'quantity' => $this->quantity,
            'unit_measure' => $this->unit_measure,
            'estimated_unit_cost' => $this->estimated_unit_cost,
            'estimated_total_cost' => $this->estimated_total_cost,
        ];
    }
}
