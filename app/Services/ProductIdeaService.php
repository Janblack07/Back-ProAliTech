<?php

namespace App\Services;

use App\Enums\ProductIdeaStatusEnum;
use App\Models\ProductIdea;

class ProductIdeaService
{
    public function paginate(?string $search = null, ?string $status = null, int $perPage = 10)
    {
        return ProductIdea::query()
            ->with(['category', 'user', 'evaluations.user'])
            ->when($search, function ($q) use ($search) {
                $q->where('idea_name', 'like', "%{$search}%")
                  ->orWhereHas('category', fn ($sub) => $sub->where('name', 'like', "%{$search}%"));
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data, int $userId): ProductIdea
    {
        $data['user_id'] = $userId;
        $data['status'] = $data['status'] ?? ProductIdeaStatusEnum::DRAFT->value;

        return ProductIdea::create($data)->load(['category', 'user', 'evaluations.user']);
    }

    public function update(ProductIdea $productIdea, array $data): ProductIdea
    {
        $productIdea->update($data);

        return $productIdea->refresh()->load(['category', 'user', 'evaluations.user']);
    }

    public function show(ProductIdea $productIdea): ProductIdea
    {
        return $productIdea->load(['category', 'user', 'evaluations.user']);
    }
}
