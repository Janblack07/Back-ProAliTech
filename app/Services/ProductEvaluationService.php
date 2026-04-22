<?php

namespace App\Services;

use App\Enums\ProductIdeaStatusEnum;
use App\Enums\ViabilityResultEnum;
use App\Models\ProductEvaluation;
use App\Models\ProductIdea;

class ProductEvaluationService
{
    public function evaluate(ProductIdea $productIdea, array $data, int $userId): ProductEvaluation
    {
        $estimatedTotalCost = (float) $data['estimated_total_cost'];
        $estimatedUnitCost = (float) $data['estimated_unit_cost'];
        $proposedSalePrice = (float) $data['proposed_sale_price'];
        $fixedCosts = (float) ($data['fixed_costs'] ?? 0);

        $estimatedProfit = $proposedSalePrice - $estimatedUnitCost;
        $estimatedMarginPercent = $proposedSalePrice > 0
            ? ($estimatedProfit / $proposedSalePrice) * 100
            : 0;

        $breakEvenQuantity = $estimatedProfit > 0
            ? $fixedCosts / $estimatedProfit
            : null;

        $viabilityResult = $this->resolveViability($estimatedMarginPercent);
        $recommendation = $this->buildRecommendation(
            $viabilityResult,
            $estimatedMarginPercent,
            $data['recommendation_notes'] ?? null
        );

        $evaluation = ProductEvaluation::create([
            'product_idea_id' => $productIdea->id,
            'user_id' => $userId,
            'estimated_total_cost' => $estimatedTotalCost,
            'estimated_unit_cost' => $estimatedUnitCost,
            'proposed_sale_price' => $proposedSalePrice,
            'estimated_profit' => $estimatedProfit,
            'estimated_margin_percent' => $estimatedMarginPercent,
            'break_even_quantity' => $breakEvenQuantity,
            'viability_result' => $viabilityResult,
            'recommendation' => $recommendation,
            'evaluation_date' => now()->toDateString(),
        ]);

        $productIdea->update([
            'status' => ProductIdeaStatusEnum::EVALUATED->value,
        ]);

        return $evaluation->load('user');
    }

    protected function resolveViability(float $margin): string
    {
        if ($margin >= 30) {
            return ViabilityResultEnum::PROFITABLE->value;
        }

        if ($margin >= 15) {
            return ViabilityResultEnum::ACCEPTABLE->value;
        }

        if ($margin >= 5) {
            return ViabilityResultEnum::RISK->value;
        }

        return ViabilityResultEnum::NOT_PROFITABLE->value;
    }

    protected function buildRecommendation(string $result, float $margin, ?string $notes = null): string
    {
        $base = match ($result) {
            ViabilityResultEnum::PROFITABLE->value => "Producto recomendable. El margen estimado es de {$margin}%.",
            ViabilityResultEnum::ACCEPTABLE->value => "Producto viable con monitoreo. El margen estimado es de {$margin}%.",
            ViabilityResultEnum::RISK->value => "Producto con riesgo medio. Se recomienda revisar costos o precio. Margen estimado: {$margin}%.",
            default => "Producto no recomendable en el estado actual. Margen estimado: {$margin}%.",
        };

        return $notes ? $base . ' ' . $notes : $base;
    }
}
