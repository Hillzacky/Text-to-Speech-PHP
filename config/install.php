<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server Requirements
    |--------------------------------------------------------------------------
    */
    'php_version' => '7.4',

    'extensions' => [
        'php' => [
            'Ctype',
            'Fileinfo',
            'JSON',
            'Mbstring',
            'OpenSSL',
            'PDO',
            'XML',
            'GD',
            'cURL'
        ],
        'apache' => [
            'mod_rewrite',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File permissions
    |--------------------------------------------------------------------------
    */
    'permissions' => [
        'Files' => [
            '.env',
        ],
        'Folders' =>
        [
            'bootstrap/cache',
            'public/uploads',
            'resources/lang',
            'storage',
            'storage/framework/',
            'storage/framework/cache',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
        ],
    ]
];
