<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_id')
                ->constrained('inventories')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('movement_type', 30); // entry, exit, adjustment, waste, return
            $table->string('reference_type', 30)->nullable(); // purchase, sale, production, adjustment
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->decimal('quantity', 12, 2);
            $table->decimal('stock_before', 12, 2);
            $table->decimal('stock_after', 12, 2);

            $table->timestamp('movement_date');
            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            $table->index('movement_type');
            $table->index('movement_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
