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
            // Skor liveness dari MiniFASNet (1.0 = pasti asli, 0.0 = pasti spoof)
            $table->decimal('liveness_score', 6, 4)
                  ->nullable()
                  ->after('confidence_score');

            // Jenis spoofing yang terdeteksi (null = wajah asli)
            $table->enum('spoof_type', ['print_photo', 'digital_screen'])
                  ->nullable()
                  ->after('liveness_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
             $table->dropColumn(['liveness_score', 'spoof_type']);
        });
    }
};
