<?php

/*
 * This file is part of the Laravel Settings project.
 *
 * All copyright for project Laravel Settings are held by Meletios Flevarakis, 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meletisf\Settings\Events;

use Illuminate\Foundation\Events\Dispatchable;

class SettingDeleted
{
    use Dispatchable;

    public function __construct(public string $setting) {}
}
