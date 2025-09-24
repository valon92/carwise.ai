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
        Schema::create('car_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Brand name (e.g., "Toyota", "BMW", "Mercedes-Benz")
            $table->string('slug')->unique(); // URL-friendly version
            $table->string('country'); // Country of origin
            $table->string('logo_url')->nullable(); // Brand logo URL
            $table->string('website')->nullable(); // Official website
            $table->text('description')->nullable(); // Brand description
            $table->integer('founded_year')->nullable(); // Year founded
            $table->string('headquarters')->nullable(); // Headquarters location
            $table->json('specialties')->nullable(); // Brand specialties (e.g., ["luxury", "sports", "electric"])
            $table->boolean('is_active')->default(true); // Whether brand is still active
            $table->boolean('is_popular')->default(false); // Popular brands for quick access
            $table->integer('sort_order')->default(0); // For custom ordering
            $table->timestamps();

            // Indexes for better performance
            $table->index(['is_active', 'is_popular']);
            $table->index('country');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_brands');
    }
};