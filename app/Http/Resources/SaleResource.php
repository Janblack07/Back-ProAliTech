<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'lastname' => $this->user->lastname,
                ];
            }),
            'sale_date' => $this->sale_date?->format('Y-m-d'),
            'invoice_number' => $this->invoice_number,
            'customer_name' => $this->customer_name,
            'customer_document' => $this->customer_document,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'total' => $this->total,
            'notes' => $this->notes,
            'status' => $this->status,
            'details' => SaleDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at,
        ];
    }
}
