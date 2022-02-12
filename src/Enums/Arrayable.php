<?php

namespace Meletisf\Settings\Enums;

/**
 * This trait makes the default PHP enums look similar to bensampo's enums which
 * are used by versions of this package which are compatible with PHP 8.0.
 *
 * @method array cases()
 */
trait Arrayable
{
    public static function names(): array
    {
        $names = [];
        foreach (static::cases() as $case) {
            $names[] = $case->name;
        }

        return $names;
    }

    public static function values(): array
    {
        $values = [];
        foreach (static::cases() as $case) {
            $values[] = $case->value;
        }

        return $values;
    }

    public static function toArray(): array
    {
        $data = [];
        foreach (static::cases() as $case) {
            $data[$case->name] = $case->value;
        }

        return $data;
    }
}
