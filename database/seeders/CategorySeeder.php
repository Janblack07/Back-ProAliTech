<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Lácteos', 'description' => 'Productos derivados de la leche', 'status' => true],
            ['name' => 'Embutidos', 'description' => 'Productos cárnicos procesados', 'status' => true],
            ['name' => 'Quesos', 'description' => 'Variedades de queso', 'status' => true],
            ['name' => 'Yogures', 'description' => 'Productos fermentados', 'status' => true],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
