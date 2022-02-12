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

enum SettingType: string
{
    use Arrayable;

    case Integer = 'integer';
    case Float = 'float';
    case String = 'string';
    case Boolean = 'boolean';
    case Array = 'array';
    case Serialized = 'serialized';
    case Model = 'model';
}
