<?php

/*
 * This file is part of the Laravel Settings project.
 *
 * All copyright for project Laravel Settings are held by Meletios Flevarakis, 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meletisf\Settings\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Integer()
 * @method static static Float()
 * @method static static String()
 * @method static static Boolean()
 * @method static static Array()
 * @method static static Serialized()
 * @method static static Model()
 */
final class SettingType extends Enum
{
    public const Integer = 'integer';
    public const Float = 'float';
    public const String = 'string';
    public const Boolean = 'boolean';
    public const Array = 'array';
    public const Serialized = 'serialized';
    public const Model = 'model';
}
