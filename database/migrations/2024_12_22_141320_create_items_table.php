<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID sebagai primary key
            $table->uuid('category_id')->nullable(); // Foreign key untuk kategori
            $table->string('sku')->unique()->nullable(); // SKU barang (opsional)
            $table->string('name'); // Nama barang
            $table->string('unit'); // Satuan barang (pcs, kg, liter, dll)
            $table->text('description')->nullable(); // Deskripsi barang
            $table->integer('price'); // Harga untuk outlet pertama
            $table->integer('current_stock')->default(0); // Stok saat ini
            $table->integer('min_stock')->default(0); // Stok minimum (opsional)
            $table->integer('max_stock')->default(0); // Stok maksimum (opsional)
            $table->timestamps(); // created_at dan updated_at
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
