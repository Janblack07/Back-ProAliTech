<?php

namespace App\Http\Controllers\Api\V1\Purchase;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class PurchaseController extends Controller
{
    public function __construct(
        protected PurchaseService $purchaseService
    ) {}

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('purchases.read'), 403);

        $purchases = $this->purchaseService->paginate(
            $request->string('search')->toString(),
            $request->filled('status') ? $request->string('status')->toString() : null,
            (int) $request->integer('per_page', 10)
        );

        return $this->successResponse([
            'items' => PurchaseResource::collection($purchases->items()),
            'pagination' => [
                'current_page' => $purchases->currentPage(),
                'last_page' => $purchases->lastPage(),
                'per_page' => $purchases->perPage(),
                'total' => $purchases->total(),
            ],
        ], 'Listado de compras.');
    }

    public function store(StorePurchaseRequest $request): JsonResponse
    {
        try {
            $purchase = $this->purchaseService->create(
                $request->validated(),
                (int) $request->user()->getAuthIdentifier()
            );

            return $this->successResponse(
                new PurchaseResource($purchase),
                'Compra registrada correctamente.',
                201
            );
        } catch (RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function show(Purchase $purchase, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('purchases.read'), 403);

        return $this->successResponse(
            new PurchaseResource($this->purchaseService->show($purchase)),
            'Detalle de compra.'
        );
    }
}
