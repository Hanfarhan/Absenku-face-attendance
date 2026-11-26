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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');
            $table->decimal('confidence_score', 6, 4);
            $table->decimal('threshold_used', 4, 2);
            $table->enum('result', ['accepted', 'rejected']);
            $table->enum('rejection_reason', [
                'low_confidence',
                'no_face_detected',
                'multiple_faces',
            ])->nullable();
            $table->string('capture_path')->nullable();
            $table->boolean('is_actual_user')->nullable();
            $table->timestamp('attempted_at');
            $table->timestamps();

            $table->index('user_id');
            $table->index('result');
            $table->index('threshold_used');
            $table->index('is_actual_user');
            $table->index('attempted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
