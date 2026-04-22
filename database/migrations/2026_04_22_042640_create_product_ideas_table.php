<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_ideas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('idea_name', 150);
            $table->text('description')->nullable();

            $table->decimal('proposed_sale_price', 12, 2)->nullable();
            $table->string('expected_demand', 30)->nullable();
            $table->string('competition_level', 30)->nullable();

            $table->decimal('estimated_labor_cost', 12, 2)->default(0);
            $table->decimal('estimated_energy_cost', 12, 2)->default(0);
            $table->decimal('estimated_indirect_cost', 12, 2)->default(0);
            $table->decimal('estimated_waste_percent', 5, 2)->default(0);

            $table->text('observations')->nullable();
            $table->string('status', 30)->default('draft');

            $table->timestamps();

            $table->index(['category_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_ideas');
    }
};
