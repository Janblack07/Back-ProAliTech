<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionDetailResource extends JsonResource
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
            'quantity_used' => $this->quantity_used,
            'unit_measure' => $this->unit_measure,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'batch_number' => $this->batch_number,
            'expiration_date' => $this->expiration_date?->format('Y-m-d'),
        ];
    }
}
