<?php

use App\Http\Controllers\Api\V1\Auth\AccessControlController;
use App\Http\Controllers\Api\V1\Category\CategoryController;
use App\Http\Controllers\Api\V1\Product\ProductController;
use App\Http\Controllers\Api\V1\RawMaterial\RawMaterialController;
use App\Http\Controllers\Api\V1\Supplier\SupplierController;
use App\Http\Controllers\Api\V1\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('roles', [AccessControlController::class, 'roles']);
    Route::get('permissions', [AccessControlController::class, 'permissions']);

    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);

    Route::get('categories/active/list', [CategoryController::class, 'active']);
    Route::apiResource('categories', CategoryController::class);

    Route::get('suppliers/active/list', [SupplierController::class, 'active']);
    Route::apiResource('suppliers', SupplierController::class);

    Route::get('raw-materials/active/list', [RawMaterialController::class, 'active']);
    Route::apiResource('raw-materials', RawMaterialController::class);

    Route::get('products/active/list', [ProductController::class, 'active']);
    Route::apiResource('products', ProductController::class);
});
