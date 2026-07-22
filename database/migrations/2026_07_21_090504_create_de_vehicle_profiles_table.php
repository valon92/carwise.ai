<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('de_vehicle_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->char('vin', 17);
            $table->string('license_plate')->nullable();
            $table->string('nickname')->nullable();
            $table->unsignedInteger('current_mileage')->nullable();
            $table->unsignedBigInteger('legacy_car_id')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->smallInteger('year')->nullable();
            $table->string('engine')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('transmission')->nullable();
            $table->smallInteger('horsepower')->nullable();
            $table->json('factory_equipment')->nullable();
            $table->json('vehicle_options')->nullable();
            $table->unsignedBigInteger('last_vin_decode_id')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->unique(['user_id', 'vin']);
            $table->index('vin');
            $table->index('legacy_car_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('de_vehicle_profiles');
    }
};
