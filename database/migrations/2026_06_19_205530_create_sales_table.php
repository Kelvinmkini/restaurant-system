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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('guests')->default(0);
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->decimal('market_purchases', 12, 2)->default(0);
            $table->decimal('other_expenses', 12, 2)->default(0);
            $table->decimal('gross_profit', 12, 2)->default(0);
            $table->decimal('net_profit', 12, 2)->default(0);
            $table->date('sale_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
