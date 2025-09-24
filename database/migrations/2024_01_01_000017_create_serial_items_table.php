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
        Schema::create('serial_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->string('serial_no');
            $table->string('barcode')->nullable();
            $table->string('status')->default('available');
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();

            $table->index('batch_id');
            $table->index('serial_no');
            $table->index('barcode');
            $table->index('status');
            $table->unique('serial_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serial_items');
    }
};