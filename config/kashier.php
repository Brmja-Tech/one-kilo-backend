<?php

return [
    'enabled' => filter_var(env('KASHIER_ENABLED', false), FILTER_VALIDATE_BOOL),

    // Controls BOTH the API base URL selection and the "mode" field sent to Kashier.
    // Supported: "test", "live"
    'mode' => env('KASHIER_MODE', 'test'),

    'merchant_id' => env('KASHIER_MERCHANT_ID'),
    'api_key' => env('KASHIER_API_KEY'),
    'secret_key' => env('KASHIER_SECRET_KEY'),

    'currency' => env('KASHIER_CURRENCY', 'EGP'),

    'base_urls' => [
        // Kashier API base URLs (kept constant; switching is controlled by KASHIER_MODE).
        'test' => 'https://test-api.kashier.io',
        'live' => 'https://api.kashier.io',
    ],

    // Required by Kashier Payment Sessions.
    'type' => env('KASHIER_TYPE', 'external'),

    // Optional fields used by our integration.
    // If empty, we fall back to internal routes:
    // - Callback: {APP_URL}/api/payments/kashier/callback
    // - Webhook:  {APP_URL}/api/payments/kashier/webhook
    'merchant_redirect_url' => env('KASHIER_MERCHANT_REDIRECT_URL'),
    'server_webhook_url' => env('KASHIER_SERVER_WEBHOOK_URL'),

    // Optional: controls the hosted payment UI language (if supported by Kashier).
    // Leave empty to let Kashier decide.
    'display' => env('KASHIER_DISPLAY'),

    'max_failure_attempts' => (int) env('KASHIER_MAX_FAILURE_ATTEMPTS', 3),
    'session_expire_minutes' => (int) env('KASHIER_SESSION_EXPIRE_MINUTES', 30),
    'callback_redirect_url' => env('KASHIER_CALLBACK_REDIRECT_URL'),
    'webhook_token' => env('KASHIER_WEBHOOK_TOKEN'),

    'timeout' => (int) env('KASHIER_TIMEOUT', 30),

    // Which Laravel log channel to use for Kashier-related logs.
    'log_channel' => env('KASHIER_LOG_CHANNEL', 'kashier'),
    'webhook_log_channel' => env('KASHIER_WEBHOOK_LOG_CHANNEL', 'kashier_webhook'),
];
