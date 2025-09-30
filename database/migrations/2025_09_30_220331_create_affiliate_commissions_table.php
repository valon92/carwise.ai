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
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('click_id');
            $table->foreignId('part_id')->constrained('car_parts')->onDelete('cascade');
            $table->string('brand');
            $table->string('category');
            $table->string('order_id');
            $table->string('customer_email');
            $table->decimal('purchase_amount', 10, 2);
            $table->string('currency', 3);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamp('purchase_date');
            $table->timestamp('payment_date')->nullable();
            $table->timestamps();

            $table->index(['click_id']);
            $table->index(['part_id']);
            $table->index(['brand']);
            $table->index(['category']);
            $table->index(['status']);
            $table->index(['purchase_date']);
            $table->index(['order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_commissions');
    }
};