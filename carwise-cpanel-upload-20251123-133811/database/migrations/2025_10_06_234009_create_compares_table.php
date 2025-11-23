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
        Schema::create('compares', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('part_id')->nullable(); // Can be null for external parts
            $table->string('part_name');
            $table->string('part_brand')->nullable();
            $table->string('part_number')->nullable();
            $table->text('part_description')->nullable();
            $table->string('part_image_url')->nullable();
            $table->string('part_category')->nullable();
            $table->decimal('part_price', 10, 2);
            $table->string('part_currency', 3)->default('USD');
            $table->string('source')->default('carwise'); // carwise, amazon, ebay, etc.
            $table->string('affiliate_url')->nullable();
            $table->json('specifications')->nullable(); // Part specifications for comparison
            $table->json('features')->nullable(); // Part features
            $table->json('compatibility')->nullable(); // Vehicle compatibility
            $table->string('warranty')->nullable();
            $table->string('shipping_info')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamp('added_at')->useCurrent();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'added_at']);
            $table->index(['user_id', 'source']);
            $table->index('part_id');
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compares');
    }
};
