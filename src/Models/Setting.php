<?php

namespace Meletisf\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int    $id
 * @property string $key
 * @property string $value
 * @property string $cast_to
 * @property bool   $is_immutable
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Setting extends Model
{
    public function setTable($table)
    {
        return config('laravel-settings.table');
    }
}
