<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Absenku</title>

    <!-- Vite (WAJIB untuk Breeze) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100">

    <!-- NAVBAR -->
    <header class="flex justify-between items-center px-8 py-4 bg-white dark:bg-gray-800 shadow-sm">
        <h1 class="text-xl font-bold text-blue-600">Absenku</h1>

        @if (Route::has('login'))
            <nav class="flex gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600">
                        Login
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <!-- HERO -->
    <section class="text-center py-20 px-6 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
        <h2 class="text-4xl md:text-5xl font-bold">
            Sistem Absensi Face Recognition
        </h2>

        <p class="mt-4 text-lg max-w-2xl mx-auto">
            Absensi lebih cepat, aman, dan tanpa kecurangan menggunakan teknologi pengenalan wajah berbasis AI.
        </p>

        <div class="mt-8 flex justify-center gap-4">
            @auth
                <a href="{{ url('/dashboard') }}"
                   class="px-6 py-3 bg-white text-blue-600 rounded-lg font-semibold shadow hover:scale-105 transition">
                    Masuk Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="px-6 py-3 bg-white text-blue-600 rounded-lg font-semibold shadow hover:scale-105 transition">
                    Mulai Sekarang
                </a>

                <a href="{{ route('register') }}"
                   class="px-6 py-3 border border-white rounded-lg hover:bg-white hover:text-blue-600 transition">
                    Daftar
                </a>
            @endauth
        </div>
    </section>

    <!-- FITUR -->
    <section class="py-16 px-8 max-w-6xl mx-auto">
        <h3 class="text-2xl font-bold text-center mb-10">
            Fitur Unggulan
        </h3>

        <div class="grid md:grid-cols-3 gap-8">

            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition">
                <h4 class="font-semibold text-lg">Face Recognition</h4>
                <p class="mt-2 text-gray-500 text-sm">
                    Absensi otomatis dengan teknologi pengenalan wajah tanpa sentuhan.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition">
                <h4 class="font-semibold text-lg">Real-time Monitoring</h4>
                <p class="mt-2 text-gray-500 text-sm">
                    Data absensi langsung tersimpan dan dapat dipantau secara realtime.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition">
                <h4 class="font-semibold text-lg">Anti Kecurangan</h4>
                <p class="mt-2 text-gray-500 text-sm">
                    Mencegah titip absen dengan sistem identifikasi wajah berbasis AI.
                </p>
            </div>

        </div>
    </section>

    <!-- CARA KERJA -->
    <section class="py-16 bg-gray-100 dark:bg-gray-800 px-8">
        <div class="max-w-5xl mx-auto text-center">
            <h3 class="text-2xl font-bold mb-10">Cara Kerja</h3>

            <div class="grid md:grid-cols-3 gap-8 text-left">

                <div>
                    <h4 class="font-bold text-blue-600">1. Registrasi Wajah</h4>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-2">
                        Pengguna mendaftarkan wajah ke sistem.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-blue-600">2. Scan Kamera</h4>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-2">
                        Kamera mendeteksi wajah saat absensi.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-blue-600">3. Data Tersimpan</h4>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-2">
                        Absensi otomatis tercatat ke database.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 text-center">
        <h3 class="text-2xl font-bold">
            Siap menggunakan Absenku?
        </h3>

        <p class="mt-2 text-gray-500">
            Tingkatkan efisiensi absensi sekarang juga.
        </p>

        <div class="mt-6">
            @guest
                <a href="{{ route('register') }}"
                   class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                    Daftar Sekarang
                </a>
            @endguest
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="text-center py-6 text-sm text-gray-500">
        © {{ date('Y') }} Absenku - Face Recognition Attendance System
    </footer>

</body>
</html>