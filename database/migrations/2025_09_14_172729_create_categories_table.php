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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_category_id')->nullable();
            $table->integer('reorder_point')->default(10);
            $table->decimal('markup_percentage', 5, 2)->default(30.00);
            $table->boolean('is_active')->default(true);
            $table->boolean('track_serial')->default(false);
            $table->timestamps();

            $table->foreign('parent_category_id')->references('id')->on('categories')->onDelete('set null');
            $table->index('name');
            $table->index('parent_category_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
