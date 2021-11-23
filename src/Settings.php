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

use Meletisf\Settings\Enums\SettingType;
use Meletisf\Settings\Models\Setting as SettingModel;

class Settings
{
    private array $cache = ['casted' => []];

    public function __construct(array $config)
    {
        if ($config['preload_all']) {
            $this->preload();
        }
    }

    public function preload(): void
    {
        $settings = SettingModel::all();

        /** @var SettingModel $setting */
        foreach ($settings as $setting) {
            $this->cache['casted'][$setting->key] = $this->cast($setting->value, $setting->cast_to);
        }
    }

    public function get(string $key, bool $ignoreCasting = false): mixed
    {
        if (array_key_exists($key, $this->cache['casted'])) {
            return $this->cache['casted'][$key];
        }

        /** @var SettingModel|null $setting */
        $setting = SettingModel::where('key', $key)->first();

        if (!$setting) {
            return null;
        }

        if ($ignoreCasting) {
            return $setting->value;
        }

        $cast = $this->cast($setting->value, $setting->cast_to);
        $this->cache['casted'][$key] = $cast;

        return $cast;
    }

    public function set(string $key, mixed $value): bool
    {
        /** @var SettingModel|null $setting */
        $setting = SettingModel::where('key', $key)->first();

        if ($setting) {
            if ($setting->is_immutable) {
                return false;
            }
        } else {
            $setting = new SettingModel();
            $setting->key = $key;
            $setting->cast_to = $this->guessType($value);
        }

        $setting->value = $this->toString($value, $setting->cast_to);

        $this->cache['casted'][$key] = $value;

        return $setting->save();
    }

    public function getCache(): array
    {
        return $this->cache;
    }

    private function guessType(mixed $value): string
    {
        return match (gettype($value)) {
            'integer'   => SettingType::Integer,
            'double'    => SettingType::Float,
            'boolean'   => SettingType::Boolean,
            'array'     => SettingType::Array,
            'object'    => SettingType::Serialized,
            default     => SettingType::String // + string
        };
    }

    private function cast(string $original, string $castTo): mixed
    {
        return match ($castTo) {
            SettingType::Integer        => (int) $original,
            SettingType::Float          => (float) $original,
            SettingType::String         => $original,
            SettingType::Boolean        => (bool) $original,
            SettingType::Array          => json_decode($original, true),
            SettingType::Serialized     => unserialize($original),
            default                     => (string) $original
        };
    }

    private function toString(mixed $value, string $from): string
    {
        return match ($from) {
            SettingType::String, SettingType::Integer, SettingType::Float => $casted = $value,
            SettingType::Boolean        => $casted = (string) $value,
            SettingType::Array          => $casted = json_encode($value),
            SettingType::Serialized     => $casted = serialize($value),
            default                     => (string) $value
        };
    }
}
