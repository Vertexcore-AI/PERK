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
        Schema::create('grn_items', function (Blueprint $table) {
            $table->id('grn_item_id');
            $table->foreignId('grn_id')->constrained('grns', 'grn_id')->onDelete('cascade');
            $table->foreignId('item_id')->nullable()->constrained('items', 'id');
            $table->foreignId('batch_id')->nullable()->constrained('batches', 'id');
            $table->string('vendor_item_code')->nullable();
            $table->integer('received_qty')->default(0);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('vat', 5, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->integer('stored_qty')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add check constraints
            $table->index('grn_id');
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grn_items');
    }
};