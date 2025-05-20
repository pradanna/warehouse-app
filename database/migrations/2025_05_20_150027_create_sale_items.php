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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sale_id')->nullable();
            $table->uuid('inventory_id'); // Foreign key ke items
            $table->decimal('quantity', 15, 2)->default(0); // Jumlah yang dibeli
            $table->decimal('price', 15, 2)->default(0); // Harga per item dalam satuan terkecil (misal: sen)
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
