<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS Notifications
    |--------------------------------------------------------------------------
    |
    | SMS_ENABLED turns SMS on/off.
    | SMS_DRIVER: "bdbulksms" (bdbulksms.net token API), "bulksmsbd"
    | (bulksmsbd.net), "custom" (any GET API using
    | {number} and {message} placeholders in SMS_CUSTOM_URL) or "log"
    | (writes to storage/logs only, for testing).
    | SMS_ADMIN_NUMBERS: comma separated numbers that get payment alerts
    | (falls back to the owner's number when empty).
    |
    */

    'enabled' => filter_var(env('SMS_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'driver' => env('SMS_DRIVER', 'log'),

    'admin_numbers' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) (env('SMS_ADMIN_NUMBERS') ?: '01632315608'))
    ))),

    'timeout' => (int) env('SMS_TIMEOUT', 15),

    'drivers' => [
        'bdbulksms' => [
            'url' => env('BDBULKSMS_URL', 'https://api.bdbulksms.net/api.php?json'),
            'token' => env('BDBULKSMS_TOKEN'),
            'verify_ssl' => filter_var(env('BDBULKSMS_VERIFY_SSL', true), FILTER_VALIDATE_BOOLEAN),
        ],

        'bulksmsbd' => [
            'url' => env('BULKSMSBD_URL', 'http://bulksmsbd.net/api/smsapi'),
            'api_key' => env('BULKSMSBD_API_KEY'),
            'sender_id' => env('BULKSMSBD_SENDER_ID'),
        ],

        'custom' => [
            'url' => env('SMS_CUSTOM_URL'),
        ],
    ],

];
