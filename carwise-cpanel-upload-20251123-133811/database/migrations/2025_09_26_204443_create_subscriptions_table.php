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
            $table->string('subscription_id')->unique(); // External subscription ID
            $table->string('provider'); // stripe, paypal, etc.
            $table->string('plan_name'); // basic, premium, pro, etc.
            $table->string('status'); // active, cancelled, past_due, unpaid, trialing
            
            // Plan details
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('interval'); // month, year
            $table->integer('interval_count')->default(1);
            
            // Subscription periods
            $table->timestamp('current_period_start');
            $table->timestamp('current_period_end');
            $table->timestamp('trial_start')->nullable();
            $table->timestamp('trial_end')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            
            // Features and limits
            $table->json('features')->nullable(); // Available features for this plan
            $table->json('limits')->nullable(); // Usage limits
            
            // Provider specific data
            $table->json('provider_data')->nullable();
            $table->string('provider_customer_id')->nullable();
            
            // Payment method
            $table->string('payment_method_id')->nullable();
            $table->json('payment_method_details')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['provider', 'subscription_id']);
            $table->index(['plan_name', 'status']);
            $table->index(['current_period_end']);
            $table->index(['trial_end']);
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