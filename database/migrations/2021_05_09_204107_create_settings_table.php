<?php

/*
 * This file is part of the Laravel Settings project.
 *
 * All copyright for project Laravel Settings are held by Meletios Flevarakis, 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('laravel-settings.table', 'settings'), function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->mediumText('value')->default(null);
            $table->enum('cast_to', \Meletisf\Settings\Enums\SettingType::getValues())
                ->default(\Meletisf\Settings\Enums\SettingType::String);
            $table->boolean('is_immutable')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop(
            config('laravel-settings.table', 'settings')
        );
    }
}
