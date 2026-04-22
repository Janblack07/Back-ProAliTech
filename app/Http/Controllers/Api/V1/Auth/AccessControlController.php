<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessControlController extends Controller
{
    public function roles(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('roles.read'), 403);

        $roles = Role::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return $this->successResponse($roles, 'Listado de roles.');
    }

    public function permissions(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('roles.read'), 403);

        $permissions = Permission::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return $this->successResponse($permissions, 'Listado de permisos.');
    }
}
