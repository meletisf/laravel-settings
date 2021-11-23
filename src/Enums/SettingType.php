<?php

namespace Meletisf\Settings\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Integer()
 * @method static static Float()
 * @method static static String()
 * @method static static Boolean()
 * @method static static Array()
 * @method static static Serialized()
 */
final class SettingType extends Enum
{
    public const Integer        = 'integer';
    public const Float          = 'float';
    public const String         = 'string';
    public const Boolean        = 'boolean';
    public const Array          = 'array';
    public const Serialized     = 'serialized';
}
