<?php
return [
    'default' => 'default',

    'projects' => [
        'default' => [
            'credentials' => [
                'file' => env('FIREBASE_CREDENTIALS'),
            ],
            'database_uri' => env('FIREBASE_DATABASE_URI', ''),
        ],
    ],

    'messaging' => [
        'default' => [
            'sender_id' => env('FCM_SENDER_ID'),
            'server_key' => env('FCM_SERVER_KEY'),
        ],
    ],
];
