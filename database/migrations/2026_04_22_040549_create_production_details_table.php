<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('production_id')
                ->constrained('productions')
                ->cascadeOnDelete();

            $table->foreignId('raw_material_id')
                ->constrained('raw_materials')
                ->restrictOnDelete();

            $table->decimal('quantity_used', 12, 2);
            $table->string('unit_measure', 30);
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('total_cost', 12, 2);

            $table->string('batch_number', 100)->nullable();
            $table->date('expiration_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_details');
    }
};
