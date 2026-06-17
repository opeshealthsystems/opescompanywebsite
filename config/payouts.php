<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payout Driver
    |--------------------------------------------------------------------------
    |
    | Which payout gateway processes practitioner compensation for paid testing
    | programmes. "manual" records payouts that an admin settles offline (the
    | current behaviour). To go live with a real rail, implement the relevant
    | driver (see app/Services/Payouts) and switch this to 'mtn_momo' or
    | 'orange_money', then supply the credentials below.
    |
    | Supported: "manual", "mtn_momo", "orange_money"
    |
    */

    'driver' => env('PAYOUT_DRIVER', 'manual'),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    */

    'currency' => env('PAYOUT_CURRENCY', 'XAF'),

    /*
    |--------------------------------------------------------------------------
    | Provider Credentials
    |--------------------------------------------------------------------------
    |
    | Left empty by design. The real provider drivers are stubs until a rail is
    | chosen and these are populated (sandbox first). Claude does not handle
    | live financial credentials — set these yourself in .env.
    |
    */

    'mtn_momo' => [
        'base_url'         => env('MTN_MOMO_BASE_URL'),
        'subscription_key' => env('MTN_MOMO_SUBSCRIPTION_KEY'),
        'api_user'         => env('MTN_MOMO_API_USER'),
        'api_key'          => env('MTN_MOMO_API_KEY'),
        'environment'      => env('MTN_MOMO_ENVIRONMENT', 'sandbox'),
    ],

    'orange_money' => [
        'base_url'      => env('ORANGE_MONEY_BASE_URL'),
        'client_id'     => env('ORANGE_MONEY_CLIENT_ID'),
        'client_secret' => env('ORANGE_MONEY_CLIENT_SECRET'),
        'merchant_key'  => env('ORANGE_MONEY_MERCHANT_KEY'),
        'environment'   => env('ORANGE_MONEY_ENVIRONMENT', 'sandbox'),
    ],

];
