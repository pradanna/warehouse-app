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
        Schema::create('outlet_expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('outlet_id')->nullable();
            $table->uuid('expense_category_id')->nullable();
            $table->date('date');
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->uuid('author_id')->nullable();
            $table->timestamps();
            $table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('cascade');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_expenses');
    }
};
