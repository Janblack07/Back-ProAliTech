<?php

namespace App\Http\Controllers\Api\V1\Category;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    public function active(Request $request): JsonResponse
    {
    abort_unless($request->user()->can('categories.read'), 403);

    return $this->successResponse(
        $this->categoryService->activeList(),
        'Listado de categorías activas.'
    );
    }

    public function index(Request $request): JsonResponse
    {
    abort_unless($request->user()->can('categories.read'), 403);

    $status = null;
    if ($request->filled('status')) {
        $status = filter_var($request->get('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    $categories = $this->categoryService->paginate(
        $request->string('search')->toString(),
        $status,
        (int) $request->integer('per_page', 10)
    );

    return $this->successResponse([
        'items' => CategoryResource::collection($categories->items()),
        'pagination' => [
            'current_page' => $categories->currentPage(),
            'last_page' => $categories->lastPage(),
            'per_page' => $categories->perPage(),
            'total' => $categories->total(),
        ],
        ], 'Listado de categorías.');
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return $this->successResponse(
            new CategoryResource($category),
            'Categoría creada correctamente.',
            201
        );
    }

    public function show(Category $category, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('categories.read'), 403);

        return $this->successResponse(
            new CategoryResource($category),
            'Detalle de categoría.'
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->update($category, $request->validated());

        return $this->successResponse(
            new CategoryResource($category),
            'Categoría actualizada correctamente.'
        );
    }

    public function destroy(Category $category, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('categories.delete'), 403);

        $this->categoryService->delete($category);

        return $this->successResponse(null, 'Categoría eliminada correctamente.');
    }
}
