<?php

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
