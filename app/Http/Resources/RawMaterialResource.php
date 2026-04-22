<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RawMaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'supplier_id' => $this->supplier_id,
            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    'id' => $this->supplier->id,
                    'business_name' => $this->supplier->business_name,
                ];
            }),
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'material_type' => $this->material_type,
            'unit_measure' => $this->unit_measure,
            'cost_per_unit' => $this->cost_per_unit,
            'minimum_stock' => $this->minimum_stock,
            'expiration_date' => $this->expiration_date?->format('Y-m-d'),
            'image_url' => $this->image_url,
            'image_public_id' => $this->image_public_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
