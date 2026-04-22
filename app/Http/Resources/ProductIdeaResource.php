<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductIdeaResource extends JsonResource
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
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'lastname' => $this->user->lastname,
                ];
            }),
            'idea_name' => $this->idea_name,
            'description' => $this->description,
            'proposed_sale_price' => $this->proposed_sale_price,
            'expected_demand' => $this->expected_demand,
            'competition_level' => $this->competition_level,
            'estimated_labor_cost' => $this->estimated_labor_cost,
            'estimated_energy_cost' => $this->estimated_energy_cost,
            'estimated_indirect_cost' => $this->estimated_indirect_cost,
            'estimated_waste_percent' => $this->estimated_waste_percent,
            'observations' => $this->observations,
            'status' => $this->status,
            'latest_evaluation' => $this->whenLoaded('evaluations', function () {
                $latest = $this->evaluations->sortByDesc('id')->first();

                return $latest ? new ProductEvaluationResource($latest) : null;
            }),
            'created_at' => $this->created_at,
        ];
    }
}
