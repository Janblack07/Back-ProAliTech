<?php

namespace App\Services;

use App\Models\RawMaterial;
use App\Models\Recipe;
use Illuminate\Support\Facades\DB;

class RecipeService
{
    public function paginate(?string $search = null, ?bool $status = null, int $perPage = 10)
    {
        return Recipe::query()
            ->with('product')
            ->when($search, function ($q) use ($search) {
                $q->where('recipe_name', 'like', "%{$search}%")
                  ->orWhereHas('product', fn ($sub) => $sub->where('name', 'like', "%{$search}%"));
            })
            ->when(!is_null($status), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Recipe
    {
        return DB::transaction(function () use ($data) {
            $details = $data['details'];
            unset($data['details']);

            $data['status'] = $data['status'] ?? true;

            $recipe = Recipe::create($data);

            foreach ($details as $detail) {
                $rawMaterial = RawMaterial::findOrFail($detail['raw_material_id']);
                $estimatedUnitCost = (float) $rawMaterial->cost_per_unit;
                $estimatedTotalCost = ((float) $detail['quantity']) * $estimatedUnitCost;

                $recipe->details()->create([
                    'raw_material_id' => $rawMaterial->id,
                    'quantity' => $detail['quantity'],
                    'unit_measure' => $detail['unit_measure'],
                    'estimated_unit_cost' => $estimatedUnitCost,
                    'estimated_total_cost' => $estimatedTotalCost,
                ]);
            }

            return $recipe->load(['product', 'details.rawMaterial']);
        });
    }

    public function update(Recipe $recipe, array $data): Recipe
    {
        return DB::transaction(function () use ($recipe, $data) {
            $details = $data['details'];
            unset($data['details']);

            $recipe->update($data);

            $recipe->details()->delete();

            foreach ($details as $detail) {
                $rawMaterial = RawMaterial::findOrFail($detail['raw_material_id']);
                $estimatedUnitCost = (float) $rawMaterial->cost_per_unit;
                $estimatedTotalCost = ((float) $detail['quantity']) * $estimatedUnitCost;

                $recipe->details()->create([
                    'raw_material_id' => $rawMaterial->id,
                    'quantity' => $detail['quantity'],
                    'unit_measure' => $detail['unit_measure'],
                    'estimated_unit_cost' => $estimatedUnitCost,
                    'estimated_total_cost' => $estimatedTotalCost,
                ]);
            }

            return $recipe->load(['product', 'details.rawMaterial']);
        });
    }

    public function show(Recipe $recipe): Recipe
    {
        return $recipe->load(['product', 'details.rawMaterial']);
    }
}
