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
        Schema::create('subscription_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->enum('action_type', ['diagnosis', 'api_call', 'storage_upload', 'vehicle_add'])->notNull();
            $table->integer('usage_count')->default(1);
            $table->date('usage_date');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'usage_date']);
            $table->index(['subscription_id', 'usage_date']);
            $table->index(['action_type', 'usage_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_usage');
    }
};

