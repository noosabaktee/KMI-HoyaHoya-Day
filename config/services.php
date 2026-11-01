<?php

return [
    'postmark' => ['token' => env('POSTMARK_TOKEN')],
    'ses' => ['key' => env('AWS_ACCESS_KEY_ID'), 'secret' => env('AWS_SECRET_ACCESS_KEY'), 'region' => env('AWS_DEFAULT_REGION', 'us-east-1')],
    'slack' => ['notifications' => ['bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'), 'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL')]],
    
    'mpwa' => [
        'server' => env('MPWA_SERVER', 'https://whatsapp.intconnect.id'),
        'api_key' => env('MPWA_API_KEY', '####'),
        'sender' => env('MPWA_SENDER', '62888xxxx'),
        'footer' => env('MPWA_FOOTER', 'Sent via KMI Hoya-Hoya Day Festival'),
    ],
];
