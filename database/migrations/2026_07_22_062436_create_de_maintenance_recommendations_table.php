<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('de_maintenance_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_profile_id')->constrained('de_vehicle_profiles')->cascadeOnDelete();
            $table->string('recommendation_type', 40);
            $table->string('title');
            $table->text('description');
            $table->string('priority', 20)->default('medium');
            $table->unsignedInteger('due_at_mileage')->nullable();
            $table->date('due_at_date')->nullable();
            $table->text('reasoning')->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('source', 40)->default('ai_predictive');
            $table->timestamps();

            $table->index(['vehicle_profile_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('de_maintenance_recommendations');
    }
};
