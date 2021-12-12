<?php

return [
    /*
     * Configure the name of the table. You may want to override this if it causes conflicts.
     */
    'table' => 'settings',

    /*
     * If this is set to true, it will preload all the settings to the memory.
     * It is recommended for sets smaller than 500 entries.
     */
    'preload_all' => true,

    /*
     * Which class represents the settings model.
     */
    'settings_model' => \Meletisf\Settings\Models\Setting::class,

    /*
     * Override the class which serializes models.
     */
    'model_processor' => \Meletisf\Settings\ModelProcessor::class,

    /*
     * Which settings should always exist in the database.
     * Add them with key => value format.
     */
    'required_configuration' => [

    ],
];
