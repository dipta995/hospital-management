<?php

/*
|--------------------------------------------------------------------------
| Subdomain → Database tenants
|--------------------------------------------------------------------------
| Edit this file to add or change hospital tenants.
| Host subdomain (first part of domain) maps to a MySQL connection.
|
| Example: alsunnah.yourdomain.com → uses the "alsunnah" entry below.
| localhost / 127.0.0.1 → uses "127" or falls back to "default".
*/

return [
    'subdomain' => [
        'alsunnah' => [
            'database' => 'dreammak_alsunnah',
            'username' => 'dreammak_alsunnah',
            'password' => 'dreammak_alsunnah',
            'host' => '127.0.0.1',
        ],
        'rebeka' => [
            'database' => 'dreammak_rebeka',
            'username' => 'dreammak_rebeka',
            'password' => 'dreammak_rebeka',
            'host' => '127.0.0.1',
        ],
        '127' => [
            'database' => env('DB_DATABASE', 'hospital_management'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'host' => env('DB_HOST', '127.0.0.1'),
        ],
        // Add more tenants here...
    ],

    'default' => [
        'database' => env('DB_DATABASE', 'hospital_management'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'host' => env('DB_HOST', '127.0.0.1'),
    ],
];
