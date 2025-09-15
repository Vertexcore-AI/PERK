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
            $table->unsignedBigInteger('customer_id'); 
            $table->date('quote_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->decimal('total_estimate', 10, 2)->default(0);
            $table->string('status')->default('Pending');
             $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
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
