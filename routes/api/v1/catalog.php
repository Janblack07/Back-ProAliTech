<?php

use App\Http\Controllers\Api\V1\Auth\AccessControlController;
use App\Http\Controllers\Api\V1\Category\CategoryController;
use App\Http\Controllers\Api\V1\Inventory\InventoryController;
use App\Http\Controllers\Api\V1\Product\ProductController;
use App\Http\Controllers\Api\V1\RawMaterial\RawMaterialController;
use App\Http\Controllers\Api\V1\Supplier\SupplierController;
use App\Http\Controllers\Api\V1\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Purchase\PurchaseController;
use App\Http\Controllers\Api\V1\Sale\SaleController;
use App\Http\Controllers\Api\V1\Production\ProductionController;
use App\Http\Controllers\Api\V1\Recipe\RecipeController;

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

    Route::get('inventories', [InventoryController::class, 'index']);
    Route::get('inventories/{inventory}', [InventoryController::class, 'show']);
    Route::get('inventories/{inventory}/movements', [InventoryController::class, 'movements']);
    Route::post('inventories/{inventory}/adjust', [InventoryController::class, 'adjust']);

    Route::get('purchases', [PurchaseController::class, 'index']);
    Route::post('purchases', [PurchaseController::class, 'store']);
    Route::get('purchases/{purchase}', [PurchaseController::class, 'show']);

    Route::get('sales', [SaleController::class, 'index']);
    Route::post('sales', [SaleController::class, 'store']);
    Route::get('sales/{sale}', [SaleController::class, 'show']);

    Route::get('recipes', [RecipeController::class, 'index']);
    Route::post('recipes', [RecipeController::class, 'store']);
    Route::get('recipes/{recipe}', [RecipeController::class, 'show']);
    Route::put('recipes/{recipe}', [RecipeController::class, 'update']);

    Route::get('productions', [ProductionController::class, 'index']);
    Route::post('productions', [ProductionController::class, 'store']);
    Route::get('productions/{production}', [ProductionController::class, 'show']);
});
