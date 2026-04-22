<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function paginate(?string $search = null, ?bool $status = null, int $perPage = 10)
    {
        return User::query()
            ->with('roles')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(!is_null($status), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $roles = $data['roles'] ?? [];
            unset($data['roles']);

            $data['password'] = Hash::make($data['password']);
            $data['status'] = $data['status'] ?? true;

            $user = User::create($data);
            $user->syncRoles($roles);

            return $user->load('roles');
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $roles = $data['roles'] ?? [];
            unset($data['roles']);

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);
            $user->syncRoles($roles);

            return $user->load('roles');
        });
    }

    public function delete(User $user): void
    {
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            abort(422, 'No puedes eliminar el único usuario administrador.');
        }

        $user->tokens()->delete();
        $user->delete();
    }
}
