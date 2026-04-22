<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    'id' => $this->supplier->id,
                    'business_name' => $this->supplier->business_name,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'lastname' => $this->user->lastname,
                ];
            }),
            'purchase_date' => $this->purchase_date?->format('Y-m-d'),
            'invoice_number' => $this->invoice_number,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
            'notes' => $this->notes,
            'status' => $this->status,
            'details' => PurchaseDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at,
        ];
    }
}
