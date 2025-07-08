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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', 'http://localhost:8000/auth/google/callback'),
    ],

    'hubspot' => [
        'api_key' => env('HUBSPOT_API_KEY'),
        'client_id' => env('HUBSPOT_CLIENT_ID'),
        'client_secret' => env('HUBSPOT_CLIENT_SECRET'),
        'redirect_uri' => env('HUBSPOT_REDIRECT_URI'),
        'base_url' => 'https://api.hubapi.com',
    ],
];
