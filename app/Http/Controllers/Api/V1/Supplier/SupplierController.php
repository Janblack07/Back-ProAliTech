<?php

namespace App\Http\Controllers\Api\V1\Supplier;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(
        protected SupplierService $supplierService
    ) {}

    public function active(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('suppliers.read'), 403);

        return $this->successResponse(
            $this->supplierService->activeList(),
            'Listado de proveedores activos.'
        );
    }
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('suppliers.read'), 403);

        $status = null;
        if ($request->filled('status')) {
            $status = filter_var($request->get('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $suppliers = $this->supplierService->paginate(
            $request->string('search')->toString(),
            $status,
            (int) $request->integer('per_page', 10)
        );

        return $this->successResponse([
            'items' => SupplierResource::collection($suppliers->items()),
            'pagination' => [
                'current_page' => $suppliers->currentPage(),
                'last_page' => $suppliers->lastPage(),
                'per_page' => $suppliers->perPage(),
                'total' => $suppliers->total(),
            ],
        ], 'Listado de proveedores.');
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = $this->supplierService->create($request->validated());

        return $this->successResponse(
            new SupplierResource($supplier),
            'Proveedor creado correctamente.',
            201
        );
    }

    public function show(Supplier $supplier, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('suppliers.read'), 403);

        return $this->successResponse(
            new SupplierResource($supplier),
            'Detalle de proveedor.'
        );
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier = $this->supplierService->update($supplier, $request->validated());

        return $this->successResponse(
            new SupplierResource($supplier),
            'Proveedor actualizado correctamente.'
        );
    }

    public function destroy(Supplier $supplier, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('suppliers.delete'), 403);

        $this->supplierService->delete($supplier);

        return $this->successResponse(null, 'Proveedor eliminado correctamente.');
    }
}
