<?php

namespace App\Http\Controllers\Api\V1\Recipe;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Recipe\StoreRecipeRequest;
use App\Http\Requests\Recipe\UpdateRecipeRequest;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use App\Services\RecipeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function __construct(
        protected RecipeService $recipeService
    ) {}

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('recipes.read'), 403);

        $status = null;
        if ($request->filled('status')) {
            $status = filter_var($request->get('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $recipes = $this->recipeService->paginate(
            $request->string('search')->toString(),
            $status,
            (int) $request->integer('per_page', 10)
        );

        return $this->successResponse([
            'items' => RecipeResource::collection($recipes->items()),
            'pagination' => [
                'current_page' => $recipes->currentPage(),
                'last_page' => $recipes->lastPage(),
                'per_page' => $recipes->perPage(),
                'total' => $recipes->total(),
            ],
        ], 'Listado de recetas.');
    }

    public function store(StoreRecipeRequest $request): JsonResponse
    {
        $recipe = $this->recipeService->create($request->validated());

        return $this->successResponse(
            new RecipeResource($recipe),
            'Receta creada correctamente.',
            201
        );
    }

    public function show(Recipe $recipe, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('recipes.read'), 403);

        return $this->successResponse(
            new RecipeResource($this->recipeService->show($recipe)),
            'Detalle de receta.'
        );
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe): JsonResponse
    {
        $recipe = $this->recipeService->update($recipe, $request->validated());

        return $this->successResponse(
            new RecipeResource($recipe),
            'Receta actualizada correctamente.'
        );
    }
}
