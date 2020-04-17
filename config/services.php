<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => '376310558506-req3l9p0ktn08rlcgu3h3508o2la0a04.apps.googleusercontent.com',
        'client_secret' => 'cymIDHS_aZKGi-pZNU3_hjhs',
        'redirect' => 'https://mlijo.id/google/callback',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'exchange-api' => [
        'default' => 'fixer',
        'fixer' => [
            'paid_account' => false,
            'key' => env('fixer_api_key'),
            'class' => 'Webkul\Core\Helpers\Exchange\FixerExchange'
        ]
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ]
];
