<?php

namespace App\Http\Controllers\Api\V1\RawMaterial;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\RawMaterial\StoreRawMaterialRequest;
use App\Http\Requests\RawMaterial\UpdateRawMaterialRequest;
use App\Http\Resources\RawMaterialResource;
use App\Models\RawMaterial;
use App\Services\RawMaterialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
{
    public function __construct(
        protected RawMaterialService $rawMaterialService
    ) {}
    public function active(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('raw_materials.read'), 403);

        $status = null;
        if ($request->filled('status')) {
            $status = filter_var($request->get('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        return $this->successResponse(
            $this->rawMaterialService->activeList(),
            $status,
            'Listado de materias primas activas.'
        );
    }

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('raw_materials.read'), 403);

        $rawMaterials = $this->rawMaterialService->paginate(
            $request->string('search')->toString(),
            $request->filled('material_type') ? $request->string('material_type')->toString() : null,
            (int) $request->integer('per_page', 10)
        );

        return $this->successResponse([
            'items' => RawMaterialResource::collection($rawMaterials->items()),
            'pagination' => [
                'current_page' => $rawMaterials->currentPage(),
                'last_page' => $rawMaterials->lastPage(),
                'per_page' => $rawMaterials->perPage(),
                'total' => $rawMaterials->total(),
            ],
        ], 'Listado de materias primas.');
    }

    public function store(StoreRawMaterialRequest $request): JsonResponse
    {
        $rawMaterial = $this->rawMaterialService->create($request->validated());

        return $this->successResponse(
            new RawMaterialResource($rawMaterial->load('supplier')),
            'Materia prima creada correctamente.',
            201
        );
    }

    public function show(RawMaterial $rawMaterial, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('raw_materials.read'), 403);

        return $this->successResponse(
            new RawMaterialResource($rawMaterial->load('supplier')),
            'Detalle de materia prima.'
        );
    }

    public function update(UpdateRawMaterialRequest $request, RawMaterial $rawMaterial): JsonResponse
    {
        $rawMaterial = $this->rawMaterialService->update($rawMaterial, $request->validated());

        return $this->successResponse(
            new RawMaterialResource($rawMaterial->load('supplier')),
            'Materia prima actualizada correctamente.'
        );
    }

    public function destroy(RawMaterial $rawMaterial, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('raw_materials.delete'), 403);

        $this->rawMaterialService->delete($rawMaterial);

        return $this->successResponse(null, 'Materia prima eliminada correctamente.');
    }
}
