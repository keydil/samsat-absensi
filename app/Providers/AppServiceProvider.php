<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- Tambahin ini

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
        // Tambahin blok ini biar Railway ngebaca asetnya pake HTTPS
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
    }
}