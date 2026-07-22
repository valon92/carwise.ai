<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('de_ai_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_scan_id')->constrained('de_diagnostic_scans')->cascadeOnDelete();
            $table->foreignId('vehicle_profile_id')->constrained('de_vehicle_profiles')->cascadeOnDelete();
            $table->string('provider', 40)->default('ai_diagnosis_service');
            $table->string('problem_description');
            $table->string('severity', 20);
            $table->json('possible_causes')->nullable();
            $table->text('repair_procedure')->nullable();
            $table->decimal('estimated_repair_cost_min', 10, 2)->nullable();
            $table->decimal('estimated_repair_cost_max', 10, 2)->nullable();
            $table->decimal('estimated_repair_time_hours', 6, 2)->nullable();
            $table->json('recommended_parts')->nullable();
            $table->text('safety_recommendation')->nullable();
            $table->boolean('can_continue_driving')->default(true);
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index(['vehicle_profile_id', 'created_at']);
            $table->index('diagnostic_scan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('de_ai_analyses');
    }
};
