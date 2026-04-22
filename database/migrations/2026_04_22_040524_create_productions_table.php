<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->foreignId('recipe_id')
                ->nullable()
                ->constrained('recipes')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('batch_number', 100)->unique();
            $table->date('production_date');

            $table->decimal('expected_quantity', 12, 2);
            $table->decimal('produced_quantity', 12, 2);
            $table->string('unit_measure', 30);

            $table->decimal('labor_cost', 12, 2)->default(0);
            $table->decimal('energy_cost', 12, 2)->default(0);
            $table->decimal('indirect_cost', 12, 2)->default(0);
            $table->decimal('waste_quantity', 12, 2)->default(0);

            $table->decimal('total_cost', 12, 2)->default(0);
            $table->decimal('unit_cost', 12, 2)->default(0);

            $table->text('notes')->nullable();
            $table->string('status', 30)->default('completed');
            $table->timestamps();

            $table->index(['production_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
