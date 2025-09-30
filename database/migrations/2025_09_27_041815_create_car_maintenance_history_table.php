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
        Schema::create('car_maintenance_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Maintenance details
            $table->string('maintenance_type'); // oil_change, tire_change, timing_belt, brake_pad, air_filter, etc.
            $table->string('title');
            $table->text('description')->nullable();
            
            // Service details
            $table->date('service_date');
            $table->integer('service_mileage');
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            
            // Service provider
            $table->string('service_provider')->nullable(); // garage name
            $table->string('service_provider_contact')->nullable();
            $table->text('service_provider_address')->nullable();
            
            // Parts and materials
            $table->json('parts_used')->nullable(); // array of parts with details
            $table->json('materials_used')->nullable(); // array of materials
            
            // Next service due
            $table->date('next_service_due_date')->nullable();
            $table->integer('next_service_due_mileage')->nullable();
            
            // Documentation
            $table->json('attachments')->nullable(); // receipts, photos, etc.
            $table->text('notes')->nullable();
            
            // Status
            $table->enum('status', ['completed', 'scheduled', 'cancelled'])->default('completed');
            $table->boolean('is_warranty_work')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['car_id', 'service_date']);
            $table->index(['maintenance_type', 'service_date']);
            $table->index(['user_id', 'service_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_maintenance_history');
    }
};