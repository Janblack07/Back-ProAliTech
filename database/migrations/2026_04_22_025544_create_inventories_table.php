<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();

            $table->string('inventory_type', 30); // product | raw_material

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('raw_material_id')
                ->nullable()
                ->constrained('raw_materials')
                ->cascadeOnDelete();

            $table->decimal('current_stock', 12, 2)->default(0);
            $table->string('unit_measure', 30);
            $table->decimal('minimum_stock', 12, 2)->default(0);
            $table->timestamp('last_movement_at')->nullable();
            $table->timestamps();

            $table->index('inventory_type');
            $table->index('product_id');
            $table->index('raw_material_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
