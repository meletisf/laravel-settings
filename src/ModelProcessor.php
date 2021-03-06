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

use Illuminate\Database\Eloquent\Model;

class ModelProcessor
{
    public static function unserialize(string $fromString): Model|null
    {
        $model = explode(':', $fromString);

        $class = $model[0];
        $id = $model[1];

        if (!class_exists($class)) {
            return null;
        }

        try {
            $result = $class::findorFail($id);
        } catch (\Exception | \Throwable $e) {
            return null;
        }

        return $result;
    }

    public static function serialize(Model $model): string
    {
        $class = get_class($model);
        $key = $model->getKey();

        return "{$class}:{$key}";
    }
}
