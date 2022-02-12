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
use Meletisf\Settings\Events\SettingCreated;
use Meletisf\Settings\Events\SettingDeleted;
use Meletisf\Settings\Events\SettingUpdated;
use Meletisf\Settings\Models\Setting as SettingModel;

class Settings
{
    private array $cache = ['casted' => []];
    private bool $broadcastEvents = false;

    public function __construct(private array $config)
    {
        if ($this->config['preload_all']) {
            $this->load();
        }
        if (array_key_exists('broadcast_events', $this->config)) {
            $this->broadcastEvents = $this->config['broadcast_events'];
        }
    }

    public function load(): void
    {
        $settings = SettingModel::all();

        /** @var SettingModel $setting */
        foreach ($settings as $setting) {
            $this->cache['casted'][$setting->key] = $this->unserialize(
                $setting->value,
                SettingType::from($setting->cast_to)
            );
        }
    }

    public function get(string $key, bool $ignoreCasting = false): mixed
    {
        if (array_key_exists($key, $this->cache['casted']) && !$ignoreCasting) {
            return $this->cache['casted'][$key];
        }

        $setting = $this->getSettingsModelInstance($key);

        if (!$setting) {
            return null;
        }

        if ($ignoreCasting) {
            return $setting->value;
        }

        $cast = $this->unserialize($setting->value, SettingType::from($setting->cast_to));
        $this->cache['casted'][$key] = $cast;

        return $cast;
    }

    public function set(string $key, mixed $value, bool $isImmutable = false): bool
    {
        $created = false;
        $setting = $this->getSettingsModelInstance($key);

        if ($setting) {
            if ($setting->is_immutable) {
                return false;
            }
        } else {
            $setting = new SettingModel();
            $setting->key = $key;
            $setting->is_immutable = $isImmutable;
            $created = true;
        }

        $castType = $this->guessType($value);
        $setting->cast_to = $castType->value;

        $setting->value = $this->serialize($value, $castType);

        $this->cache['casted'][$key] = $value;

        if ($this->broadcastEvents) {
            if ($created) {
                event(new SettingCreated($key, $setting->value));
            } else {
                event(new SettingUpdated($key, $setting->value));
            }
        }

        return $setting->save();
    }

    public function remove(string $key): bool
    {
        $setting = $this->getSettingsModelInstance($key);

        if (!$setting) {
            return false;
        }

        if ($setting->is_immutable) {
            return false;
        }

        unset($this->cache['casted'][$key]);

        if ($this->broadcastEvents) {
            event(new SettingDeleted($key));
        }

        return $setting->delete();
    }

    public function getCache(): array
    {
        return $this->cache;
    }

    private function getSettingsModelInstance(string $key): SettingModel|null
    {
        return $this->getModelFqn()::where('key', $key)->first();
    }

    public function getModelFqn(): string
    {
        return $this->config['settings_model'];
    }

    private function guessType(mixed $value): SettingType
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

    private function unserialize(string $value, SettingType $castTo): mixed
    {
        return match ($castTo) {
            SettingType::Integer        => (int) $value,
            SettingType::Float          => (float) $value,
            SettingType::String         => $value,
            SettingType::Boolean        => (bool) $value,
            SettingType::Array          => json_decode($value, true),
            SettingType::Serialized     => unserialize($value),
            SettingType::Model          => $this->config['model_processor']::unserialize($value), // @phpstan-ignore-line
        };
    }

    private function serialize(mixed $value, SettingType $type): string
    {
        return match ($type) {
            SettingType::String, SettingType::Integer, SettingType::Float => $value,
            SettingType::Boolean        => $value ? 'true' : 'false',
            SettingType::Array          => json_encode($value),
            SettingType::Serialized     => serialize($value),
            SettingType::Model          => $this->config['model_processor']::serialize($value), // @phpstan-ignore-line
        };
    }
}
