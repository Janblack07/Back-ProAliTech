<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_evaluations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_idea_id')
                ->constrained('product_ideas')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->decimal('estimated_total_cost', 12, 2);
            $table->decimal('estimated_unit_cost', 12, 2);
            $table->decimal('proposed_sale_price', 12, 2);
            $table->decimal('estimated_profit', 12, 2);
            $table->decimal('estimated_margin_percent', 5, 2);
            $table->decimal('break_even_quantity', 12, 2)->nullable();

            $table->string('viability_result', 30);
            $table->text('recommendation')->nullable();
            $table->date('evaluation_date');

            $table->timestamps();

            $table->index(['evaluation_date', 'viability_result']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_evaluations');
    }
};
