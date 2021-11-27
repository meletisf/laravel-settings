<?php

namespace Meletisf\Settings\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Meletisf\Settings\Enums\SettingType;
use Meletisf\Settings\Models\Setting;
use Meletisf\Settings\Settings;
use Meletisf\Settings\Tests\Fixtures\Models\User;
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
    function retrieves_uncached_setting()
    {
        $defaults = $this->getServiceConfiguration();
        $defaults['preload_all'] = false;
        $service = new Settings($defaults);
        $expected = SettingsSeeder::getTestData()[0];

        $received = $service->get($expected['key']);
        $this->assertEquals($expected['value'], $received);
        $this->assertArrayHasKey($expected['key'], $service->getCache()['casted']);
    }

    /** @test */
    function returns_null_when_setting_does_not_exist()
    {
        $service = new Settings($this->getServiceConfiguration());
        $result = $service->get('does.not.exist');

        $this->assertNull($result);
    }

    /** @test */
    function get_method_can_ignore_casting()
    {
        $service = new Settings($this->getServiceConfiguration());
        $returned = $service->get('test.array', true);

        $this->assertIsString($returned);
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
    function remove_returns_false_if_setting_does_not_exist()
    {
        $service = new Settings($this->getServiceConfiguration());
        $result = $service->remove('does.not.exist');

        $this->assertFalse($result);
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

    /** @test */
    function get_model_returns_the_correct_model()
    {
        $service = new Settings($this->getServiceConfiguration());
        $returned = PHPUnitUtil::callMethod($service, 'getModel', []);
        $fromConfig = $this->getServiceConfiguration()['settings_model'];

        $this->assertEquals($fromConfig, $returned);
    }

    /** @test */
    function guess_type_guesses_model_correctly()
    {
        $service = new Settings($this->getServiceConfiguration());

        $user = new User;
        $user->id = 1;

        $returned = PHPUnitUtil::callMethod($service, 'guessType', [$user]);

        $this->assertEquals(SettingType::Model, $returned);
    }

    /** @test */
    function guess_type_guesses_integer_correctly()
    {
        $service = new Settings($this->getServiceConfiguration());
        $returned = PHPUnitUtil::callMethod($service, 'guessType', [1234]);

        $this->assertEquals(SettingType::Integer, $returned);
    }

    /** @test */
    function guess_type_guesses_float_correctly()
    {
        $service = new Settings($this->getServiceConfiguration());
        $returned = PHPUnitUtil::callMethod($service, 'guessType', [3.141]);

        $this->assertEquals(SettingType::Float, $returned);
    }

    /** @test */
    function guess_type_guesses_boolean_correctly()
    {
        $service = new Settings($this->getServiceConfiguration());
        $returned = PHPUnitUtil::callMethod($service, 'guessType', [true]);

        $this->assertEquals(SettingType::Boolean, $returned);
    }

    /** @test */
    function guess_type_guesses_array_correctly()
    {
        $service = new Settings($this->getServiceConfiguration());
        $returned = PHPUnitUtil::callMethod($service, 'guessType', [['first', 'second']]);

        $this->assertEquals(SettingType::Array, $returned);
    }

    /** @test */
    function guess_type_guesses_serialized_correctly()
    {
        $service = new Settings($this->getServiceConfiguration());
        $returned = PHPUnitUtil::callMethod($service, 'guessType', [new \stdClass()]);

        $this->assertEquals(SettingType::Serialized, $returned);
    }

    /** @test */
    function unserializes_values_correctly()
    {
        $service = new Settings($this->getServiceConfiguration());

        $integer = PHPUnitUtil::callMethod($service, 'unserialize', ['1234', SettingType::Integer]);
        $this->assertIsInt($integer);

        $float = PHPUnitUtil::callMethod($service, 'unserialize', ['3.141', SettingType::Float]);
        $this->assertIsFloat($float);

        $string = PHPUnitUtil::callMethod($service, 'unserialize', [3.14, SettingType::String]);
        $this->assertIsString($string);

        $bool = PHPUnitUtil::callMethod($service, 'unserialize', ['false', SettingType::Boolean]);
        $this->assertIsBool($bool);

        $array = PHPUnitUtil::callMethod($service, 'unserialize', ['["first", "second"]', SettingType::Array]);
        $this->assertIsArray($array);

        $serialized = PHPUnitUtil::callMethod($service, 'unserialize', ["O:8:\"stdClass\":0:{}", SettingType::Serialized]);
        $this->assertIsObject($serialized);
    }

    /** @test */
    function unserializes_model_correctly()
    {
        $service = new Settings($this->getServiceConfiguration());

        $testModel = Setting::first();

        $model = PHPUnitUtil::callMethod(
            $service,
            'unserialize',
            ["Meletisf\Settings\Models\Setting:{$testModel->id}", SettingType::Model]
        );

        $this->assertIsObject($model);
        $this->assertEquals($testModel->id, $model->id);
    }

    /** @test */
    function serializes_values_correctly()
    {
        $service = new Settings($this->getServiceConfiguration());

        $string = PHPUnitUtil::callMethod($service, 'serialize', ['string', SettingType::String]);
        $this->assertEquals('string', $string);

        $int = PHPUnitUtil::callMethod($service, 'serialize', ['1224', SettingType::Integer]);
        $this->assertEquals('1224', $int);

        $float = PHPUnitUtil::callMethod($service, 'serialize', ['3.141', SettingType::Integer]);
        $this->assertEquals('3.141', $float);

        $bool = PHPUnitUtil::callMethod($service, 'serialize', [false, SettingType::Boolean]);
        $this->assertIsString($bool);
        $this->assertEquals('false', $bool);

        $array = PHPUnitUtil::callMethod($service, 'serialize', [['first', 'second'], SettingType::Array]);
        $this->assertIsString($array);
        $this->assertEquals('["first","second"]', $array);

        $serialized = PHPUnitUtil::callMethod($service, 'serialize', [new \stdClass(), SettingType::Serialized]);
        $this->assertIsString($serialized);
        $this->assertEquals("O:8:\"stdClass\":0:{}", $serialized);

        $testModel = Setting::first();
        $model = PHPUnitUtil::callMethod($service, 'serialize', [$testModel, SettingType::Model]);
        $this->assertIsString($model);
        $this->assertEquals("Meletisf\Settings\Models\Setting:{$testModel->id}", $model);
    }
}