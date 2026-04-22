<?php

namespace App\Services;

use App\Enums\InventoryReferenceTypeEnum;
use App\Enums\InventoryTypeEnum;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    public function paginate(
        ?string $search = null,
        ?string $inventoryType = null,
        ?bool $lowStock = null,
        int $perPage = 10
    ) {
        return Inventory::query()
            ->with(['product', 'rawMaterial'])
            ->when($inventoryType, fn ($q) => $q->where('inventory_type', $inventoryType))
            ->when($lowStock === true, fn ($q) => $q->whereColumn('current_stock', '<=', 'minimum_stock'))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->whereHas('product', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })->orWhereHas('rawMaterial', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->paginate($perPage);
    }

    public function movements(int $inventoryId, int $perPage = 10)
    {
        return InventoryMovement::query()
            ->with('user')
            ->where('inventory_id', $inventoryId)
            ->latest('movement_date')
            ->paginate($perPage);
    }

    public function createInventoryForProduct(Product $product): Inventory
    {
        return Inventory::firstOrCreate(
            [
                'inventory_type' => InventoryTypeEnum::PRODUCT->value,
                'product_id' => $product->id,
            ],
            [
                'raw_material_id' => null,
                'current_stock' => 0,
                'unit_measure' => $product->unit_measure,
                'minimum_stock' => $product->minimum_stock,
                'last_movement_at' => null,
            ]
        );
    }

    public function createInventoryForRawMaterial(RawMaterial $rawMaterial): Inventory
    {
        return Inventory::firstOrCreate(
            [
                'inventory_type' => InventoryTypeEnum::RAW_MATERIAL->value,
                'raw_material_id' => $rawMaterial->id,
            ],
            [
                'product_id' => null,
                'current_stock' => 0,
                'unit_measure' => $rawMaterial->unit_measure,
                'minimum_stock' => $rawMaterial->minimum_stock,
                'last_movement_at' => null,
            ]
        );
    }

    public function adjustStock(
        Inventory $inventory,
        string $movementType,
        float $quantity,
        ?string $description = null,
        ?int $userId = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): Inventory {
        return DB::transaction(function () use (
            $inventory,
            $movementType,
            $quantity,
            $description,
            $userId,
            $referenceType,
            $referenceId
        ) {
            $stockBefore = (float) $inventory->current_stock;
            $stockAfter = $stockBefore;

            if (in_array($movementType, ['entry', 'return'])) {
                $stockAfter += $quantity;
            } elseif (in_array($movementType, ['exit', 'waste'])) {
                $stockAfter -= $quantity;
            } elseif ($movementType === 'adjustment') {
                $stockAfter += $quantity;
            }

            if ($stockAfter < 0) {
                throw new RuntimeException('El stock no puede quedar en negativo.');
            }

            $inventory->update([
                'current_stock' => $stockAfter,
                'last_movement_at' => now(),
            ]);

            InventoryMovement::create([
                'inventory_id' => $inventory->id,
                'user_id' => $userId,
                'movement_type' => $movementType,
                'reference_type' => $referenceType ?? InventoryReferenceTypeEnum::MANUAL->value,
                'reference_id' => $referenceId,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'movement_date' => now(),
                'description' => $description,
            ]);

            return $inventory->fresh(['product', 'rawMaterial']);
        });
    }

    public function syncProductInventoryMetadata(Product $product): void
    {
        $inventory = $product->inventory;

        if ($inventory) {
            $inventory->update([
                'unit_measure' => $product->unit_measure,
                'minimum_stock' => $product->minimum_stock,
            ]);
        }
    }

    public function syncRawMaterialInventoryMetadata(RawMaterial $rawMaterial): void
    {
        $inventory = $rawMaterial->inventory;

        if ($inventory) {
            $inventory->update([
                'unit_measure' => $rawMaterial->unit_measure,
                'minimum_stock' => $rawMaterial->minimum_stock,
            ]);
        }
    }
}
