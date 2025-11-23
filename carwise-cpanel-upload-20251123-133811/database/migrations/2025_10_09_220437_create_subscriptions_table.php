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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan_id', 50);
            $table->enum('status', ['active', 'cancelled', 'expired', 'trial'])->default('trial');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->date('next_billing_date')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->string('payment_method_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('subscription_plans');
            $table->index(['user_id', 'status']);
            $table->index('stripe_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

