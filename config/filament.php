<?php

return [
    'path' => env('ADMIN_PANEL_PATH', 'admin'),

    'domain' => env('ADMIN_PANEL_DOMAIN'),

    'brand' => env('APP_NAME', 'Epic Explore') . ' Admin',

    'auth' => [
        'guard' => 'admin',
        'provider' => 'admins',
    ],

    'pages' => [
        'registration' => [
            'enabled' => false,
        ],
        'password_reset' => [
            'enabled' => true,
        ],
    ],

    'middleware' => [
        'auth' => [
            'admin',
        ],
    ],

    'database' => [
        'notifications' => [
            'enabled' => true,
            'polling_interval' => 30,
            'databases' => [],
        ],
    ],

    'broadcasting' => [
        'enabled' => false,
    ],

    'resources' => [
        'discover' => [
            __DIR__ . '/../app/Filament/Resources',
        ],
    ],

    'widgets' => [
        'discover' => [
            __DIR__ . '/../app/Filament/Widgets',
        ],
    ],

    'pages' => [
        'discover' => [
            __DIR__ . '/../app/Filament/Pages',
        ],
    ],
];
