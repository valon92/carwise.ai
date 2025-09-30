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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payment_id')->unique(); // External payment ID (Stripe, PayPal)
            $table->string('provider'); // stripe, paypal, etc.
            $table->string('type'); // diagnosis, subscription, premium_feature, etc.
            $table->string('status'); // pending, processing, completed, failed, refunded, cancelled
            
            // Payment details
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('description');
            $table->json('metadata')->nullable(); // Additional payment data
            
            // Related entities
            $table->string('related_type')->nullable(); // diagnosis_session, subscription, etc.
            $table->unsignedBigInteger('related_id')->nullable();
            
            // Payment method details
            $table->string('payment_method')->nullable(); // card, paypal, bank_transfer
            $table->string('payment_method_id')->nullable(); // External payment method ID
            $table->json('payment_method_details')->nullable();
            
            // Provider specific data
            $table->json('provider_data')->nullable(); // Raw response from payment provider
            $table->string('provider_transaction_id')->nullable();
            $table->string('provider_fee_id')->nullable();
            
            // Refund information
            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->json('refund_data')->nullable();
            $table->timestamp('refunded_at')->nullable();
            
            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['provider', 'payment_id']);
            $table->index(['type', 'status']);
            $table->index(['related_type', 'related_id']);
            $table->index(['created_at']);
            $table->index(['paid_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};