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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // e.g., 'diagnosis_complete', 'review_approved', 'system_alert'
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data for the notification
            $table->string('action_url')->nullable(); // URL to navigate to when clicked
            $table->string('action_text')->nullable(); // Text for the action button
            
            // Notification status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            // Priority levels: low, normal, high, urgent
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // Notification channels
            $table->boolean('in_app')->default(true);
            $table->boolean('email')->default(false);
            $table->boolean('push')->default(false);
            $table->boolean('sms')->default(false);
            
            // Delivery status
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            
            // Expiration
            $table->timestamp('expires_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index(['priority', 'created_at']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};