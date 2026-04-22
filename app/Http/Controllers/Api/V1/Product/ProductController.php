<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}
    public function active(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('products.read'), 403);

        return $this->successResponse(
            $this->productService->activeList(),
            'Listado de productos activos.'
        );
    }

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('products.read'), 403);

        $status = null;
        if ($request->filled('status')) {
            $status = filter_var($request->get('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $products = $this->productService->paginate(
            $request->string('search')->toString(),
            $status,
            $request->filled('category_id') ? (int) $request->integer('category_id') : null,
            (int) $request->integer('per_page', 10)
        );

        return $this->successResponse([
            'items' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ], 'Listado de productos.');
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return $this->successResponse(
            new ProductResource($product->load('category')),
            'Producto creado correctamente.',
            201
        );
    }

    public function show(Product $product, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('products.read'), 403);

        return $this->successResponse(
            new ProductResource($product->load('category')),
            'Detalle de producto.'
        );
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService->update($product, $request->validated());

        return $this->successResponse(
            new ProductResource($product->load('category')),
            'Producto actualizado correctamente.'
        );
    }

    public function destroy(Product $product, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('products.delete'), 403);

        $this->productService->delete($product);

        return $this->successResponse(null, 'Producto eliminado correctamente.');
    }
}
