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
        Schema::create('vendor_item_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->string('vendor_item_code');
            $table->string('vendor_item_name')->nullable();
            $table->boolean('is_preferred')->default(false);
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('item_id');
            $table->index('vendor_item_code');
            $table->unique(['vendor_id', 'vendor_item_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_item_mappings');
    }
};