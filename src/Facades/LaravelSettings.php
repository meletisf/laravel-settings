<?php

/*
 * This file is part of the Laravel Settings project.
 *
 * All copyright for project Laravel Settings are held by Meletios Flevarakis, 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meletisf\Settings\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Meletisf\Settings\Settings
 */
class LaravelSettings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-settings';
    }
}
