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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'api_url' => env('OPENAI_API_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-4o'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 2048),
        'temperature' => env('OPENAI_TEMPERATURE', 0.3),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'max_tokens' => env('GEMINI_MAX_TOKENS', 2048),
        'temperature' => env('GEMINI_TEMPERATURE', 0.3),
    ],

    'claude' => [
        'api_key' => env('CLAUDE_API_KEY'),
        'base_url' => env('CLAUDE_BASE_URL', 'https://api.anthropic.com/v1'),
        'model' => env('CLAUDE_MODEL', 'claude-3-5-sonnet-20241022'),
        'max_tokens' => env('CLAUDE_MAX_TOKENS', 3000),
        'temperature' => env('CLAUDE_TEMPERATURE', 0.3),
    ],

    'cohere' => [
        'api_key' => env('COHERE_API_KEY'),
        'base_url' => env('COHERE_BASE_URL', 'https://api.cohere.ai/v1'),
        'model' => env('COHERE_MODEL', 'command-r-plus'),
        'max_tokens' => env('COHERE_MAX_TOKENS', 2500),
        'temperature' => env('COHERE_TEMPERATURE', 0.3),
    ],

    'mistral' => [
        'api_key' => env('MISTRAL_API_KEY'),
        'base_url' => env('MISTRAL_BASE_URL', 'https://api.mistral.ai/v1'),
        'model' => env('MISTRAL_MODEL', 'mistral-large-latest'),
        'max_tokens' => env('MISTRAL_MAX_TOKENS', 2500),
        'temperature' => env('MISTRAL_TEMPERATURE', 0.3),
    ],

    'carapi' => [
        'token' => env('CARAPI_TOKEN'),
        'secret' => env('CARAPI_SECRET'),
        'base_url' => env('CARAPI_BASE_URL', 'https://carapi.app/api'),
        'enabled' => env('CARAPI_ENABLED', false),
    ],

];
