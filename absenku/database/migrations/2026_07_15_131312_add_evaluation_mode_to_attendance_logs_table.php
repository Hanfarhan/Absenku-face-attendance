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
        Schema::table('attendance_logs', function (Blueprint $table) {
            // Mode evaluasi: 'full_pipeline' = Sistem B (FaceNet + Liveness Detection)
            //                'facenet_only'  = Sistem A (hanya FaceNet, bypass liveness)
            // Default: full_pipeline agar record lama tetap konsisten
            $table->enum('evaluation_mode', ['full_pipeline', 'facenet_only'])
                  ->default('full_pipeline')
                  ->after('is_actual_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropColumn('evaluation_mode');
        });
    }
};
