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
        Schema::create('items', function (Blueprint $table) {
            $table->id('item_id'); 
            $table->string('item_no')->unique(); 
            $table->text('description');
            $table->decimal('vat', 5, 2)->default(0);
            $table->string('manufacturer_name')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories'); 
            $table->string('unit_of_measure')->default('PCS');
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->nullable();
            $table->boolean('is_serialized')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
