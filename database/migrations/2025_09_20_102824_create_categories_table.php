<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('parent_category_id')->nullable()->constrained('categories');
            $table->integer('reorder_point')->default(10);
            $table->decimal('markup_percentage', 5, 2)->default(30);
            $table->boolean('is_active')->default(true);
            $table->boolean('track_serial')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};