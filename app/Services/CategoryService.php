<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function paginate(?string $search = null, ?bool $status = null, int $perPage = 10)
    {
        return Category::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when(!is_null($status), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function activeList()
    {
        return Category::query()
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function create(array $data): Category
    {
        $data['status'] = $data['status'] ?? true;
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->refresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
