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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('brand', 100);
            $table->string('model', 100);
            $table->year('year');
            $table->string('vin', 17)->nullable()->unique();
            $table->string('license_plate', 20)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('fuel_type', 20)->nullable(); // gasoline, diesel, electric, hybrid
            $table->string('transmission', 20)->nullable(); // manual, automatic, cvt
            $table->integer('mileage')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->json('specifications')->nullable(); // engine, horsepower, etc.
            $table->json('maintenance_history')->nullable();
            $table->string('status', 20)->default('active'); // active, sold, scrapped
            $table->timestamps();

            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['brand', 'model']);
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};