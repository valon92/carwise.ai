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
        Schema::table('diagnosis_sessions', function (Blueprint $table) {
            $table->foreignId('car_id')->nullable()->constrained('cars')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnosis_sessions', function (Blueprint $table) {
            $table->dropForeign(['car_id']);
            $table->dropColumn('car_id');
        });
    }
};