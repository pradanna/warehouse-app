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
        Schema::create('fund_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('outlet_id')->nullable();
            $table->uuid('debit_cash_flow_id')->nullable();
            $table->uuid('credit_cash_flow_id')->nullable();
            $table->date('date');
            $table->enum('transfer_to', ['cash', 'digital']);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
            $table->foreign('debit_cash_flow_id')->references('id')->on('cash_flows')->onDelete('set null');
            $table->foreign('credit_cash_flow_id')->references('id')->on('cash_flows')->onDelete('set null');
            $table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_transfers');
    }
};
