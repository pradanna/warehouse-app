<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Primary key
            $table->uuid('purchase_id'); // Foreign key ke purchases
            $table->uuid('item_id'); // Foreign key ke items
            $table->decimal('quantity', 15, 2)->default(0); // Jumlah yang dibeli
            $table->decimal('price', 15, 2)->default(0); // Harga per item dalam satuan terkecil (misal: sen)
            $table->decimal('total', 15, 2)->default(0); // Total harga (quantity * price)
            $table->timestamps();

            // Definisikan foreign key
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
