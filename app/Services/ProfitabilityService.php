<?php

namespace App\Services;

use App\Enums\ViabilityResultEnum;
use App\Models\Product;

class ProfitabilityService
{
    public function productProfitability(?string $search = null, int $perPage = 10)
    {
        return Product::query()
            ->with('category')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->through(function (Product $product) {
                $salePrice = (float) $product->sale_price;
                $costPrice = (float) $product->cost_price;
                $profit = $salePrice - $costPrice;
                $margin = $salePrice > 0 ? ($profit / $salePrice) * 100 : 0;

                return [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'category' => $product->category?->name,
                    'cost_price' => round($costPrice, 2),
                    'sale_price' => round($salePrice, 2),
                    'profit' => round($profit, 2),
                    'margin_percent' => round($margin, 2),
                    'result' => $this->resolveViability($margin),
                ];
            });
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
}
