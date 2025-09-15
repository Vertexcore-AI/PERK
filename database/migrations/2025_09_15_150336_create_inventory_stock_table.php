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
        Schema::create('inventory_stock', function (Blueprint $table) {
            $table->id('stock_id');
            $table->foreignId('item_id')->constrained('items', 'id');
            $table->foreignId('store_id')->constrained('stores', 'id');
            $table->foreignId('bin_id')->nullable()->constrained('bins', 'id');
            $table->foreignId('batch_id')->nullable()->constrained('batches', 'id');
            $table->integer('quantity')->default(0);
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();

            // Add indexes for performance
            $table->index('item_id');
            $table->index('store_id');
            $table->index('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stock');
    }
};