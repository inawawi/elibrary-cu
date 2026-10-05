<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if (
            str_starts_with(config('app.url'), 'https://')
            || request()->header('X-Forwarded-Proto') === 'https'
            || request()->header('X-Forwarded-Port') == 443
            || request()->server('SERVER_PORT') == 443
            || request()->server('HTTPS') === 'on'
        ) {
            URL::forceScheme('https');
        }
    }
}
