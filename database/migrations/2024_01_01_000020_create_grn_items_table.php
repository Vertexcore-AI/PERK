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
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('set null');
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null');
            $table->string('vendor_item_code')->nullable();
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('vat', 5, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->index('grn_id');
            $table->index('item_id');
            $table->index('batch_id');
            $table->index('vendor_item_code');
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