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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('part_id')->nullable();
            $table->string('part_name');
            $table->string('part_brand')->nullable();
            $table->string('part_number')->nullable();
            $table->text('part_description')->nullable();
            $table->string('part_image_url')->nullable();
            $table->string('part_category')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('source')->default('carwise');
            $table->string('affiliate_url')->nullable();
            $table->json('tracking_data')->nullable();
            $table->timestamps();

            $table->index(['order_id']);
            $table->index(['part_id']);
            $table->index(['part_name']);
            $table->index(['part_brand']);
            $table->index(['part_category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};