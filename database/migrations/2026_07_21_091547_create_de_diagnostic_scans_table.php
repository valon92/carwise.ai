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
        Schema::create('de_diagnostic_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_profile_id')->constrained('de_vehicle_profiles')->cascadeOnDelete();
            $table->foreignId('connector_pairing_id')->nullable()->constrained('de_connector_pairings')->nullOnDelete();
            $table->timestamp('scan_date');
            $table->unsignedInteger('mileage')->nullable();
            $table->string('source', 30)->default('manual');
            $table->json('engine_dtcs')->nullable();
            $table->json('abs_errors')->nullable();
            $table->json('airbag_errors')->nullable();
            $table->json('transmission_errors')->nullable();
            $table->json('battery_health')->nullable();
            $table->json('oil_life')->nullable();
            $table->json('tire_pressure')->nullable();
            $table->json('live_sensor_data')->nullable();
            $table->json('ecu_info')->nullable();
            $table->json('vehicle_status')->nullable();
            $table->json('raw_payload')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vehicle_profile_id', 'scan_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('de_diagnostic_scans');
    }
};
