<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
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
        // Waktu Indonesia: zona waktu diatur di config/app.php (Asia/Jakarta),
        // di sini bahasa untuk diffForHumans() -> "5 menit yang lalu".
        Carbon::setLocale('id');
    }
}
