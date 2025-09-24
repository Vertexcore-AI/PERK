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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->string('operation');
            $table->unsignedBigInteger('record_id');
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->string('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at');

            $table->index('table_name');
            $table->index('operation');
            $table->index('record_id');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};