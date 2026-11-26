<?php

namespace App\Helpers;

use Carbon\Carbon;

class AttendanceHelper
{
    /**
     * Cek apakah waktu check-in tertentu dianggap terlambat,
     * berdasarkan jam batas yang diatur di config/attendance.php.
     */
    public static function isLate(Carbon|string $attemptedAt): bool
    {
        $time = $attemptedAt instanceof Carbon
            ? $attemptedAt->format('H:i')
            : Carbon::parse($attemptedAt)->format('H:i');

        return $time > config('attendance.late_threshold_time');
    }

    /**
     * Ambil label status berdasarkan hasil & waktu absensi.
     * Mengembalikan: 'hadir' | 'terlambat' | 'gagal'
     */
    public static function getStatusLabel(string $result, Carbon|string $attemptedAt): string
    {
        if ($result !== 'accepted') {
            return 'gagal';
        }

        return self::isLate($attemptedAt) ? 'terlambat' : 'hadir';
    }

    /**
     * Ambil teks tampilan untuk status (Hadir / Terlambat / Gagal).
     */
    public static function getStatusText(string $result, Carbon|string $attemptedAt): string
    {
        return match (self::getStatusLabel($result, $attemptedAt)) {
            'hadir'     => 'Hadir',
            'terlambat' => 'Terlambat',
            default     => 'Gagal',
        };
    }

    /**
     * Ambil jam batas terlambat yang sedang berlaku (untuk ditampilkan di UI).
     */
    public static function getLateThresholdTime(): string
    {
        return config('attendance.late_threshold_time');
    }
}