<?php

namespace App\Services;

use App\Models\RawMaterial;

class RawMaterialService
{
    public function __construct(
        protected CloudinaryService $cloudinaryService
    ) {}

    public function paginate(?string $search = null, ?string $type = null, ?bool $status = null, int $perPage = 10)
    {
        return RawMaterial::query()
            ->with('supplier')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($type, fn ($q) => $q->where('material_type', $type))
            ->when(!is_null($status), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function activeList()
    {
        return RawMaterial::query()
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'unit_measure']);
    }

    public function create(array $data): RawMaterial
    {
        if (isset($data['image'])) {
            $upload = $this->cloudinaryService->uploadImage($data['image'], 'alimenticios/raw-materials');
            $data['image_url'] = $upload['secure_url'];
            $data['image_public_id'] = $upload['public_id'];
        }

        unset($data['image']);
        $data['status'] = $data['status'] ?? true;

        return RawMaterial::create($data);
    }

    public function update(RawMaterial $rawMaterial, array $data): RawMaterial
    {
        if (isset($data['image'])) {
            $this->cloudinaryService->deleteImage($rawMaterial->image_public_id);

            $upload = $this->cloudinaryService->uploadImage($data['image'], 'alimenticios/raw-materials');
            $data['image_url'] = $upload['secure_url'];
            $data['image_public_id'] = $upload['public_id'];
        }

        unset($data['image']);

        $rawMaterial->update($data);

        return $rawMaterial->refresh();
    }

    public function delete(RawMaterial $rawMaterial): void
    {
        $this->cloudinaryService->deleteImage($rawMaterial->image_public_id);
        $rawMaterial->delete();
    }
}
