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
        Schema::create('diagnosis_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->integer('mileage')->nullable();
            $table->string('engine_type')->nullable();
            $table->string('engine_size')->nullable();
            $table->text('description');
            $table->json('symptoms')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('ai_response')->nullable();
            $table->integer('confidence_score')->nullable();
            $table->string('severity')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['session_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosis_sessions');
    }
};