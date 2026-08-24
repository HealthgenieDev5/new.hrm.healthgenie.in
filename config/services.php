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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'machine_1_api' => [
        'url' => env('MACHINE_1_API_URL'),
        'corporate_id' => env('MACHINE_1_CORPORATE_ID'),
        'username' => env('MACHINE_1_USERNAME'),
        'password' => env('MACHINE_1_PASSWORD'),
    ],

    'machine_2_api' => [
        'url' => env('MACHINE_2_API_URL'),
        'corporate_id' => env('MACHINE_2_CORPORATE_ID'),
        'username' => env('MACHINE_2_USERNAME'),
        'password' => env('MACHINE_2_PASSWORD'),
    ],

    'machine_3_api' => [
        'url' => env('MACHINE_3_API_URL'),
        'corporate_id' => env('MACHINE_3_CORPORATE_ID'),
        'username' => env('MACHINE_3_USERNAME'),
        'password' => env('MACHINE_3_PASSWORD'),
    ],

    'head_office_machine_api' => [
        'url' => env('HEAD_OFFICE_MACHINE_API_URL'),
        'corporate_id' => env('HEAD_OFFICE_MACHINE_CORPORATE_ID'),
        'username' => env('HEAD_OFFICE_MACHINE_USERNAME'),
        'password' => env('HEAD_OFFICE_MACHINE_PASSWORD'),
    ],

];
