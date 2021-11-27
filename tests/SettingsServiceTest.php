<?php

namespace Meletisf\Settings\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Meletisf\Settings\Settings;
use Meletisf\Settings\Tests\Seeds\SettingsSeeder;

class SettingsServiceTest extends TestCase {
    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingsSeeder::class);
    }

    /** @test */
    function preloads_settings_when_initialized()
    {
        $service = new Settings($this->getServiceConfiguration());
        $this->assertArrayHasKey('test.string', $service->getCache()['casted']);
        $this->assertArrayHasKey('test.integer', $service->getCache()['casted']);
    }

    /** @test */
    function load_method_loads_all_the_settings()
    {
        $service = new Settings(['preload_all' => false]);
        $service->load();
        $this->assertArrayHasKey('test.string', $service->getCache()['casted']);
        $this->assertArrayHasKey('test.integer', $service->getCache()['casted']);
        $this->assertCount(count($service->getCache()['casted']), SettingsSeeder::getTestData());
    }

    /** @test */
    function retrieves_a_settings_using_a_key()
    {
        $service = new Settings($this->getServiceConfiguration());
        $expected = SettingsSeeder::getTestData()[0];

        $received = $service->get($expected['key']);
        $this->assertEquals($expected['value'], $received);
    }

    /** @test */
    function creates_a_new_setting()
    {
        $service = new Settings($this->getServiceConfiguration());
        $result = $service->set('test.creates_a_new_setting', 'creates_a_new_setting');

        $this->assertTrue($result);
        $this->assertDatabaseHas('settings', [
            'key' => 'test.creates_a_new_setting',
            'value' => 'creates_a_new_setting'
        ]);
        $this->assertArrayHasKey('test.creates_a_new_setting', $service->getCache()['casted']);
    }

    /** @test */
    function updates_existing_setting()
    {
        $service = new Settings($this->getServiceConfiguration());
        $key = "test.string";
        $newVal = "this is updated string";

        $this->assertDatabaseHas('settings', [
            'key' => $key,
            'value' => 'this is a string'
        ]);
        $result = $service->set($key, $newVal);

        $this->assertTrue($result);
        $this->assertDatabaseHas('settings', [
            'key' => $key,
            'value' => $newVal
        ]);
        $this->assertArrayHasKey($key, $service->getCache()['casted']);
        $this->assertEquals($newVal, $service->getCache()['casted'][$key]);
    }

    /** @test */
    function it_does_not_update_immutable_settings()
    {
        $service = new Settings($this->getServiceConfiguration());
        $newVal = 'update immutable';
        $result = $service->set('test.immutable', $newVal);

        $this->assertFalse($result);
        $this->assertDatabaseMissing('settings', [
            'value' => $newVal
        ]);
    }

    /** @test */
    function removes_a_setting()
    {
        $service = new Settings($this->getServiceConfiguration());
        $subject = SettingsSeeder::getTestData()[0];
        $result = $service->remove($subject['key']);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('settings', [
            'key' => $subject['key']
        ]);
        $this->assertArrayNotHasKey($subject['key'], $service->getCache()['casted']);
    }

    /** @test */
    function does_not_remove_immutable_settings()
    {
        $service = new Settings($this->getServiceConfiguration());
        $key = 'test.immutable';

        $result = $service->remove($key);

        $this->assertFalse($result);
        $this->assertDatabaseHas('settings', [
            'key' => $key
        ]);
        $this->assertArrayHasKey($key, $service->getCache()['casted']);
    }
}