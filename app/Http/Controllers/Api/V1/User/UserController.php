<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('users.read'), 403);

        $status = null;
        if ($request->filled('status')) {
            $status = filter_var($request->get('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $users = $this->userService->paginate(
            $request->string('search')->toString(),
            $status,
            (int) $request->integer('per_page', 10)
        );

        $items = collect($users->items())->map(function ($user) {
            $user->permissions_list = $user->getAllPermissions()->pluck('name')->values();
            return new UserResource($user);
        });

        return $this->successResponse([
            'items' => $items,
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ], 'Listado de usuarios.');
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());
        $user->permissions_list = $user->getAllPermissions()->pluck('name')->values();

        return $this->successResponse(
            new UserResource($user),
            'Usuario creado correctamente.',
            201
        );
    }

    public function show(User $user, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('users.read'), 403);

        $user->load('roles');
        $user->permissions_list = $user->getAllPermissions()->pluck('name')->values();

        return $this->successResponse(
            new UserResource($user),
            'Detalle de usuario.'
        );
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validated());
        $user->permissions_list = $user->getAllPermissions()->pluck('name')->values();

        return $this->successResponse(
            new UserResource($user),
            'Usuario actualizado correctamente.'
        );
    }

    public function destroy(User $user, Request $request): JsonResponse
    {
        abort_unless($request->user()->can('users.delete'), 403);

        $this->userService->delete($user);

        return $this->successResponse(null, 'Usuario eliminado correctamente.');
    }
}
