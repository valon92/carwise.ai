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
        Schema::create('car_images', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->integer('year')->nullable();
            $table->string('body_type')->nullable(); // sedan, suv, hatchback, etc.
            $table->string('color')->nullable();
            $table->string('image_url');
            $table->string('thumbnail_url')->nullable();
            $table->string('image_type')->default('exterior'); // exterior, interior, engine, etc.
            $table->string('angle')->default('front'); // front, side, rear, 3/4, etc.
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('source')->nullable(); // manufacturer, stock photo, user upload
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // additional image data
            $table->timestamps();

            // Indexes for better performance
            $table->index(['brand', 'model']);
            $table->index(['brand', 'model', 'year']);
            $table->index(['image_type', 'angle']);
            $table->index('is_primary');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_images');
    }
};