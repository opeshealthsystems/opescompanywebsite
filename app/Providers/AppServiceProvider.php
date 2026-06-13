<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        // Allow dotted anonymous component tags (e.g. <x-layouts.app>) to
        // resolve to views under resources/views, so the master layout can
        // live at resources/views/layouts/app.blade.php.
        Blade::anonymousComponentPath(resource_path('views'));
    }
}
