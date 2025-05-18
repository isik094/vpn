<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Freekassa
    |--------------------------------------------------------------------------
    |
    | Freekassa payment data
    |
    */
    'api_key' => env('FREEKASSA_API_KEY'),
    'merchant_id' => env('FREEKASSA_MERCHANT_ID'),
    'secret_key_1' => env('FREEKASSA_SECRET_KEY_1'),
    'secret_key_2' => env('FREEKASSA_SECRET_KEY_2'),
];
