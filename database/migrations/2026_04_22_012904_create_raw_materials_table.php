<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('suppliers')
                ->nullOnDelete();

            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('material_type', 50);
            $table->string('unit_measure', 30);
            $table->decimal('cost_per_unit', 12, 2)->default(0);
            $table->decimal('minimum_stock', 12, 2)->default(0);
            $table->date('expiration_date')->nullable();

            $table->string('image_url', 500)->nullable();
            $table->string('image_public_id', 255)->nullable();

            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['name', 'material_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
