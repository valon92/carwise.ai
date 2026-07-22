<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('de_vehicle_history_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_profile_id')->constrained('de_vehicle_profiles')->cascadeOnDelete();
            $table->string('event_type', 40);
            $table->timestamp('event_date');
            $table->unsignedInteger('mileage')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('diagnostic_scan_id')->nullable()->constrained('de_diagnostic_scans')->nullOnDelete();
            $table->foreignId('ai_analysis_id')->nullable()->constrained('de_ai_analyses')->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['vehicle_profile_id', 'event_date']);
            $table->index(['vehicle_profile_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('de_vehicle_history_events');
    }
};
