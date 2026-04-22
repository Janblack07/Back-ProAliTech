<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'inventory_type' => $this->inventory_type,
            'product_id' => $this->product_id,
            'raw_material_id' => $this->raw_material_id,
            'item' => $this->when(
                $this->relationLoaded('product') || $this->relationLoaded('rawMaterial'),
                function () {
                    if ($this->inventory_type === 'product' && $this->product) {
                        return [
                            'id' => $this->product->id,
                            'name' => $this->product->name,
                            'code' => $this->product->code,
                            'image_url' => $this->product->image_url,
                        ];
                    }

                    if ($this->inventory_type === 'raw_material' && $this->rawMaterial) {
                        return [
                            'id' => $this->rawMaterial->id,
                            'name' => $this->rawMaterial->name,
                            'code' => $this->rawMaterial->code,
                            'image_url' => $this->rawMaterial->image_url,
                        ];
                    }

                    return null;
                }
            ),
            'current_stock' => $this->current_stock,
            'unit_measure' => $this->unit_measure,
            'minimum_stock' => $this->minimum_stock,
            'is_low_stock' => (float) $this->current_stock <= (float) $this->minimum_stock,
            'last_movement_at' => $this->last_movement_at,
            'created_at' => $this->created_at,
        ];
    }
}
