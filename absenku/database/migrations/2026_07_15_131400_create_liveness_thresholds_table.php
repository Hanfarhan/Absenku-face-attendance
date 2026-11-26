<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel ini menyimpan riwayat perubahan liveness threshold
     * (MiniFASNetV2-SE). Setiap kali admin mengubah threshold dari
     * dashboard, record baru dibuat dan is_active di-set ke true
     * (record lama di-set false).
     *
     * Terpisah dari tabel 'thresholds' yang menyimpan FaceNet threshold,
     * agar kedua threshold bisa dikontrol secara independen.
     */
    public function up(): void
    {
        Schema::create('liveness_thresholds', function (Blueprint $table) {
            $table->id();

            // Nilai threshold (0.00 - 1.00)
            // score >= value → wajah dianggap REAL
            // score <  value → wajah dianggap SPOOF
            $table->decimal('value', 4, 2);

            // Admin yang mengubah threshold
            $table->foreignId('set_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            // Catatan/alasan perubahan threshold (opsional)
            $table->string('note')->nullable();

            // Hanya satu record yang boleh is_active=true pada satu waktu
            $table->boolean('is_active')->default(false);

            $table->timestamps();

            $table->index('is_active');
        });

        // Seed nilai awal: threshold default 0.5
        DB::table('liveness_thresholds')->insert([
            'value'      => 0.50,
            'set_by'     => null,
            'note'       => 'Nilai default awal sistem',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liveness_thresholds');
    }
};
