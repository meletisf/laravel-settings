<?php

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
