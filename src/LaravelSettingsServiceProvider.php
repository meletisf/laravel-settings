<?php

namespace Meletisf\Settings;

use Illuminate\Support\ServiceProvider;

class LaravelSettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('laravel-settings', fn ($app) => new Settings(
            config('laravel-settings')
        ));
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-settings.php' => config_path('laravel-settings.php'),
        ], 'settings-config');
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'laravel2fa-migrations');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

}
