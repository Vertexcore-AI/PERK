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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('vendor_id');
            $table->string('batch_no')->nullable();
            $table->decimal('unit_cost', 10, 2);
            $table->integer('received_qty');
            $table->integer('remaining_qty');
            $table->date('received_date');
            $table->date('expiry_date')->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('vat_percent', 5, 2)->default(0);
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('restrict');
            $table->index(['item_id', 'vendor_id']);
            $table->index('batch_no');
            $table->index('received_date');
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
