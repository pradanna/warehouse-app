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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->uuid('outlet_expense_id')->nullable()->after('outlet_id');
            $table->foreign('outlet_expense_id')->references('id')->on('outlet_expenses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign('payrolls_outlet_expense_id_foreign');
            $table->dropColumn('outlet_expense_id');
        });
    }
};
