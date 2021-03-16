<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'line' => [
        'client_id'     => env('LINE_CLIENT_ID'),
        'client_secret' => env('LINE_CLIENT_SECRET'),
        'redirect'      => env('LINE_CLIENT_REDIRECT'),
        'bot_link_url'  => env('LINE_BOT_LINK_URL'),
        'bot_token'     => env('LINE_BOT_TOKEN'),
    ],

    'telegram' => [
        'client_id'     => env('TELEGRAM_CLIENT_ID'),
        'client_secret' => env('TELEGRAM_CLIENT_SECRET'),
        'redirect'      => env('TELEGRAM_CLIENT_REDIRECT'),
        'bot_link_url'  => env('TELEGRAM_BOT_LINK_URL'),
        'bot_token'     => env('TELEGRAM_CLIENT_SECRET'),
        'widget_src'     => env('TELEGRAM_WIDGET_SRC'),
        'request_access'     => env('TELEGRAM_REQUEST_ACCESS', null),
    ],

    'data_api_token' => env('DATA_API_TOKEN', null),
    'data_api_secret' => env('DATA_API_SECRET', null),

];
