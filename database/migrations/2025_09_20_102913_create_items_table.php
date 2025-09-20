<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_no');
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('categories');
            $table->string('unit_of_measure')->default('piece');
            $table->integer('reorder_point')->default(10);
            $table->boolean('is_serialized')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('barcode')->nullable();
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->nullable();
            $table->text('manufacturer_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};