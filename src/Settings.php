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

    public function __construct(private array $config)
    {
        if ($this->config['preload_all']) {
            $this->load();
        }
    }

    public function load(): void
    {
        $settings = SettingModel::all();

        /** @var SettingModel $setting */
        foreach ($settings as $setting) {
            $this->cache['casted'][$setting->key] = $this->unserialize($setting->value, $setting->cast_to);
        }
    }

    public function get(string $key, bool $ignoreCasting = false): mixed
    {
        if (array_key_exists($key, $this->cache['casted'])) {
            return $this->cache['casted'][$key];
        }

        $setting = $this->getSettingsModel($key);

        if (!$setting) {
            return null;
        }

        if ($ignoreCasting) {
            return $setting->value;
        }

        $cast = $this->unserialize($setting->value, $setting->cast_to);
        $this->cache['casted'][$key] = $cast;

        return $cast;
    }

    public function set(string $key, mixed $value): bool
    {
        $setting = $this->getSettingsModel($key);

        if ($setting) {
            if ($setting->is_immutable) {
                return false;
            }
        } else {
            $setting = new SettingModel();
            $setting->key = $key;
            $setting->cast_to = $this->guessType($value);
        }

        $setting->value = $this->serialize($value, $setting->cast_to);

        $this->cache['casted'][$key] = $value;

        return $setting->save();
    }

    public function remove(string $key): bool
    {
        $setting = $this->getSettingsModel($key);

        if (!$setting) {
            return false;
        }

        if ($setting->is_immutable) {
            return false;
        }

        unset($this->cache['casted'][$key]);

        return $setting->delete();
    }

    public function getCache(): array
    {
        return $this->cache;
    }

    private function getSettingsModel(string $key): SettingModel|null
    {
        return SettingModel::where('key', $key)->first();
    }

    private function guessType(mixed $value): string
    {
        if (is_subclass_of($value, "Illuminate\Database\Eloquent\Model")) {
            return SettingType::Model;
        }

        return match (gettype($value)) {
            'integer'   => SettingType::Integer,
            'double'    => SettingType::Float,
            'string'    => SettingType::String,
            'boolean'   => SettingType::Boolean,
            'array'     => SettingType::Array,
            default     => SettingType::Serialized
        };
    }

    private function unserialize(string $value, string $castTo): mixed
    {
        return match ($castTo) {
            SettingType::Integer        => (int) $value,
            SettingType::Float          => (float) $value,
            SettingType::String         => $value,
            SettingType::Boolean        => (bool) $value,
            SettingType::Array          => json_decode($value, true),
            SettingType::Serialized     => unserialize($value),
            SettingType::Model          => (new $this->config['model_processor']($value))->unserialize(), // @phpstan-ignore-line
            default                     => (string) $value
        };
    }

    private function serialize(mixed $value, string $from): string
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
