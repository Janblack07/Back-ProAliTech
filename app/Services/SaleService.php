<?php

namespace App\Services;

use App\Enums\InventoryMovementTypeEnum;
use App\Enums\InventoryReferenceTypeEnum;
use App\Enums\SaleStatusEnum;
use App\Models\Income;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SaleService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function paginate(?string $search = null, ?string $status = null, int $perPage = 10)
    {
        return Sale::query()
            ->with('user')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_document', 'like', "%{$search}%");
                });
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('sale_date')
            ->paginate($perPage);
    }

    public function create(array $data, int $userId): Sale
    {
        return DB::transaction(function () use ($data, $userId) {
            $details = $data['details'];
            unset($data['details']);

            $subtotal = 0;
            $globalDiscount = (float) ($data['discount'] ?? 0);

            $preparedDetails = [];

            foreach ($details as $detail) {
                $quantity = (float) $detail['quantity'];
                $unitPrice = (float) $detail['unit_price'];
                $lineDiscount = (float) ($detail['discount'] ?? 0);
                $lineSubtotal = ($quantity * $unitPrice) - $lineDiscount;

                if ($lineSubtotal < 0) {
                    throw new RuntimeException('El subtotal de una línea no puede ser negativo.');
                }

                $preparedDetails[] = [
                    ...$detail,
                    'line_subtotal' => $lineSubtotal,
                ];

                $subtotal += $lineSubtotal;
            }

            $tax = (float) ($data['tax'] ?? 0);
            $total = $subtotal + $tax - $globalDiscount;

            if ($total < 0) {
                throw new RuntimeException('El total de la venta no puede ser negativo.');
            }

            $sale = Sale::create([
                'user_id' => $userId,
                'sale_date' => $data['sale_date'],
                'invoice_number' => $data['invoice_number'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_document' => $data['customer_document'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $globalDiscount,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
                'status' => SaleStatusEnum::REGISTERED->value,
            ]);

            foreach ($preparedDetails as $detail) {
                $product = Product::query()
                    ->with('inventory')
                    ->findOrFail($detail['product_id']);

                if (!$product->inventory) {
                    throw new RuntimeException("El producto {$product->name} no tiene inventario asociado.");
                }

                if ((float) $product->inventory->current_stock < (float) $detail['quantity']) {
                    throw new RuntimeException("Stock insuficiente para el producto {$product->name}.");
                }

                $sale->details()->create([
                    'product_id' => $product->id,
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'discount' => $detail['discount'] ?? 0,
                    'subtotal' => $detail['line_subtotal'],
                ]);

                $this->inventoryService->adjustStock(
                    $product->inventory,
                    InventoryMovementTypeEnum::EXIT->value,
                    (float) $detail['quantity'],
                    'Salida por venta registrada.',
                    $userId,
                    InventoryReferenceTypeEnum::SALE->value,
                    (int) $sale->getKey()
                );
            }

            Income::create([
                'user_id' => $userId,
                'sale_id' => $sale->id,
                'income_type' => 'sale',
                'concept' => 'Ingreso por venta',
                'amount' => $total,
                'income_date' => $sale->sale_date,
                'notes' => $sale->notes,
                'status' => 'registered',
            ]);

            return $sale->load([
                'user',
                'details.product',
            ]);
        });
    }

    public function show(Sale $sale): Sale
    {
        return $sale->load([
            'user',
            'details.product',
            'income',
        ]);
    }
}
