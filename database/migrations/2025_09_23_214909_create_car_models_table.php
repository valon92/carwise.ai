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
        Schema::create('car_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_brand_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Model name (e.g., "Camry", "X5", "C-Class")
            $table->string('slug'); // URL-friendly version
            $table->string('generation')->nullable(); // Generation (e.g., "XV70", "F15", "W205")
            $table->integer('start_year')->nullable(); // Production start year
            $table->integer('end_year')->nullable(); // Production end year (null if still in production)
            $table->string('body_type')->nullable(); // sedan, suv, hatchback, coupe, etc.
            $table->string('segment')->nullable(); // compact, midsize, luxury, etc.
            $table->json('engine_options')->nullable(); // Available engine options
            $table->json('transmission_options')->nullable(); // Available transmission options
            $table->json('fuel_types')->nullable(); // Available fuel types
            $table->json('specifications')->nullable(); // Technical specifications
            $table->text('description')->nullable(); // Model description
            $table->string('image_url')->nullable(); // Model image URL
            $table->boolean('is_active')->default(true); // Whether model is still in production
            $table->boolean('is_popular')->default(false); // Popular models for quick access
            $table->integer('sort_order')->default(0); // For custom ordering
            $table->timestamps();

            // Indexes for better performance
            $table->index(['car_brand_id', 'is_active']);
            $table->index(['start_year', 'end_year']);
            $table->index('body_type');
            $table->index('segment');
            $table->index('is_popular');
            $table->index('sort_order');
            
            // Unique constraint for brand + model + generation
            $table->unique(['car_brand_id', 'name', 'generation'], 'unique_brand_model_generation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_models');
    }
};