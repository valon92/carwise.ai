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
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('search_name');
            $table->string('search_query');
            $table->string('search_type')->default('car_parts');
            $table->string('search_category')->nullable();
            $table->json('search_filters')->nullable();
            $table->text('search_description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_favorite')->default(false);
            $table->json('tags')->nullable();
            $table->boolean('notification_enabled')->default(false);
            $table->string('notification_frequency')->default('weekly'); // daily, weekly, monthly, instant
            $table->timestamp('last_searched_at')->nullable();
            $table->integer('search_count')->default(0);
            $table->integer('results_count')->default(0);
            $table->decimal('average_duration', 8, 3)->nullable();
            $table->decimal('success_rate', 5, 2)->default(0.00);
            $table->string('search_source')->default('web');
            $table->string('search_context')->nullable();
            $table->json('additional_data')->nullable();
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['search_name']);
            $table->index(['search_query']);
            $table->index(['search_type']);
            $table->index(['search_category']);
            $table->index(['is_public']);
            $table->index(['is_favorite']);
            $table->index(['notification_enabled']);
            $table->index(['last_searched_at']);
            $table->index(['search_count']);
            $table->index(['success_rate']);
            $table->index(['search_source']);
            
            // Unique constraint to prevent duplicate saved searches
            $table->unique(['user_id', 'search_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};