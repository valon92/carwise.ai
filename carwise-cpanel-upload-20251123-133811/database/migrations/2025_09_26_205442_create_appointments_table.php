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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mechanic_id')->constrained('mechanics')->onDelete('cascade');
            $table->string('appointment_number')->unique(); // Unique appointment reference
            
            // Appointment details
            $table->string('service_type'); // diagnosis, repair, maintenance, inspection, etc.
            $table->text('description'); // Detailed description of the service needed
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('pending');
            
            // Scheduling
            $table->timestamp('scheduled_at');
            $table->timestamp('estimated_duration'); // Estimated end time
            $table->timestamp('actual_start_at')->nullable();
            $table->timestamp('actual_end_at')->nullable();
            
            // Location
            $table->enum('location_type', ['mechanic_shop', 'customer_location', 'mobile_service'])->default('mechanic_shop');
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Vehicle information
            $table->string('vehicle_make');
            $table->string('vehicle_model');
            $table->integer('vehicle_year');
            $table->string('vehicle_vin')->nullable();
            $table->string('vehicle_license_plate')->nullable();
            $table->integer('vehicle_mileage')->nullable();
            
            // Pricing
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->text('cost_breakdown')->nullable(); // JSON breakdown of costs
            
            // Related entities
            $table->string('related_type')->nullable(); // diagnosis_session, chat_conversation, etc.
            $table->unsignedBigInteger('related_id')->nullable();
            
            // Communication
            $table->text('notes')->nullable(); // Internal notes
            $table->text('customer_notes')->nullable(); // Notes from customer
            $table->text('mechanic_notes')->nullable(); // Notes from mechanic
            
            // Follow-up
            $table->boolean('requires_follow_up')->default(false);
            $table->timestamp('follow_up_date')->nullable();
            $table->text('follow_up_notes')->nullable();
            
            // Cancellation
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->enum('cancelled_by', ['customer', 'mechanic', 'system'])->nullable();
            
            // Rating and feedback
            $table->integer('customer_rating')->nullable(); // 1-5 stars
            $table->text('customer_feedback')->nullable();
            $table->integer('mechanic_rating')->nullable(); // 1-5 stars
            $table->text('mechanic_feedback')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['mechanic_id', 'status']);
            $table->index(['scheduled_at']);
            $table->index(['status', 'scheduled_at']);
            $table->index(['service_type', 'status']);
            $table->index(['related_type', 'related_id']);
            $table->index(['appointment_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};