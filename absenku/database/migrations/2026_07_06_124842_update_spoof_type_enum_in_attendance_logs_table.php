<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE attendance_logs MODIFY COLUMN spoof_type 
            ENUM('print_photo', 'digital_screen', 'spoof') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE attendance_logs MODIFY COLUMN spoof_type 
            ENUM('print_photo', 'digital_screen') NULL");
    }
};