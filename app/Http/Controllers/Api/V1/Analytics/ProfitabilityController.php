<?php

namespace App\Http\Controllers\Api\V1\Analytics;

use App\Http\Controllers\Api\V1\Controller;
use App\Services\ProfitabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfitabilityController extends Controller
{
    public function __construct(
        protected ProfitabilityService $profitabilityService
    ) {}

    public function products(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('reports.read'), 403);

        $result = $this->profitabilityService->productProfitability(
            $request->string('search')->toString(),
            (int) $request->integer('per_page', 10)
        );

        return $this->successResponse([
            'items' => $result->items(),
            'pagination' => [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
            ],
        ], 'Rentabilidad de productos.');
    }
}
