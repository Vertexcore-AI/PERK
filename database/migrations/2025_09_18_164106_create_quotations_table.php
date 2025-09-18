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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id('quote_id');
            $table->foreignId('customer_id')->nullable()->constrained('customers', 'customer_id');
            $table->date('quote_date');
            $table->date('valid_until');
            $table->decimal('total_estimate', 10, 2)->nullable();
            $table->enum('status', ['Pending', 'Expired', 'Converted'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
