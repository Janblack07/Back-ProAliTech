<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'inventory_id' => $this->inventory_id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user?->id,
                    'name' => $this->user?->name,
                    'lastname' => $this->user?->lastname,
                ];
            }),
            'movement_type' => $this->movement_type,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'quantity' => $this->quantity,
            'stock_before' => $this->stock_before,
            'stock_after' => $this->stock_after,
            'movement_date' => $this->movement_date,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
