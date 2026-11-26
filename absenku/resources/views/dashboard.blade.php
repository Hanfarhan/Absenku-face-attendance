<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="fa-shell">

        @include('dashboard.sidebar')

        <div class="fa-main">
            @include('dashboard.topbar')

            <div class="fa-content">
                @include('dashboard.pages.page-dashboard')
                @include('dashboard.pages.page-absensi')
                @include('dashboard.pages.page-riwayat')
                @include('dashboard.pages.page-wajah')
            </div>
        </div>

    </div>

    @include('dashboard.scripts')
    @vite(['resources/js/face-register.js']) 
    @vite(['resources/js/face-register.js', 'resources/js/attendance-camera.js'])
</x-app-layout>