<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('inventory_id');
            $table->enum('type', ['in', 'out']);
            $table->decimal('quantity_open', 15, 2)->default(0);
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('quantity_close', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('movement_type', ['purchase', 'sale', 'transfer', 'adjustment', 'conversion', 'return', 'wastage']);
            $table->string('movement_reference')->nullable();
            $table->timestamps();
            $table->foreign('inventory_id')->references('id')->on('inventories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
