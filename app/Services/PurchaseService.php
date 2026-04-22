<?php

namespace App\Services;

use App\Enums\InventoryMovementTypeEnum;
use App\Enums\InventoryReferenceTypeEnum;
use App\Enums\PurchaseStatusEnum;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PurchaseService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function paginate(?string $search = null, ?string $status = null, int $perPage = 10)
    {
        return Purchase::query()
            ->with(['supplier', 'user'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function ($sub) use ($search) {
                            $sub->where('business_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('purchase_date')
            ->paginate($perPage);
    }

    public function create(array $data, int $userId): Purchase
    {
        return DB::transaction(function () use ($data, $userId) {
            $details = $data['details'];
            unset($data['details']);

            $subtotal = 0;

            foreach ($details as $detail) {
                $subtotal += ((float) $detail['quantity']) * ((float) $detail['unit_price']);
            }

            $tax = (float) ($data['tax'] ?? 0);
            $total = $subtotal + $tax;

            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'user_id' => $userId,
                'purchase_date' => $data['purchase_date'],
                'invoice_number' => $data['invoice_number'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
                'status' => PurchaseStatusEnum::REGISTERED->value,
            ]);

            foreach ($details as $detail) {
                $rawMaterial = RawMaterial::query()
                    ->with('inventory')
                    ->findOrFail($detail['raw_material_id']);

                $lineSubtotal = ((float) $detail['quantity']) * ((float) $detail['unit_price']);

                $purchase->details()->create([
                    'raw_material_id' => $rawMaterial->id,
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'subtotal' => $lineSubtotal,
                    'expiration_date' => $detail['expiration_date'] ?? null,
                    'batch_number' => $detail['batch_number'] ?? null,
                ]);

                if (!$rawMaterial->inventory) {
                    throw new RuntimeException("La materia prima {$rawMaterial->name} no tiene inventario asociado.");
                }

                $this->inventoryService->adjustStock(
                    $rawMaterial->inventory,
                    InventoryMovementTypeEnum::ENTRY->value,
                    (float) $detail['quantity'],
                    'Ingreso por compra registrada.',
                    $userId,
                    InventoryReferenceTypeEnum::PURCHASE->value,
                    (int) $purchase->getKey()
                );
            }

            Expense::create([
                'user_id' => $userId,
                'purchase_id' => $purchase->id,
                'expense_type' => 'purchase_raw_material',
                'concept' => 'Compra de materia prima',
                'amount' => $total,
                'expense_date' => $purchase->purchase_date,
                'notes' => $purchase->notes,
                'status' => 'registered',
            ]);

            return $purchase->load([
                'supplier',
                'user',
                'details.rawMaterial',
            ]);
        });
    }

    public function show(Purchase $purchase): Purchase
    {
        return $purchase->load([
            'supplier',
            'user',
            'details.rawMaterial',
            'expense',
        ]);
    }
}
