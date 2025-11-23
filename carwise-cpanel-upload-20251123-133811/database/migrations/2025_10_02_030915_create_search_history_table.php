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
        Schema::create('search_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('search_query');
            $table->string('search_type')->default('car_parts');
            $table->string('search_category')->nullable();
            $table->json('search_filters')->nullable();
            $table->integer('results_count')->default(0);
            $table->decimal('search_duration', 8, 3)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->string('session_id')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('operating_system')->nullable();
            $table->timestamp('search_timestamp')->useCurrent();
            $table->boolean('is_successful')->default(true);
            $table->text('error_message')->nullable();
            $table->string('search_source')->default('web');
            $table->string('search_context')->nullable();
            $table->json('additional_data')->nullable();
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['search_query']);
            $table->index(['search_type']);
            $table->index(['search_category']);
            $table->index(['search_timestamp']);
            $table->index(['is_successful']);
            $table->index(['results_count']);
            $table->index(['device_type']);
            $table->index(['browser']);
            $table->index(['session_id']);
            $table->index(['search_source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_history');
    }
};