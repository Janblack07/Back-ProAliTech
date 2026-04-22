<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('recipe_name', 150);
            $table->decimal('expected_yield', 12, 2);
            $table->string('unit_measure', 30);

            $table->decimal('estimated_labor_cost', 12, 2)->default(0);
            $table->decimal('estimated_energy_cost', 12, 2)->default(0);
            $table->decimal('estimated_indirect_cost', 12, 2)->default(0);
            $table->decimal('estimated_waste_percent', 5, 2)->default(0);

            $table->text('instructions')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
