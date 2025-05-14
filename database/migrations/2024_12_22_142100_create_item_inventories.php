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
        Schema::create('inventories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('item_id')->nullable();
            $table->uuid('unit_id')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->text('description')->nullable();
            $table->integer('price')->default(0);
            $table->decimal('current_stock', 15, 2);
            $table->decimal('min_stock', 15, 2);
            $table->decimal('max_stock', 15, 2);
            $table->timestamps();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
