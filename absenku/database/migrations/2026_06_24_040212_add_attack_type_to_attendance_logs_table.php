<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->enum('attack_type', ['genuine', 'print_photo', 'digital_screen'])
                  ->default('genuine')
                  ->after('is_actual_user');
        });
    }
 
    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropColumn('attack_type');
        });
    }
};
