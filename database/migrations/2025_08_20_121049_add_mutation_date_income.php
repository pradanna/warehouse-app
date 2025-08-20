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
        Schema::table('outlet_incomes', function (Blueprint $table) {
            $table->date('mutation_date')->nullable()->after('by_mutation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outlet_incomes', function (Blueprint $table) {
            $table->dropColumn('mutation_date');
        });
    }
};
