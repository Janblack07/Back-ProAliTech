<?php

namespace App\Services;

use App\Enums\InventoryMovementTypeEnum;
use App\Enums\InventoryReferenceTypeEnum;
use App\Enums\ProductionStatusEnum;
use App\Models\Production;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductionService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function paginate(?string $search = null, ?string $status = null, int $perPage = 10)
    {
        return Production::query()
            ->with(['product', 'recipe', 'user'])
            ->when($search, function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhereHas('product', fn ($sub) => $sub->where('name', 'like', "%{$search}%"));
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('production_date')
            ->paginate($perPage);
    }

    public function create(array $data, int $userId): Production
    {
        return DB::transaction(function () use ($data, $userId) {
            $details = $data['details'];
            unset($data['details']);

            $laborCost = (float) ($data['labor_cost'] ?? 0);
            $energyCost = (float) ($data['energy_cost'] ?? 0);
            $indirectCost = (float) ($data['indirect_cost'] ?? 0);
            $wasteQuantity = (float) ($data['waste_quantity'] ?? 0);

            $production = Production::create([
                'product_id' => $data['product_id'],
                'recipe_id' => $data['recipe_id'] ?? null,
                'user_id' => $userId,
                'batch_number' => $data['batch_number'],
                'production_date' => $data['production_date'],
                'expected_quantity' => $data['expected_quantity'],
                'produced_quantity' => $data['produced_quantity'],
                'unit_measure' => $data['unit_measure'],
                'labor_cost' => $laborCost,
                'energy_cost' => $energyCost,
                'indirect_cost' => $indirectCost,
                'waste_quantity' => $wasteQuantity,
                'total_cost' => 0,
                'unit_cost' => 0,
                'notes' => $data['notes'] ?? null,
                'status' => ProductionStatusEnum::COMPLETED->value,
            ]);

            $rawMaterialsCost = 0;

            foreach ($details as $detail) {
                $rawMaterial = RawMaterial::query()
                    ->with('inventory')
                    ->findOrFail($detail['raw_material_id']);

                if (!$rawMaterial->inventory) {
                    throw new RuntimeException("La materia prima {$rawMaterial->name} no tiene inventario asociado.");
                }

                $quantityUsed = (float) $detail['quantity_used'];
                $unitCost = (float) $rawMaterial->cost_per_unit;
                $totalCost = $quantityUsed * $unitCost;

                if ((float) $rawMaterial->inventory->current_stock < $quantityUsed) {
                    throw new RuntimeException("Stock insuficiente para la materia prima {$rawMaterial->name}.");
                }

                $production->details()->create([
                    'raw_material_id' => $rawMaterial->id,
                    'quantity_used' => $quantityUsed,
                    'unit_measure' => $detail['unit_measure'],
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                    'batch_number' => $detail['batch_number'] ?? null,
                    'expiration_date' => $detail['expiration_date'] ?? null,
                ]);

                $this->inventoryService->adjustStock(
                    $rawMaterial->inventory,
                    InventoryMovementTypeEnum::EXIT->value,
                    $quantityUsed,
                    'Salida por producción registrada.',
                    $userId,
                    InventoryReferenceTypeEnum::PRODUCTION->value,
                    (int) $production->getKey()
                );

                $rawMaterialsCost += $totalCost;
            }

            $totalCost = $rawMaterialsCost + $laborCost + $energyCost + $indirectCost;
            $producedQuantity = (float) $data['produced_quantity'];

            if ($producedQuantity <= 0) {
                throw new RuntimeException('La cantidad producida debe ser mayor a cero.');
            }

            $unitCost = $totalCost / $producedQuantity;

            $production->update([
                'total_cost' => $totalCost,
                'unit_cost' => $unitCost,
            ]);

            $product = $production->product()->with('inventory')->first();

            if (!$product || !$product->inventory) {
                throw new RuntimeException('El producto no tiene inventario asociado.');
            }

            $this->inventoryService->adjustStock(
                $product->inventory,
                InventoryMovementTypeEnum::ENTRY->value,
                $producedQuantity,
                'Ingreso por producción completada.',
                $userId,
                InventoryReferenceTypeEnum::PRODUCTION->value,
                (int) $production->getKey()
            );

            return $production->load([
                'product',
                'recipe',
                'user',
                'details.rawMaterial',
            ]);
        });
    }

    public function show(Production $production): Production
    {
        return $production->load([
            'product',
            'recipe',
            'user',
            'details.rawMaterial',
        ]);
    }
}
