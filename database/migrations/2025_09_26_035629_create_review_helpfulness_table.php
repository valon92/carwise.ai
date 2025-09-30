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
        Schema::create('review_helpfulness', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('mechanic_reviews')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_helpful'); // true for helpful, false for not helpful
            $table->timestamps();
            
            // Indexes
            $table->index(['review_id', 'user_id']);
            $table->index(['user_id', 'created_at']);
            
            // Unique constraint to prevent duplicate votes
            $table->unique(['review_id', 'user_id'], 'unique_review_user_helpfulness');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_helpfulness');
    }
};