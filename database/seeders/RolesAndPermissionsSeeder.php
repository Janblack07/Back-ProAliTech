<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.read',

            'users.read',
            'users.create',
            'users.update',
            'users.delete',

            'roles.read',

            'categories.read',
            'categories.create',
            'categories.update',
            'categories.delete',

            'suppliers.read',
            'suppliers.create',
            'suppliers.update',
            'suppliers.delete',

            'raw_materials.read',
            'raw_materials.create',
            'raw_materials.update',
            'raw_materials.delete',

            'products.read',
            'products.create',
            'products.update',
            'products.delete',

            'purchases.read',
            'purchases.create',
            'purchases.update',
            'purchases.delete',

            'inventories.read',
            'inventory_movements.read',
            'inventory_adjustments.create',

            'recipes.read',
            'recipes.create',
            'recipes.update',
            'recipes.delete',

            'productions.read',
            'productions.create',
            'productions.update',
            'productions.delete',

            'sales.read',
            'sales.create',
            'sales.update',
            'sales.delete',

            'incomes.read',
            'incomes.create',
            'incomes.update',
            'incomes.delete',

            'expenses.read',
            'expenses.create',
            'expenses.update',
            'expenses.delete',

            'product_ideas.read',
            'product_ideas.create',
            'product_ideas.update',
            'product_ideas.delete',

            'product_evaluations.read',
            'product_evaluations.create',
            'product_evaluations.update',
            'product_evaluations.delete',

            'reports.read',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managementRole = Role::firstOrCreate(['name' => 'gerencia']);
        $inventoryRole = Role::firstOrCreate(['name' => 'inventario']);
        $productionRole = Role::firstOrCreate(['name' => 'produccion']);
        $salesRole = Role::firstOrCreate(['name' => 'ventas']);

        $adminRole->syncPermissions(Permission::all());

        $managementRole->syncPermissions([
            'dashboard.read',
            'reports.read',
            'products.read',
            'categories.read',
            'raw_materials.read',
            'suppliers.read',
            'purchases.read',
            'inventories.read',
            'inventory_movements.read',
            'recipes.read',
            'productions.read',
            'sales.read',
            'incomes.read',
            'expenses.read',
            'product_ideas.read',
            'product_evaluations.read',
        ]);

        $inventoryRole->syncPermissions([
            'dashboard.read',
            'categories.read',
            'suppliers.read',
            'raw_materials.read',
            'raw_materials.create',
            'raw_materials.update',
            'products.read',
            'purchases.read',
            'purchases.create',
            'purchases.update',
            'inventories.read',
            'inventory_movements.read',
            'inventory_adjustments.create',
        ]);

        $productionRole->syncPermissions([
            'dashboard.read',
            'products.read',
            'raw_materials.read',
            'recipes.read',
            'recipes.create',
            'recipes.update',
            'productions.read',
            'productions.create',
            'productions.update',
            'inventories.read',
            'inventory_movements.read',
        ]);

        $salesRole->syncPermissions([
            'dashboard.read',
            'products.read',
            'sales.read',
            'sales.create',
            'sales.update',
            'incomes.read',
            'incomes.create',
            'inventories.read',
            'inventory_movements.read',
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@alimenticios.com'],
            [
                'name' => 'Administrador',
                'lastname' => 'General',
                'phone' => '0999999999',
                'password' => Hash::make('Admin12345*'),
                'status' => true,
            ]
        );

        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}