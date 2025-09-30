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
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('cars', 'current_mileage')) {
                $table->integer('current_mileage')->nullable()->after('mileage');
            }
            if (!Schema::hasColumn('cars', 'last_service_date')) {
                $table->date('last_service_date')->nullable()->after('current_mileage');
            }
            if (!Schema::hasColumn('cars', 'last_service_mileage')) {
                $table->integer('last_service_mileage')->nullable()->after('last_service_date');
            }
            
            // Oil change tracking
            if (!Schema::hasColumn('cars', 'last_oil_change_date')) {
                $table->date('last_oil_change_date')->nullable()->after('last_service_mileage');
            }
            if (!Schema::hasColumn('cars', 'last_oil_change_mileage')) {
                $table->integer('last_oil_change_mileage')->nullable()->after('last_oil_change_date');
            }
            if (!Schema::hasColumn('cars', 'oil_change_interval')) {
                $table->integer('oil_change_interval')->default(15000)->after('last_oil_change_mileage');
            }
            
            // Tire tracking
            if (!Schema::hasColumn('cars', 'last_tire_change_date')) {
                $table->date('last_tire_change_date')->nullable()->after('oil_change_interval');
            }
            if (!Schema::hasColumn('cars', 'last_tire_change_mileage')) {
                $table->integer('last_tire_change_mileage')->nullable()->after('last_tire_change_date');
            }
            if (!Schema::hasColumn('cars', 'tire_change_interval')) {
                $table->integer('tire_change_interval')->default(50000)->after('last_tire_change_mileage');
            }
            
            // Timing belt tracking
            if (!Schema::hasColumn('cars', 'last_timing_belt_change_date')) {
                $table->date('last_timing_belt_change_date')->nullable()->after('tire_change_interval');
            }
            if (!Schema::hasColumn('cars', 'last_timing_belt_change_mileage')) {
                $table->integer('last_timing_belt_change_mileage')->nullable()->after('last_timing_belt_change_date');
            }
            if (!Schema::hasColumn('cars', 'timing_belt_change_interval')) {
                $table->integer('timing_belt_change_interval')->default(100000)->after('last_timing_belt_change_mileage');
            }
            
            // Brake pad tracking
            if (!Schema::hasColumn('cars', 'last_brake_pad_change_date')) {
                $table->date('last_brake_pad_change_date')->nullable()->after('timing_belt_change_interval');
            }
            if (!Schema::hasColumn('cars', 'last_brake_pad_change_mileage')) {
                $table->integer('last_brake_pad_change_mileage')->nullable()->after('last_brake_pad_change_date');
            }
            if (!Schema::hasColumn('cars', 'brake_pad_change_interval')) {
                $table->integer('brake_pad_change_interval')->default(60000)->after('last_brake_pad_change_mileage');
            }
            
            // Air filter tracking
            if (!Schema::hasColumn('cars', 'last_air_filter_change_date')) {
                $table->date('last_air_filter_change_date')->nullable()->after('brake_pad_change_interval');
            }
            if (!Schema::hasColumn('cars', 'last_air_filter_change_mileage')) {
                $table->integer('last_air_filter_change_mileage')->nullable()->after('last_air_filter_change_date');
            }
            if (!Schema::hasColumn('cars', 'air_filter_change_interval')) {
                $table->integer('air_filter_change_interval')->default(30000)->after('last_air_filter_change_mileage');
            }
            
            // Fuel filter tracking
            if (!Schema::hasColumn('cars', 'last_fuel_filter_change_date')) {
                $table->date('last_fuel_filter_change_date')->nullable()->after('air_filter_change_interval');
            }
            if (!Schema::hasColumn('cars', 'last_fuel_filter_change_mileage')) {
                $table->integer('last_fuel_filter_change_mileage')->nullable()->after('last_fuel_filter_change_date');
            }
            if (!Schema::hasColumn('cars', 'fuel_filter_change_interval')) {
                $table->integer('fuel_filter_change_interval')->default(40000)->after('last_fuel_filter_change_mileage');
            }
            
            // Spark plugs tracking
            if (!Schema::hasColumn('cars', 'last_spark_plugs_change_date')) {
                $table->date('last_spark_plugs_change_date')->nullable()->after('fuel_filter_change_interval');
            }
            if (!Schema::hasColumn('cars', 'last_spark_plugs_change_mileage')) {
                $table->integer('last_spark_plugs_change_mileage')->nullable()->after('last_spark_plugs_change_date');
            }
            if (!Schema::hasColumn('cars', 'spark_plugs_change_interval')) {
                $table->integer('spark_plugs_change_interval')->default(60000)->after('last_spark_plugs_change_mileage');
            }
            
            // Battery tracking
            if (!Schema::hasColumn('cars', 'battery_installation_date')) {
                $table->date('battery_installation_date')->nullable()->after('spark_plugs_change_interval');
            }
            if (!Schema::hasColumn('cars', 'battery_life_years')) {
                $table->integer('battery_life_years')->default(4)->after('battery_installation_date');
            }
            
            // Seasonal tire tracking
            if (!Schema::hasColumn('cars', 'current_tire_type')) {
                $table->enum('current_tire_type', ['summer', 'winter', 'all_season'])->default('all_season')->after('battery_life_years');
            }
            if (!Schema::hasColumn('cars', 'last_seasonal_tire_change_date')) {
                $table->date('last_seasonal_tire_change_date')->nullable()->after('current_tire_type');
            }
            
            // Insurance and registration
            if (!Schema::hasColumn('cars', 'insurance_expiry_date')) {
                $table->date('insurance_expiry_date')->nullable()->after('last_seasonal_tire_change_date');
            }
            if (!Schema::hasColumn('cars', 'registration_expiry_date')) {
                $table->date('registration_expiry_date')->nullable()->after('insurance_expiry_date');
            }
            
            // Maintenance preferences
            if (!Schema::hasColumn('cars', 'maintenance_notifications_enabled')) {
                $table->boolean('maintenance_notifications_enabled')->default(true)->after('registration_expiry_date');
            }
            if (!Schema::hasColumn('cars', 'notification_advance_days')) {
                $table->integer('notification_advance_days')->default(30)->after('maintenance_notifications_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn([
                'current_mileage',
                'last_service_date',
                'last_service_mileage',
                'last_oil_change_date',
                'last_oil_change_mileage',
                'oil_change_interval',
                'last_tire_change_date',
                'last_tire_change_mileage',
                'tire_change_interval',
                'last_timing_belt_change_date',
                'last_timing_belt_change_mileage',
                'timing_belt_change_interval',
                'last_brake_pad_change_date',
                'last_brake_pad_change_mileage',
                'brake_pad_change_interval',
                'last_air_filter_change_date',
                'last_air_filter_change_mileage',
                'air_filter_change_interval',
                'last_fuel_filter_change_date',
                'last_fuel_filter_change_mileage',
                'fuel_filter_change_interval',
                'last_spark_plugs_change_date',
                'last_spark_plugs_change_mileage',
                'spark_plugs_change_interval',
                'battery_installation_date',
                'battery_life_years',
                'current_tire_type',
                'last_seasonal_tire_change_date',
                'insurance_expiry_date',
                'registration_expiry_date',
                'maintenance_notifications_enabled',
                'notification_advance_days'
            ]);
        });
    }
};