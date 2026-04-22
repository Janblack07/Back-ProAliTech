<?php

namespace App\Http\Controllers\Api\V1\Sale;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class SaleController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {}

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('sales.read'), 403);

        $sales = $this->saleService->paginate(
            $request->string('search')->toString(),
            $request->filled('status') ? $request->string('status')->toString() : null,
            (int) $request->integer('per_page', 10)
        );

        return $this->successResponse([
            'items' => SaleResource::collection($sales->items()),
            'pagination' => [
                'current_page' => $sales->currentPage(),
                'last_page' => $sales->lastPage(),
                'per_page' => $sales->perPage(),
                'total' => $sales->total(),
            ],
        ], 'Listado de ventas.');
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        try {
            $sale = $this->saleService->create(
                $request->validated(),
                (int) $request->user()->getAuthIdentifier()
            );

            return $this->successResponse(
                new SaleResource($sale),
                'Venta registrada correctamente.',
                201
            );
        } catch (RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function show(Sale $sale, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('sales.read'), 403);

        return $this->successResponse(
            new SaleResource($this->saleService->show($sale)),
            'Detalle de venta.'
        );
    }
}
