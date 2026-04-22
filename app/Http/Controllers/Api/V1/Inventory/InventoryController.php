<?php

namespace App\Http\Controllers\Api\V1\Inventory;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Inventory\AdjustInventoryRequest;
use App\Http\Resources\InventoryMovementResource;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('inventories.read'), 403);

        $lowStock = null;
        if ($request->filled('low_stock')) {
            $lowStock = filter_var($request->get('low_stock'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $inventories = $this->inventoryService->paginate(
            $request->string('search')->toString(),
            $request->filled('inventory_type') ? $request->string('inventory_type')->toString() : null,
            $lowStock,
            (int) $request->integer('per_page', 10)
        );

        return $this->successResponse([
            'items' => InventoryResource::collection($inventories->items()),
            'pagination' => [
                'current_page' => $inventories->currentPage(),
                'last_page' => $inventories->lastPage(),
                'per_page' => $inventories->perPage(),
                'total' => $inventories->total(),
            ],
        ], 'Listado de inventario.');
    }

    public function show(Inventory $inventory, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('inventories.read'), 403);

        $inventory->load(['product', 'rawMaterial']);

        return $this->successResponse(
            new InventoryResource($inventory),
            'Detalle de inventario.'
        );
    }

    public function movements(Inventory $inventory, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('inventory_movements.read'), 403);

        $movements = $this->inventoryService->movements(
            (int) $inventory->getKey(),
            (int) $request->integer('per_page', 10)
        );

        return $this->successResponse([
            'items' => InventoryMovementResource::collection($movements->items()),
            'pagination' => [
                'current_page' => $movements->currentPage(),
                'last_page' => $movements->lastPage(),
                'per_page' => $movements->perPage(),
                'total' => $movements->total(),
            ],
        ], 'Movimientos de inventario.');
    }

    public function adjust(AdjustInventoryRequest $request, Inventory $inventory): JsonResponse
{
    try {
        $inventory = $this->inventoryService->adjustStock(
            $inventory,
            $request->string('movement_type')->toString(),
            (float) $request->input('quantity'),
            $request->input('description'),
            $request->user()?->getAuthIdentifier(),
            'adjustment',
            null
        );

        return $this->successResponse(
            new InventoryResource($inventory->load(['product', 'rawMaterial'])),
            'Inventario ajustado correctamente.'
        );
    } catch (RuntimeException $e) {
        return $this->errorResponse($e->getMessage(), 422);
    }
}
}
