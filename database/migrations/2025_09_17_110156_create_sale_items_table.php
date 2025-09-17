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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id('sale_item_id');
            $table->foreignId('sale_id')->constrained('sales', 'sale_id')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items', 'item_id');
            $table->foreignId('batch_id')->constrained('batches', 'batch_id');
            $table->integer('quantity')->unsigned();
            $table->decimal('unit_price', 10, 2); // Selling price from batch
            $table->decimal('unit_cost', 10, 2);  // Cost price from batch for profit tracking
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('vat', 5, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();

            $table->index(['sale_id', 'item_id']);
            $table->index('batch_id');

            // Note: SQLite check constraints will be handled at application level
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
