<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes only to tables that exist and for columns that don't have indexes yet
        
        // Cars table - add missing indexes
        if (Schema::hasTable('cars')) {
            Schema::table('cars', function (Blueprint $table) {
                // Check if license_plate index doesn't exist
                $indexes = collect(DB::select("PRAGMA index_list(cars)"))->pluck('name');
                if (!$indexes->contains('cars_license_plate_index')) {
                    $table->index('license_plate', 'cars_license_plate_index');
                }
            });
        }

        // Diagnosis sessions table - add missing indexes
        if (Schema::hasTable('diagnosis_sessions')) {
            Schema::table('diagnosis_sessions', function (Blueprint $table) {
                $indexes = collect(DB::select("PRAGMA index_list(diagnosis_sessions)"))->pluck('name');
                
                if (!$indexes->contains('diagnosis_sessions_status_index')) {
                    $table->index('status', 'diagnosis_sessions_status_index');
                }
                if (!$indexes->contains('diagnosis_sessions_car_id_index')) {
                    $table->index('car_id', 'diagnosis_sessions_car_id_index');
                }
                if (!$indexes->contains('diagnosis_sessions_processed_at_index')) {
                    $table->index('processed_at', 'diagnosis_sessions_processed_at_index');
                }
                if (!$indexes->contains('diagnosis_sessions_severity_index')) {
                    $table->index('severity', 'diagnosis_sessions_severity_index');
                }
            });
        }

        // Diagnosis results table - add indexes
        if (Schema::hasTable('diagnosis_results')) {
            Schema::table('diagnosis_results', function (Blueprint $table) {
                $indexes = collect(DB::select("PRAGMA index_list(diagnosis_results)"))->pluck('name');
                
                if (!$indexes->contains('diagnosis_results_diagnosis_session_id_index')) {
                    $table->index('diagnosis_session_id', 'diagnosis_results_diagnosis_session_id_index');
                }
                if (!$indexes->contains('diagnosis_results_severity_index')) {
                    $table->index('severity', 'diagnosis_results_severity_index');
                }
                if (!$indexes->contains('diagnosis_results_confidence_score_index')) {
                    $table->index('confidence_score', 'diagnosis_results_confidence_score_index');
                }
                if (!$indexes->contains('diagnosis_results_requires_immediate_attention_index')) {
                    $table->index('requires_immediate_attention', 'diagnosis_results_requires_immediate_attention_index');
                }
            });
        }

        // Mechanics table - add indexes
        if (Schema::hasTable('mechanics')) {
            Schema::table('mechanics', function (Blueprint $table) {
                $indexes = collect(DB::select("PRAGMA index_list(mechanics)"))->pluck('name');
                
                if (!$indexes->contains('mechanics_user_id_index')) {
                    $table->index('user_id', 'mechanics_user_id_index');
                }
                if (!$indexes->contains('mechanics_is_verified_index')) {
                    $table->index('is_verified', 'mechanics_is_verified_index');
                }
                if (!$indexes->contains('mechanics_is_available_index')) {
                    $table->index('is_available', 'mechanics_is_available_index');
                }
                if (!$indexes->contains('mechanics_city_index')) {
                    $table->index('city', 'mechanics_city_index');
                }
                if (!$indexes->contains('mechanics_rating_index')) {
                    $table->index('rating', 'mechanics_rating_index');
                }
            });
        }

        // Mechanic reviews table - add indexes (if exists)
        if (Schema::hasTable('mechanic_reviews')) {
            Schema::table('mechanic_reviews', function (Blueprint $table) {
                $indexes = collect(DB::select("PRAGMA index_list(mechanic_reviews)"))->pluck('name');
                
                if (!$indexes->contains('mechanic_reviews_mechanic_id_index')) {
                    $table->index('mechanic_id', 'mechanic_reviews_mechanic_id_index');
                }
                if (!$indexes->contains('mechanic_reviews_user_id_index')) {
                    $table->index('user_id', 'mechanic_reviews_user_id_index');
                }
                if (!$indexes->contains('mechanic_reviews_rating_index')) {
                    $table->index('rating', 'mechanic_reviews_rating_index');
                }
            });
        }

        // Notifications table - add indexes (if exists)
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $indexes = collect(DB::select("PRAGMA index_list(notifications)"))->pluck('name');
                
                if (!$indexes->contains('notifications_user_id_index')) {
                    $table->index('user_id', 'notifications_user_id_index');
                }
                if (!$indexes->contains('notifications_read_at_index')) {
                    $table->index('read_at', 'notifications_read_at_index');
                }
                if (!$indexes->contains('notifications_type_index')) {
                    $table->index('type', 'notifications_type_index');
                }
            });
        }

        // Appointments table - add indexes (if exists)
        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table) {
                $indexes = collect(DB::select("PRAGMA index_list(appointments)"))->pluck('name');
                
                if (!$indexes->contains('appointments_user_id_index')) {
                    $table->index('user_id', 'appointments_user_id_index');
                }
                if (!$indexes->contains('appointments_mechanic_id_index')) {
                    $table->index('mechanic_id', 'appointments_mechanic_id_index');
                }
                if (!$indexes->contains('appointments_car_id_index')) {
                    $table->index('car_id', 'appointments_car_id_index');
                }
                if (!$indexes->contains('appointments_status_index')) {
                    $table->index('status', 'appointments_status_index');
                }
                if (!$indexes->contains('appointments_scheduled_at_index')) {
                    $table->index('scheduled_at', 'appointments_scheduled_at_index');
                }
            });
        }

        // Payments table - add indexes (if exists)
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $indexes = collect(DB::select("PRAGMA index_list(payments)"))->pluck('name');
                
                if (!$indexes->contains('payments_user_id_index')) {
                    $table->index('user_id', 'payments_user_id_index');
                }
                if (!$indexes->contains('payments_appointment_id_index')) {
                    $table->index('appointment_id', 'payments_appointment_id_index');
                }
                if (!$indexes->contains('payments_status_index')) {
                    $table->index('status', 'payments_status_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes - uncomment if needed for rollback
        /*
        Schema::table('cars', function (Blueprint $table) {
            $table->dropIndex('cars_license_plate_index');
        });
        */
    }
};

