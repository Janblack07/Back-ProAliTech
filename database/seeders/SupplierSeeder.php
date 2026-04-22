<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'business_name' => 'Distribuidora Andina',
                'ruc' => '0999999999001',
                'contact_name' => 'Juan Pérez',
                'phone' => '0999999999',
                'email' => 'andina@test.com',
                'address' => 'Manta, Ecuador',
                'status' => true,
            ],
            [
                'business_name' => 'Insumos del Litoral',
                'ruc' => '0999999999002',
                'contact_name' => 'María Zambrano',
                'phone' => '0988888888',
                'email' => 'litoral@test.com',
                'address' => 'Portoviejo, Ecuador',
                'status' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(
                ['ruc' => $supplier['ruc']],
                $supplier
            );
        }
    }
}
