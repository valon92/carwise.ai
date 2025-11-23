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
        Schema::create('latest_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('manufacturer'); // Mercedes, BMW, Audi, etj.
            $table->string('model');
            $table->integer('year');
            $table->string('name'); // Emri i plotë i makinës
            $table->text('description')->nullable();
            $table->string('image_url'); // URL e fotos kryesore
            $table->json('gallery_images')->nullable(); // Array me foto shtesë
            $table->decimal('price', 12, 2)->nullable(); // Çmimi nëse është për shitje
            $table->string('currency', 3)->default('EUR');
            
            // Specifikat teknike
            $table->string('engine_type')->nullable(); // Petrol, Diesel, Electric, Hybrid
            $table->string('engine_size')->nullable(); // 2.0L, 3.0L, etj.
            $table->integer('horsepower')->nullable();
            $table->integer('torque')->nullable();
            $table->string('transmission')->nullable(); // Manual, Automatic, CVT
            $table->string('drivetrain')->nullable(); // FWD, RWD, AWD, 4WD
            $table->integer('seats')->nullable();
            $table->integer('doors')->nullable();
            $table->string('fuel_type')->nullable();
            $table->decimal('fuel_consumption', 5, 2)->nullable(); // L/100km
            $table->integer('co2_emissions')->nullable(); // g/km
            $table->string('body_type')->nullable(); // Sedan, SUV, Coupe, etj.
            $table->json('features')->nullable(); // Array me features
            $table->json('specifications')->nullable(); // Specifikat e detajuara
            
            // Informacione shtesë
            $table->string('status')->default('available'); // available, coming_soon, sold
            $table->boolean('is_featured')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('order')->default(0); // Për renditjen në carousel
            $table->timestamp('released_at')->nullable(); // Data e lëshimit në treg
            $table->timestamps();
            
            // Indexes
            $table->index('manufacturer');
            $table->index('status');
            $table->index('is_featured');
            $table->index('released_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('latest_vehicles');
    }
};
