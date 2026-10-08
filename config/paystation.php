<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PayStation (Bangladesh) Payment Gateway
    |--------------------------------------------------------------------------
    |
    | PAYSTATION_ENABLED turns the online payment button on/off.
    | PAYSTATION_MODE: "sandbox" for testing, "live" for real payments.
    |
    */

    'enabled' => filter_var(env('PAYSTATION_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'mode' => env('PAYSTATION_MODE', 'sandbox') === 'live' ? 'live' : 'sandbox',

    'merchant_id' => env('PAYSTATION_MERCHANT_ID'),

    'password' => env('PAYSTATION_PASSWORD'),

    'currency' => env('PAYSTATION_CURRENCY', 'BDT'),

    // 1 = customer bears the gateway charge, 0 = merchant bears it.
    'pay_with_charge' => (int) env('PAYSTATION_PAY_WITH_CHARGE', 0),

    'timeout' => (int) env('PAYSTATION_TIMEOUT', 30),

    'base_urls' => [
        'sandbox' => 'https://sandbox.paystation.com.bd',
        'live' => 'https://api.paystation.com.bd',
    ],

];
