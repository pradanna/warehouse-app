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
        Schema::table('outlet_expenses', function (Blueprint $table) {
            $table->uuid('cash_flow_id')->nullable()->after('outlet_id');
            $table->foreign('cash_flow_id')->references('id')->on('cash_flows')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outlet_expenses', function (Blueprint $table) {
            $table->dropForeign('outlet_expenses_cash_flow_id_foreign');
            $table->dropColumn('cash_flow_id');
        });
    }
};
