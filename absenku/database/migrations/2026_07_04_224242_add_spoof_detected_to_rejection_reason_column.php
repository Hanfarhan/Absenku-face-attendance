<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE attendance_logs MODIFY COLUMN rejection_reason 
            ENUM('low_confidence', 'no_face_detected', 'multiple_faces', 'spoof_detected') 
            NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE attendance_logs MODIFY COLUMN rejection_reason 
            ENUM('low_confidence', 'no_face_detected', 'multiple_faces') 
            NULL");
    }
};