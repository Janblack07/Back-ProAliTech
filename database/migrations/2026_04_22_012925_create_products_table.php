<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('unit_measure', 30);
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->decimal('minimum_stock', 12, 2)->default(0);
            $table->integer('shelf_life_days')->nullable();

            $table->string('image_url', 500)->nullable();
            $table->string('image_public_id', 255)->nullable();

            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['name', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
