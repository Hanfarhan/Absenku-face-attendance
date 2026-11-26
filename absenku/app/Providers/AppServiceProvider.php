<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Override Filament logout: redirect ke landing page (/) bukan admin login.
        // Harus di boot() bukan register() agar binding kita berjalan SETELAH
        // Filament mendaftarkan binding bawaannya.
        $this->app->bind(
            \Filament\Http\Responses\Auth\Contracts\LogoutResponse::class,
            \App\Http\Responses\LogoutResponse::class,
        );
    }
}
