<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grn_items', function (Blueprint $table) {
            $table->id('grn_item_id');
            $table->foreignId('grn_id')->constrained('grns', 'grn_id');
            $table->foreignId('item_id')->nullable()->constrained('items');
            $table->foreignId('batch_id')->nullable()->constrained('batches');
            $table->string('vendor_item_code')->nullable();
            $table->integer('received_qty')->default(0);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('vat', 5, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->integer('stored_qty')->default(0);
            $table->text('notes')->nullable();
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grn_items');
    }
};