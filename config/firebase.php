<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure Firebase settings for your application.
    | You need to get these values from your Firebase Console.
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID', ''),
    'database_url' => env('FIREBASE_DATABASE_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase Service Account Key (for FCM API v1)
    |--------------------------------------------------------------------------
    |
    | Service account key for Firebase Admin SDK authentication.
    | Download from Firebase Console → Project Settings → Service Accounts
    |
    */

    'service_account_key' => [
        'type' => env('FIREBASE_SERVICE_ACCOUNT_TYPE', 'service_account'),
        'project_id' => env('FIREBASE_PROJECT_ID', ''),
        'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID', ''),
        'private_key' => env('FIREBASE_PRIVATE_KEY', ''),
        'client_email' => env('FIREBASE_CLIENT_EMAIL', ''),
        'client_id' => env('FIREBASE_CLIENT_ID', ''),
        'auth_uri' => env('FIREBASE_AUTH_URI', 'https://accounts.google.com/o/oauth2/auth'),
        'token_uri' => env('FIREBASE_TOKEN_URI', 'https://oauth2.googleapis.com/token'),
        'auth_provider_x509_cert_url' => env('FIREBASE_AUTH_PROVIDER_X509_CERT_URL', 'https://www.googleapis.com/oauth2/v1/certs'),
        'client_x509_cert_url' => env('FIREBASE_CLIENT_X509_CERT_URL', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Legacy Server Key (for backward compatibility)
    |--------------------------------------------------------------------------
    |
    | Server key for legacy FCM API (deprecated but still supported)
    |
    */

    'server_key' => env('FIREBASE_SERVER_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging (FCM)
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Cloud Messaging
    |
    */

    'fcm' => [
        'default_channel_id' => 'bengkelsampah_channel',
        'default_sound' => 'default',
        'default_priority' => 'high',
    ],
];
