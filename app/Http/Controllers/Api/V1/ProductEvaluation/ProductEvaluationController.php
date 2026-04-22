<?php

namespace App\Http\Controllers\Api\V1\ProductEvaluation;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\ProductEvaluation\StoreProductEvaluationRequest;
use App\Http\Resources\ProductEvaluationResource;
use App\Models\ProductIdea;
use App\Services\ProductEvaluationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductEvaluationController extends Controller
{
    public function __construct(
        protected ProductEvaluationService $productEvaluationService
    ) {}

    public function store(StoreProductEvaluationRequest $request, ProductIdea $productIdea): JsonResponse
    {
        abort_unless($request->user()->can('product_evaluations.create'), 403);

        $evaluation = $this->productEvaluationService->evaluate(
            $productIdea,
            $request->validated(),
            (int) $request->user()->getAuthIdentifier()
        );

        return $this->successResponse(
            new ProductEvaluationResource($evaluation),
            'Evaluación creada correctamente.',
            201
        );
    }

    public function index(ProductIdea $productIdea, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('product_evaluations.read'), 403);

        $evaluations = $productIdea->evaluations()
            ->with('user')
            ->latest()
            ->get();

        return $this->successResponse(
            ProductEvaluationResource::collection($evaluations),
            'Listado de evaluaciones del producto.'
        );
    }
}
