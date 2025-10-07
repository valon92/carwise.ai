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
        Schema::create('wishlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('part_id')->nullable();
            $table->string('part_name');
            $table->string('part_brand')->nullable();
            $table->string('part_number')->nullable();
            $table->text('part_description')->nullable();
            $table->string('part_image_url')->nullable();
            $table->string('part_category')->nullable();
            $table->decimal('part_price', 10, 2);
            $table->string('part_currency', 3)->default('USD');
            $table->string('source')->default('carwise');
            $table->string('affiliate_url')->nullable();
            $table->text('notes')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('notification_enabled')->default(false);
            $table->decimal('price_alert_threshold', 10, 2)->nullable();
            $table->timestamp('added_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['part_id']);
            $table->index(['part_name']);
            $table->index(['part_brand']);
            $table->index(['part_category']);
            $table->index(['priority']);
            $table->index(['added_at']);
            $table->unique(['user_id', 'part_id', 'part_name']); // Prevent duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist');
    }
};