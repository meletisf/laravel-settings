<?php

use Meletisf\Settings\Settings;

if (!function_exists('settings')) {

    /**
     * @param string|array|null $param
     * @return Settings|mixed
     */
    function settings(string|array $param = null)
    {
        /** @var Settings $service */
        $service = resolve('laravel-settings');

        if (is_null($param)) {
            return $service;
        }

        if (is_array($param)) {
            return $service->set($param[0], $param[1]);
        }

        return $service->get($param);
    }
}
