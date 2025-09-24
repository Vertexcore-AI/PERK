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
            $table->id('sale_id');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->date('sale_date');
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method', 20)->default('cash');
            $table->decimal('cash_amount', 10, 2)->default(0);
            $table->decimal('card_amount', 10, 2)->default(0);
            $table->string('status', 20)->default('completed');
            $table->text('notes')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('vat_percentage', 5, 2)->default(0);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();

            $table->index('customer_id');
            $table->index('sale_date');
            $table->index('status');
            $table->index('payment_method');
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