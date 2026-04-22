<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function __construct(
        protected CloudinaryService $cloudinaryService
    ) {}

    public function paginate(?string $search = null, ?int $categoryId = null, ?bool $status = null, int $perPage = 10)
    {
        return Product::query()
            ->with('category')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when(!is_null($status), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function activeList()
    {
        return Product::query()
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'sale_price', 'unit_measure']);
    }

    public function create(array $data): Product
    {
        if (isset($data['image'])) {
            $upload = $this->cloudinaryService->uploadImage($data['image'], 'alimenticios/products');
            $data['image_url'] = $upload['secure_url'];
            $data['image_public_id'] = $upload['public_id'];
        }

        unset($data['image']);
        $data['status'] = $data['status'] ?? true;

        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        if (isset($data['image'])) {
            $this->cloudinaryService->deleteImage($product->image_public_id);

            $upload = $this->cloudinaryService->uploadImage($data['image'], 'alimenticios/products');
            $data['image_url'] = $upload['secure_url'];
            $data['image_public_id'] = $upload['public_id'];
        }

        unset($data['image']);

        $product->update($data);

        return $product->refresh();
    }

    public function delete(Product $product): void
    {
        $this->cloudinaryService->deleteImage($product->image_public_id);
        $product->delete();
    }
}
