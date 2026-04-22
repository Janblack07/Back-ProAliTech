<?php

namespace App\Http\Controllers\Api\V1\ProductIdea;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\ProductIdea\StoreProductIdeaRequest;
use App\Http\Requests\ProductIdea\UpdateProductIdeaRequest;
use App\Http\Resources\ProductIdeaResource;
use App\Models\ProductIdea;
use App\Services\ProductIdeaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductIdeaController extends Controller
{
    public function __construct(
        protected ProductIdeaService $productIdeaService
    ) {}

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('product_ideas.read'), 403);

        $ideas = $this->productIdeaService->paginate(
            $request->string('search')->toString(),
            $request->filled('status') ? $request->string('status')->toString() : null,
            (int) $request->integer('per_page', 10)
        );

        return $this->successResponse([
            'items' => ProductIdeaResource::collection($ideas->items()),
            'pagination' => [
                'current_page' => $ideas->currentPage(),
                'last_page' => $ideas->lastPage(),
                'per_page' => $ideas->perPage(),
                'total' => $ideas->total(),
            ],
        ], 'Listado de ideas de producto.');
    }

    public function store(StoreProductIdeaRequest $request): JsonResponse
    {
        $idea = $this->productIdeaService->create(
            $request->validated(),
            (int) $request->user()->getAuthIdentifier()
        );

        return $this->successResponse(
            new ProductIdeaResource($idea),
            'Idea de producto creada correctamente.',
            201
        );
    }

    public function show(ProductIdea $productIdea, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('product_ideas.read'), 403);

        return $this->successResponse(
            new ProductIdeaResource($this->productIdeaService->show($productIdea)),
            'Detalle de idea de producto.'
        );
    }

    public function update(UpdateProductIdeaRequest $request, ProductIdea $productIdea): JsonResponse
    {
        $idea = $this->productIdeaService->update($productIdea, $request->validated());

        return $this->successResponse(
            new ProductIdeaResource($idea),
            'Idea de producto actualizada correctamente.'
        );
    }
}
