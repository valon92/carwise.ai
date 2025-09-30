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
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mechanic_id')->constrained('mechanics')->onDelete('cascade');
            $table->string('subject')->nullable(); // Chat subject/topic
            $table->enum('status', ['active', 'closed', 'archived'])->default('active');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // Related entities
            $table->string('related_type')->nullable(); // diagnosis_session, appointment, etc.
            $table->unsignedBigInteger('related_id')->nullable();
            
            // Timestamps
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['mechanic_id', 'status']);
            $table->index(['status', 'last_message_at']);
            $table->index(['related_type', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};