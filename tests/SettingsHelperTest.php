<?php

namespace Meletisf\Settings\Tests;

use Meletisf\Settings\Settings;
use Meletisf\Settings\Tests\Seeds\SettingsSeeder;

class SettingsHelperTest extends TestCase {

    function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingsSeeder::class);
    }

    /** @test */
    function helper_returns_settings_service()
    {
        $this->app->singleton('laravel-settings', fn($app) => new Settings($this->getServiceConfiguration()));
        $obj = settings();

        $this->assertIsObject($obj);
        $this->assertEquals(Settings::class, get_class($obj));
    }

    /** @test */
    function helper_returns_setting_if_string_is_given()
    {
        $this->app->singleton('laravel-settings', fn($app) => new Settings($this->getServiceConfiguration()));
        $result = settings('test.string');

        $this->assertEquals('this is a string', $result);
    }

    /** @test */
    function helper_sets_setting_if_array_is_given()
    {
        $this->app->singleton('laravel-settings', fn($app) => new Settings($this->getServiceConfiguration()));
        $value = 'updated string';
        $key = 'test.helper';

        $result = settings([$key, $value]);

        $this->assertTrue($result);
        $this->assertDatabaseHas('settings', [
            'key' => $key,
            'value' => $value
        ]);
    }
}