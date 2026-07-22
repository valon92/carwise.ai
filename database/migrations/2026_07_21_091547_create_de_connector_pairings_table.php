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
        Schema::create('de_connector_pairings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_profile_id')->constrained('de_vehicle_profiles')->cascadeOnDelete();
            $table->string('connector_type', 40)->default('carwise_smart_connector');
            $table->string('device_identifier')->nullable();
            $table->string('pairing_token')->nullable();
            $table->json('capabilities')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->string('status', 30)->default('not_configured');
            $table->timestamps();

            $table->index('vehicle_profile_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('de_connector_pairings');
    }
};
