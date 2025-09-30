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
        Schema::create('car_parts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Part name (e.g., "Brake Pads", "Oil Filter")
            $table->string('part_number')->unique(); // Manufacturer part number
            $table->text('description'); // Detailed description
            $table->string('category'); // Category (engine, brakes, electrical, etc.)
            $table->string('subcategory')->nullable(); // Subcategory (brake_pads, oil_filters, etc.)
            
            // Vehicle compatibility
            $table->json('compatible_brands')->nullable(); // Array of compatible car brands
            $table->json('compatible_models')->nullable(); // Array of compatible car models
            $table->json('compatible_years')->nullable(); // Array of compatible years
            $table->string('engine_type')->nullable(); // gasoline, diesel, electric, hybrid
            $table->string('engine_size')->nullable(); // 1.6L, 2.0L, etc.
            
            // Manufacturer information
            $table->string('manufacturer'); // OEM manufacturer (BMW, Mercedes, etc.)
            $table->string('oem_number')->nullable(); // Original Equipment Manufacturer number
            $table->string('aftermarket_brand')->nullable(); // Aftermarket brand (Bosch, Brembo, etc.)
            $table->string('aftermarket_number')->nullable(); // Aftermarket part number
            
            // Pricing and availability
            $table->decimal('price', 10, 2); // Price in USD
            $table->string('currency', 3)->default('USD');
            $table->integer('stock_quantity')->default(0);
            $table->boolean('in_stock')->default(true);
            $table->string('availability_status')->default('available'); // available, limited, out_of_stock
            
            // Quality and certification
            $table->string('quality_grade')->default('standard'); // oem, premium, standard, economy
            $table->boolean('is_oem')->default(false); // Is it original equipment manufacturer part
            $table->boolean('is_certified')->default(false); // Is it certified by manufacturer
            $table->json('certifications')->nullable(); // Array of certifications
            
            // Physical specifications
            $table->string('weight')->nullable(); // Weight in kg or lbs
            $table->string('dimensions')->nullable(); // Dimensions (L x W x H)
            $table->string('material')->nullable(); // Material (steel, aluminum, plastic, etc.)
            $table->string('color')->nullable(); // Color if applicable
            
            // Installation and maintenance
            $table->integer('installation_time_minutes')->nullable(); // Estimated installation time
            $table->string('difficulty_level')->default('medium'); // easy, medium, hard, professional
            $table->text('installation_notes')->nullable(); // Installation instructions
            $table->integer('warranty_months')->default(12); // Warranty period in months
            
            // Images and documentation
            $table->string('image_url')->nullable(); // Main part image
            $table->json('additional_images')->nullable(); // Array of additional image URLs
            $table->string('manual_url')->nullable(); // Installation manual URL
            $table->string('datasheet_url')->nullable(); // Technical datasheet URL
            
            // SEO and search
            $table->string('slug')->unique(); // URL-friendly slug
            $table->json('search_keywords')->nullable(); // Keywords for search
            $table->text('meta_description')->nullable(); // SEO meta description
            
            // Status and visibility
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamp('featured_until')->nullable(); // Featured until date
            
            // Supplier information
            $table->string('supplier_name')->nullable();
            $table->string('supplier_contact')->nullable();
            $table->string('supplier_website')->nullable();
            
            // Analytics
            $table->integer('view_count')->default(0);
            $table->integer('purchase_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0.00); // Average rating
            $table->integer('review_count')->default(0);
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['category', 'is_active']);
            $table->index(['manufacturer', 'is_active']);
            $table->index(['price', 'is_active']);
            $table->index(['in_stock', 'is_active']);
            $table->index(['quality_grade', 'is_active']);
            $table->index(['is_featured', 'is_active']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_parts');
    }
};