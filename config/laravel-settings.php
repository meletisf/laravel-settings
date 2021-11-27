<?php

return [
    'table' => 'settings',

    'preload_all' => true,

    'settings_model' => \Meletisf\Settings\Models\Setting::class,

    'model_processor' => \Meletisf\Settings\ModelProcessor::class,
];
