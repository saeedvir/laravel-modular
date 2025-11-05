<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Path
    |--------------------------------------------------------------------------
    |
    | This value determines the path where modules will be stored.
    | By default, modules are stored in the 'modules' directory at the
    | root of your application.
    |
    */
    'path' => base_path('modules'),

    /*
    |--------------------------------------------------------------------------
    | Disabled Modules
    |--------------------------------------------------------------------------
    |
    | List of modules that should not be loaded. Add module names here
    | to disable them without deleting the module directory.
    |
    */
    'disabled' => [
        // 'ModuleName',
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Enable caching for improved performance. When enabled, module
    | discovery results will be cached.
    |
    */
    'cache' => [
        'enabled' => env('MODULE_CACHE_ENABLED', true),
        'key' => 'laravel_modular_cache',
        'lifetime' => 86400, // 24 hours
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Register Routes
    |--------------------------------------------------------------------------
    |
    | Automatically register web and API routes for modules.
    | Module autoloading is handled by composer merge plugin.
    |
    */
    'auto_register_routes' => true,

    /*
    |--------------------------------------------------------------------------
    | Module Stubs Path
    |--------------------------------------------------------------------------
    |
    | Path to custom stub files for module generation. Leave null to use
    | default stubs.
    |
    */
    'stubs_path' => null,
];
