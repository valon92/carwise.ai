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
        Schema::create('diagnosis_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnosis_session_id')->constrained()->onDelete('cascade');
            $table->string('problem_title');
            $table->text('problem_description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->integer('confidence_score');
            $table->json('likely_causes');
            $table->json('recommended_actions');
            $table->json('estimated_costs')->nullable();
            $table->json('ai_insights')->nullable();
            $table->json('related_issues')->nullable();
            $table->boolean('requires_immediate_attention')->default(false);
            $table->string('ai_model_version')->nullable();
            $table->timestamp('analysis_completed_at');
            $table->timestamps();

            $table->index(['diagnosis_session_id']);
            $table->index(['severity']);
            $table->index(['confidence_score']);
            $table->index(['requires_immediate_attention']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosis_results');
    }
};