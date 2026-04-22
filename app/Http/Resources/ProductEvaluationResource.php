<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductEvaluationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'estimated_total_cost' => $this->estimated_total_cost,
            'estimated_unit_cost' => $this->estimated_unit_cost,
            'proposed_sale_price' => $this->proposed_sale_price,
            'estimated_profit' => $this->estimated_profit,
            'estimated_margin_percent' => $this->estimated_margin_percent,
            'break_even_quantity' => $this->break_even_quantity,
            'viability_result' => $this->viability_result,
            'recommendation' => $this->recommendation,
            'evaluation_date' => $this->evaluation_date?->format('Y-m-d'),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'lastname' => $this->user->lastname,
                ];
            }),
        ];
    }
}
