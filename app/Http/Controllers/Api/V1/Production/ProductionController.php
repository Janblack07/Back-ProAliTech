<?php

namespace App\Http\Controllers\Api\V1\Production;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Production\StoreProductionRequest;
use App\Http\Resources\ProductionResource;
use App\Models\Production;
use App\Services\ProductionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class ProductionController extends Controller
{
    public function __construct(
        protected ProductionService $productionService
    ) {}

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('productions.read'), 403);

        $productions = $this->productionService->paginate(
            $request->string('search')->toString(),
            $request->filled('status') ? $request->string('status')->toString() : null,
            (int) $request->integer('per_page', 10)
        );

        return $this->successResponse([
            'items' => ProductionResource::collection($productions->items()),
            'pagination' => [
                'current_page' => $productions->currentPage(),
                'last_page' => $productions->lastPage(),
                'per_page' => $productions->perPage(),
                'total' => $productions->total(),
            ],
        ], 'Listado de producciones.');
    }

    public function store(StoreProductionRequest $request): JsonResponse
    {
        try {
            $production = $this->productionService->create(
                $request->validated(),
                (int) $request->user()->getAuthIdentifier()
            );

            return $this->successResponse(
                new ProductionResource($production),
                'Producción registrada correctamente.',
                201
            );
        } catch (RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function show(Production $production, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('productions.read'), 403);

        return $this->successResponse(
            new ProductionResource($this->productionService->show($production)),
            'Detalle de producción.'
        );
    }
}
