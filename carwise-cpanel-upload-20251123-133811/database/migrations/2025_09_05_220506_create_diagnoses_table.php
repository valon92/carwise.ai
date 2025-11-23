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
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('media_file')->nullable();
            $table->enum('media_type', ['image', 'video', 'audio'])->nullable();
            $table->text('description')->nullable();
            $table->json('ai_analysis')->nullable();
            $table->string('problem')->nullable();
            $table->integer('confidence')->nullable();
            $table->json('solutions')->nullable();
            $table->text('next_steps')->nullable();
            $table->enum('status', ['pending', 'analyzing', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnoses');
    }
};
