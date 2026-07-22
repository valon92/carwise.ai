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
        Schema::create('de_vin_decodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_profile_id')->constrained('de_vehicle_profiles')->cascadeOnDelete();
            $table->char('vin', 17);
            $table->string('provider', 40);
            $table->json('raw_response')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->smallInteger('year')->nullable();
            $table->string('engine')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('transmission')->nullable();
            $table->smallInteger('horsepower')->nullable();
            $table->json('specifications')->nullable();
            $table->json('factory_equipment')->nullable();
            $table->json('vehicle_options')->nullable();
            $table->json('service_schedule')->nullable();
            $table->json('recalls')->nullable();
            $table->json('warranty')->nullable();
            $table->timestamp('decoded_at');
            $table->timestamps();

            $table->index('vehicle_profile_id');
            $table->index('vin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('de_vin_decodes');
    }
};
