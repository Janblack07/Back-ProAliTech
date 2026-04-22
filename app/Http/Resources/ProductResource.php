<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'unit_measure' => $this->unit_measure,
            'cost_price' => $this->cost_price,
            'sale_price' => $this->sale_price,
            'minimum_stock' => $this->minimum_stock,
            'shelf_life_days' => $this->shelf_life_days,
            'image_url' => $this->image_url,
            'image_public_id' => $this->image_public_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
