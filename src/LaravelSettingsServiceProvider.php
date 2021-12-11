<?php

/*
 * This file is part of the Laravel Settings project.
 *
 * All copyright for project Laravel Settings are held by Meletios Flevarakis, 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meletisf\Settings;

use Illuminate\Support\ServiceProvider;
use Meletisf\Settings\Console\SyncSettings;

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

        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncSettings::class,
            ]);
        }
    }
}
