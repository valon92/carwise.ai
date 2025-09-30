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
        Schema::create('maintenance_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Notification details
            $table->string('maintenance_type'); // oil_change, tire_change, timing_belt, etc.
            $table->string('title');
            $table->text('message');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // Due dates
            $table->date('due_date');
            $table->integer('due_mileage')->nullable();
            $table->integer('current_mileage')->nullable();
            
            // Notification status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            
            // Notification channels
            $table->boolean('in_app')->default(true);
            $table->boolean('email')->default(false);
            $table->boolean('push')->default(false);
            $table->boolean('sms')->default(false);
            
            // Action taken
            $table->boolean('action_taken')->default(false);
            $table->timestamp('action_taken_at')->nullable();
            $table->text('action_notes')->nullable();
            
            // Recurring notification
            $table->boolean('is_recurring')->default(false);
            $table->integer('recurring_interval_days')->nullable();
            $table->timestamp('next_notification_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['car_id', 'due_date']);
            $table->index(['user_id', 'is_read']);
            $table->index(['maintenance_type', 'due_date']);
            $table->index(['priority', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_notifications');
    }
};