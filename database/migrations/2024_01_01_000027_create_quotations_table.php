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
            $table->date('quote_date');
            $table->date('valid_until');
            $table->decimal('total_estimate', 10, 2)->default(0);
            $table->string('status', 20)->default('Pending');
            $table->text('car_model')->nullable();
            $table->text('car_registration_number')->nullable();
            $table->text('manual_customer_name')->nullable();
            $table->text('manual_customer_address')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->timestamps();

            $table->index('quote_date');
            $table->index('valid_until');
            $table->index('status');
            $table->index('customer_id');
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