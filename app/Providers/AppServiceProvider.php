<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        if (app()->runningInConsole()) {
            return;
        }

        $request = request();
        $forwardedProto = (string) $request->headers->get('x-forwarded-proto', '');
        $appUrl = (string) config('app.url', '');

        if (
            app()->isProduction()
            || str_contains(strtolower($forwardedProto), 'https')
            || str_starts_with(strtolower($appUrl), 'https://')
        ) {
            URL::forceScheme('https');
        }
    }
}
