<?php

namespace App\Services;

use App\Models\Supplier;

class SupplierService
{
    public function paginate(?string $search = null, ?bool $status = null, int $perPage = 10)
    {
        return Supplier::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('business_name', 'like', "%{$search}%")
                        ->orWhere('ruc', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%");
                });
            })
            ->when(!is_null($status), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function activeList()
    {
        return Supplier::query()
            ->where('status', true)
            ->orderBy('business_name')
            ->get(['id', 'business_name']);
    }

    public function create(array $data): Supplier
    {
        $data['status'] = $data['status'] ?? true;
        return Supplier::create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);
        return $supplier->refresh();
    }

    public function delete(Supplier $supplier): void
    {
        $supplier->delete();
    }
}
