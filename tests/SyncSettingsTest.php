<?php

namespace Meletisf\Settings\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Meletisf\Settings\Models\Setting;
use Meletisf\Settings\Settings;
use Meletisf\Settings\Tests\Seeds\SettingsSeeder;

class SyncSettingsTest extends TestCase {
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingsSeeder::class);
    }

    /** @test */
    public function it_syncs_new_settings_from_config()
    {
        $config = $this->getServiceConfiguration();
        $this->app->bind('laravel-settings', function() use ($config) {
            return new Settings($config);
        });

        \Config::set('laravel-settings.required_configuration', [
            'test.required.configuration.string' => 'REQUIRED_CONFIGURATION_VALUE_STRING',
            'test.required.configuration.array' => ['REQUIRED_CONFIGURATION_VALUE_ARRAY', false],
            'test.required.configuration.immutable' => ['REQUIRED_CONFIGURATION_VALUE_IMMUTABLE', true],
        ]);

        $this->artisan('settings:sync');

        $this->assertDatabaseHas(Setting::class, [
            'key' => 'test.required.configuration.string',
            'value' => 'REQUIRED_CONFIGURATION_VALUE_STRING'
        ]);

        $this->assertDatabaseHas(Setting::class, [
            'key' => 'test.required.configuration.array',
            'value' => 'REQUIRED_CONFIGURATION_VALUE_ARRAY',
            'is_immutable' => false
        ]);

        $this->assertDatabaseHas(Setting::class, [
            'key' => 'test.required.configuration.immutable',
            'value' => 'REQUIRED_CONFIGURATION_VALUE_IMMUTABLE',
            'is_immutable' => true
        ]);
    }

}