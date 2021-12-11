<?php

namespace Meletisf\Settings\Tests;

use JetBrains\PhpStorm\ArrayShape;
use Meletisf\Settings\LaravelSettingsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    #[ArrayShape(['table' => 'string', 'preload_all' => 'bool', 'settings_model' => 'string', 'model_processor' => 'string'])]
    protected function getServiceConfiguration(): array
    {
        return [
            'table' => 'settings',

            'preload_all' => true,

            'settings_model' => \Meletisf\Settings\Models\Setting::class,

            'model_processor' => \Meletisf\Settings\ModelProcessor::class,
        ];
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelSettingsServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->setBasePath(__DIR__ . '/../');

        $this->loadMigrationsFrom([
            '--database' => 'testing',
            '--path' => realpath(__DIR__ . '/migrations'),
        ]);
    }
}
