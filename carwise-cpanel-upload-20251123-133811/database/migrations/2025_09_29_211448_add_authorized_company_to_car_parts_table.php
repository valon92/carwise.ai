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
        Schema::table('car_parts', function (Blueprint $table) {
            $table->foreignId('authorized_company_id')->nullable()->constrained('authorized_companies')->onDelete('set null');
            $table->string('company_part_number')->nullable(); // Company's internal part number
            $table->json('international_pricing')->nullable(); // JSON with pricing in different currencies
            $table->json('shipping_info')->nullable(); // JSON with shipping information
            $table->json('warranty_details')->nullable(); // JSON with warranty information
            $table->boolean('is_international_shipping')->default(false);
            $table->json('available_countries')->nullable(); // JSON array of country codes where this part is available
            $table->decimal('discount_percentage', 5, 2)->nullable(); // Discount percentage
            $table->timestamp('discount_valid_until')->nullable();
            $table->boolean('is_bulk_available')->default(false);
            $table->integer('bulk_minimum_quantity')->nullable();
            $table->decimal('bulk_discount_percentage', 5, 2)->nullable();
            $table->json('modification_notes')->nullable(); // JSON with user modification notes
            $table->json('installation_guides')->nullable(); // JSON with installation guide URLs
            $table->json('compatibility_matrix')->nullable(); // JSON with detailed compatibility information
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_parts', function (Blueprint $table) {
            $table->dropForeign(['authorized_company_id']);
            $table->dropColumn([
                'authorized_company_id',
                'company_part_number',
                'international_pricing',
                'shipping_info',
                'warranty_details',
                'is_international_shipping',
                'available_countries',
                'discount_percentage',
                'discount_valid_until',
                'is_bulk_available',
                'bulk_minimum_quantity',
                'bulk_discount_percentage',
                'modification_notes',
                'installation_guides',
                'compatibility_matrix'
            ]);
        });
    }
};