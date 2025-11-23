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
        Schema::create('diagnosis_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnosis_session_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->string('mime_type');
            $table->json('metadata')->nullable();
            $table->string('ai_analysis')->nullable();
            $table->json('ai_tags')->nullable();
            $table->timestamps();

            $table->index(['diagnosis_session_id']);
            $table->index(['file_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosis_media');
    }
};