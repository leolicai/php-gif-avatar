<?php
/**
 * application.config.php
 *
 */

return [

    'application' => [
        'env' => 'production',
    ],

    'modules' => require __DIR__ . '/modules.config.php',

    'module_listener_options' => [

        'module_paths' => [
            realpath(__DIR__ . '/../module'),
            realpath(__DIR__ . '/../vendor'),
        ],

        'config_glob_paths' => [
            __DIR__ . '/autoload/{{,*.}global,{,*.}local}.php',
        ],

        'config_cache_enabled' => true,

        'config_cache_key' => 'application.config.cache',

        'module_map_cache_enabled' => true,

        'module_map_cache_key' => 'application.module.cache',

        'cache_dir' => realpath(__DIR__ . '/../data/cache/'),
    ],
];
