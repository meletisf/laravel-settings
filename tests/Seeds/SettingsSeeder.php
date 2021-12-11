<?php

namespace Meletisf\Settings\Tests\Seeds;

use Illuminate\Database\Seeder;
use Meletisf\Settings\Enums\SettingType;
use Meletisf\Settings\Models\Setting;

class SettingsSeeder extends Seeder
{
    public static function getTestData(): array
    {
        return [
            [
                'key' => 'test.string',
                'value' => 'this is a string',
                'is_immutable' => false,
                'cast_to' => SettingType::String,
            ],
            [
                'key' => 'test.integer',
                'value' => 420,
                'is_immutable' => false,
                'cast_to' => SettingType::Integer,
            ],
            [
                'key' => 'test.boolean',
                'value' => true,
                'is_immutable' => false,
                'cast_to' => SettingType::Boolean,
            ],
            [
                'key' => 'test.array',
                'value' => "['first', 'second']",
                'is_immutable' => false,
                'cast_to' => SettingType::Array,
            ],
            [
                'key' => 'test.serialized',
                'value' => serialize(new \stdClass()),
                'is_immutable' => false,
                'cast_to' => SettingType::Serialized,
            ],
            [
                'key' => 'test.immutable',
                'value' => 'this is immutable',
                'is_immutable' => true,
                'cast_to' => SettingType::String,
            ],
        ];
    }

    public function run()
    {
        Setting::insert(self::getTestData());
    }
}
