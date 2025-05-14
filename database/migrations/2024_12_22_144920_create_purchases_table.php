<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Primary key
            $table->uuid('supplier_id')->nullable(); // Foreign key ke supplier
            $table->date('date');
            $table->string('reference_number')->nullable();
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('payment_type', ['cash', 'installment']); //pembayaran secara langsung atau berkala
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('paid');
            $table->timestamps();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
