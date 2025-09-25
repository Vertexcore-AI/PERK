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
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('batch_no')->nullable();
            $table->decimal('unit_cost', 10, 2);
            $table->integer('received_qty');
            $table->integer('remaining_qty');
            $table->date('received_date');
            $table->date('expiry_date')->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('vat_percent', 5, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('item_id');
            $table->index('vendor_id');
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