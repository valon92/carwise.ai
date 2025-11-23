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
        Schema::create('mechanic_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mechanic_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('diagnosis_session_id')->nullable()->constrained()->onDelete('set null');
            
            // Review content
            $table->text('review_text');
            $table->unsignedTinyInteger('rating'); // 1-5 stars
            $table->json('rating_breakdown')->nullable(); // Detailed ratings for different aspects
            
            // Review metadata
            $table->string('service_type')->nullable(); // e.g., 'diagnosis', 'repair', 'maintenance'
            $table->decimal('service_cost', 10, 2)->nullable();
            $table->date('service_date')->nullable();
            $table->json('photos')->nullable(); // Review photos
            
            // Review status
            $table->enum('status', ['pending', 'approved', 'rejected', 'flagged'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Helpfulness tracking
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);
            
            // Response from mechanic
            $table->text('mechanic_response')->nullable();
            $table->timestamp('mechanic_response_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['mechanic_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index(['rating', 'status']);
            $table->index(['service_date']);
            $table->index(['approved_at']);
            
            // Unique constraint to prevent duplicate reviews from same user for same mechanic
            $table->unique(['mechanic_id', 'user_id', 'diagnosis_session_id'], 'unique_mechanic_user_session_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mechanic_reviews');
    }
};