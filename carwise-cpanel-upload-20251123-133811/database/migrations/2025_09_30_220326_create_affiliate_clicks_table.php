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
        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained('car_parts')->onDelete('cascade');
            $table->string('brand');
            $table->string('category');
            $table->text('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->string('ip_address');
            $table->string('session_id');
            $table->uuid('click_id')->unique();
            $table->timestamp('timestamp');
            $table->boolean('converted')->default(false);
            $table->timestamp('conversion_date')->nullable();
            $table->timestamps();

            $table->index(['click_id']);
            $table->index(['part_id']);
            $table->index(['brand']);
            $table->index(['category']);
            $table->index(['converted']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_clicks');
    }
};