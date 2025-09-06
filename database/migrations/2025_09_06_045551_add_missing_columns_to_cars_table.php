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
        Schema::table('cars', function (Blueprint $table) {
            $table->string('fuel_type', 20)->nullable()->after('color');
            $table->string('transmission', 20)->nullable()->after('fuel_type');
            $table->integer('mileage')->nullable()->after('transmission');
            $table->date('purchase_date')->nullable()->after('mileage');
            $table->decimal('purchase_price', 10, 2)->nullable()->after('purchase_date');
            $table->text('notes')->nullable()->after('purchase_price');
            $table->json('specifications')->nullable()->after('notes');
            $table->json('maintenance_history')->nullable()->after('specifications');
            $table->string('status', 20)->default('active')->after('maintenance_history');

            // Add indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['brand', 'model']);
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['brand', 'model']);
            $table->dropIndex(['year']);
            
            $table->dropColumn([
                'fuel_type',
                'transmission',
                'mileage',
                'purchase_date',
                'purchase_price',
                'notes',
                'specifications',
                'maintenance_history',
                'status'
            ]);
        });
    }
};