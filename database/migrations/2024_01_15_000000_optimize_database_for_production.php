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
        // Add indexes for better performance
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index('created_at');
            $table->index(['email', 'email_verified_at']);
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('make');
            $table->index('model');
            $table->index('year');
            $table->index(['user_id', 'created_at']);
        });

        Schema::table('diagnosis_sessions', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('session_id');
            $table->index('status');
            $table->index('created_at');
            $table->index(['user_id', 'status']);
        });

        Schema::table('diagnosis_results', function (Blueprint $table) {
            $table->index('session_id');
            $table->index('severity');
            $table->index('created_at');
        });

        Schema::table('diagnosis_media', function (Blueprint $table) {
            $table->index('session_id');
            $table->index('file_type');
        });

        // Add foreign key constraints for data integrity
        Schema::table('cars', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('diagnosis_sessions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('diagnosis_results', function (Blueprint $table) {
            $table->foreign('session_id')->references('id')->on('diagnosis_sessions')->onDelete('cascade');
        });

        Schema::table('diagnosis_media', function (Blueprint $table) {
            $table->foreign('session_id')->references('id')->on('diagnosis_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['email', 'email_verified_at']);
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['make']);
            $table->dropIndex(['model']);
            $table->dropIndex(['year']);
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('diagnosis_sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['session_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('diagnosis_results', function (Blueprint $table) {
            $table->dropIndex(['session_id']);
            $table->dropIndex(['severity']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('diagnosis_media', function (Blueprint $table) {
            $table->dropIndex(['session_id']);
            $table->dropIndex(['file_type']);
        });

        // Remove foreign key constraints
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('diagnosis_sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('diagnosis_results', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
        });

        Schema::table('diagnosis_media', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
        });
    }
};
