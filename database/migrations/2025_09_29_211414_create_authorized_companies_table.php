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
        Schema::create('authorized_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->json('languages_supported')->nullable(); // Array of language codes
            $table->json('currencies_supported')->nullable(); // Array of currency codes
            $table->json('countries_served')->nullable(); // Array of country codes
            $table->json('specializations')->nullable(); // Array of car part categories
            $table->json('brands_authorized')->nullable(); // Array of car brands they're authorized for
            $table->string('certification_body')->nullable(); // e.g., ISO, TÜV, etc.
            $table->string('certification_number')->nullable();
            $table->date('certification_date')->nullable();
            $table->date('certification_expiry')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('review_count')->default(0);
            $table->integer('parts_count')->default(0);
            $table->integer('orders_count')->default(0);
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->string('payment_methods')->nullable(); // JSON array
            $table->string('shipping_methods')->nullable(); // JSON array
            $table->integer('shipping_time_days')->nullable();
            $table->decimal('shipping_cost_base', 10, 2)->nullable();
            $table->string('shipping_cost_currency')->default('USD');
            $table->boolean('offers_warranty')->default(true);
            $table->integer('warranty_months')->nullable();
            $table->boolean('offers_installation')->default(false);
            $table->decimal('installation_cost_base', 10, 2)->nullable();
            $table->string('installation_cost_currency')->default('USD');
            $table->text('return_policy')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->json('social_media')->nullable(); // JSON object with social media links
            $table->json('contact_hours')->nullable(); // JSON object with business hours
            $table->string('timezone')->default('UTC');
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authorized_companies');
    }
};