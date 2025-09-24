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
        Schema::create('grns', function (Blueprint $table) {
            $table->id('grn_id');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('inv_no');
            $table->date('billing_date')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('inv_no');
            $table->index('billing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grns');
    }
};