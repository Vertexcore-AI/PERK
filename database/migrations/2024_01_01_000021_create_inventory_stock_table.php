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
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('bin_id')->nullable()->constrained('bins')->onDelete('set null');
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null');
            $table->integer('quantity')->default(0);
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();

            $table->index('item_id');
            $table->index('store_id');
            $table->index('bin_id');
            $table->index('batch_id');
            $table->index('last_updated');
            $table->unique(['item_id', 'store_id', 'bin_id', 'batch_id']);
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