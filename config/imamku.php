<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum number of imams allowed in the system
    |--------------------------------------------------------------------------
    */
    'max_imams' => env('IMAMKU_MAX_IMAMS', 5),

    /*
    |--------------------------------------------------------------------------
    | Aladhan API Configuration (Prayer Times)
    |--------------------------------------------------------------------------
    */
    'aladhan' => [
        'base_url' => 'https://api.aladhan.com/v1',
        'city' => env('ALADHAN_CITY', 'Jakarta'),
        'country' => env('ALADHAN_COUNTRY', 'Indonesia'),
        'method' => env('ALADHAN_METHOD', 20), // 20 = Kemenag RI
    ],

    /*
    |--------------------------------------------------------------------------
    | Fonnte WhatsApp API Configuration
    |--------------------------------------------------------------------------
    */
    'fonnte' => [
        'api_url' => 'https://api.fonnte.com/send',
        'api_key' => env('FONNTE_API_KEY', ''),
        'device_id' => env('FONNTE_DEVICE_ID', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Reminders (in minutes before prayer time)
    |--------------------------------------------------------------------------
    */
    'reminders' => [60, 30],

    /*
    |--------------------------------------------------------------------------
    | Minimum hours before prayer time for swap requests
    |--------------------------------------------------------------------------
    */
    'swap_min_hours' => 2,
];
