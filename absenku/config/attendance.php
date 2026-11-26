<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Jam Batas Terlambat
    |--------------------------------------------------------------------------
    |
    | Jam batas dimana absensi dianggap terlambat (format 24 jam: H:i).
    | Ubah nilai ini saja jika ingin mengubah aturan jam terlambat
    | di seluruh aplikasi (dashboard, absensi, riwayat).
    |
    */
    'late_threshold_time' => env('ATTENDANCE_LATE_TIME', '08:00'),

    /*
    |--------------------------------------------------------------------------
    | Default Threshold Face Recognition
    |--------------------------------------------------------------------------
    |
    | Nilai threshold yang dipakai jika belum ada threshold aktif
    | di tabel `thresholds` (sebelum admin mengatur dari panel admin).
    |
    */
    'default_threshold' => env('ATTENDANCE_DEFAULT_THRESHOLD', 0.75),

    /*
    |--------------------------------------------------------------------------
    | URL Python Face Recognition API
    |--------------------------------------------------------------------------
    |
    | Base URL untuk FastAPI service (MTCNN + FaceNet).
    | Ubah di sini jika nanti API dipindah ke server lain.
    |
    */
    'face_api_url' => env('FACE_API_URL', 'http://127.0.0.1:8001'),

    /*
    |--------------------------------------------------------------------------
    | Jumlah Foto Registrasi Wajah
    |--------------------------------------------------------------------------
    |
    | Jumlah foto yang diambil saat user mendaftarkan wajah.
    |
    */
    'registration_photo_count' => env('ATTENDANCE_REG_PHOTO_COUNT', 10),

];