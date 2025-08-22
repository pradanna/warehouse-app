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
        Schema::create('outlet_purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sale_id')->nullable();
            $table->uuid('cash_flow_id')->nullable();
            $table->uuid('outlet_id')->nullable();
            $table->date('date');
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
            $table->foreign('cash_flow_id')->references('id')->on('cash_flows')->onDelete('set null');
            $table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_purchases');
    }
};
