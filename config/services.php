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

    // AI Services
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'api_url' => env('OPENAI_API_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-4o'),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 2000),
    ],

    'claude' => [
        'api_key' => env('CLAUDE_API_KEY'),
        'api_url' => env('CLAUDE_API_URL', 'https://api.anthropic.com/v1'),
        'model' => env('CLAUDE_MODEL', 'claude-3-sonnet-20240229'),
        'temperature' => env('CLAUDE_TEMPERATURE', 0.7),
        'max_tokens' => env('CLAUDE_MAX_TOKENS', 2000),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'api_url' => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'model' => env('GEMINI_MODEL', 'gemini-pro'),
        'temperature' => env('GEMINI_TEMPERATURE', 0.7),
        'max_tokens' => env('GEMINI_MAX_TOKENS', 2000),
    ],

    'mistral' => [
        'api_key' => env('MISTRAL_API_KEY'),
        'api_url' => env('MISTRAL_API_URL', 'https://api.mistral.ai/v1'),
        'model' => env('MISTRAL_MODEL', 'mistral-large-latest'),
        'temperature' => env('MISTRAL_TEMPERATURE', 0.7),
        'max_tokens' => env('MISTRAL_MAX_TOKENS', 2000),
    ],

    'cohere' => [
        'api_key' => env('COHERE_API_KEY'),
        'api_url' => env('COHERE_API_URL', 'https://api.cohere.ai/v1'),
        'model' => env('COHERE_MODEL', 'command'),
        'temperature' => env('COHERE_TEMPERATURE', 0.7),
        'max_tokens' => env('COHERE_MAX_TOKENS', 2000),
    ],

    // Analytics
    'google_analytics' => [
        'enabled' => env('GOOGLE_ANALYTICS_ENABLED', false),
        'measurement_id' => env('GOOGLE_ANALYTICS_MEASUREMENT_ID'),
        'api_secret' => env('GOOGLE_ANALYTICS_API_SECRET'),
        'property_id' => env('GOOGLE_ANALYTICS_PROPERTY_ID'),
    ],

    // Monitoring
    'sentry' => [
        'enabled' => env('SENTRY_ENABLED', true),
        'dsn' => env('SENTRY_DSN'),
        'environment' => env('SENTRY_ENVIRONMENT', env('APP_ENV', 'production')),
        'release' => env('SENTRY_RELEASE', 'carwise-ai@1.0.0'),
        'sample_rate' => env('SENTRY_SAMPLE_RATE', 1.0),
        'traces_sample_rate' => env('SENTRY_TRACES_SAMPLE_RATE', 0.1),
    ],

    'newrelic' => [
        'enabled' => env('NEWRELIC_ENABLED', false),
        'api_key' => env('NEWRELIC_API_KEY'),
        'account_id' => env('NEWRELIC_ACCOUNT_ID'),
        'license_key' => env('NEWRELIC_LICENSE_KEY'),
        'app_name' => env('NEWRELIC_APP_NAME', 'CarWise.ai'),
    ],

    // Email Services
    'sendgrid' => [
        'enabled' => env('SENDGRID_ENABLED', false),
        'api_key' => env('SENDGRID_API_KEY'),
        'from_email' => env('SENDGRID_FROM_EMAIL', 'noreply@carwise.ai'),
        'from_name' => env('SENDGRID_FROM_NAME', 'CarWise.ai'),
        'reply_to' => env('SENDGRID_REPLY_TO', 'support@carwise.ai'),
    ],

    'mailgun' => [
        'enabled' => env('MAILGUN_ENABLED', false),
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'from_email' => env('MAILGUN_FROM_EMAIL', 'noreply@carwise.ai'),
        'from_name' => env('MAILGUN_FROM_NAME', 'CarWise.ai'),
    ],

    // Push Notifications
    'firebase' => [
        'enabled' => env('FIREBASE_ENABLED', false),
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID'),
        'private_key' => env('FIREBASE_PRIVATE_KEY'),
        'client_email' => env('FIREBASE_CLIENT_EMAIL'),
        'client_id' => env('FIREBASE_CLIENT_ID'),
        'auth_uri' => env('FIREBASE_AUTH_URI', 'https://accounts.google.com/o/oauth2/auth'),
        'token_uri' => env('FIREBASE_TOKEN_URI', 'https://oauth2.googleapis.com/token'),
        'auth_provider_x509_cert_url' => env('FIREBASE_AUTH_PROVIDER_X509_CERT_URL', 'https://www.googleapis.com/oauth2/v1/certs'),
        'client_x509_cert_url' => env('FIREBASE_CLIENT_X509_CERT_URL'),
        'server_key' => env('FIREBASE_SERVER_KEY'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'vapid_key' => env('FIREBASE_VAPID_KEY'),
    ],

    'onesignal' => [
        'enabled' => env('ONESIGNAL_ENABLED', false),
        'app_id' => env('ONESIGNAL_APP_ID'),
        'rest_api_key' => env('ONESIGNAL_REST_API_KEY'),
        'user_auth_key' => env('ONESIGNAL_USER_AUTH_KEY'),
        'safari_web_id' => env('ONESIGNAL_SAFARI_WEB_ID'),
    ],

    // Real-time Communication
    'pusher' => [
        'enabled' => env('PUSHER_ENABLED', false),
        'app_id' => env('PUSHER_APP_ID'),
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'cluster' => env('PUSHER_APP_CLUSTER', 'us2'),
        'use_tls' => env('PUSHER_USE_TLS', true),
        'host' => env('PUSHER_HOST'),
        'port' => env('PUSHER_PORT', 443),
        'scheme' => env('PUSHER_SCHEME', 'https'),
    ],

    'websocket' => [
        'enabled' => env('WEBSOCKET_ENABLED', false),
        'driver' => env('WEBSOCKET_DRIVER', 'pusher'),
        'host' => env('WEBSOCKET_HOST', 'localhost'),
        'port' => env('WEBSOCKET_PORT', 6001),
        'ssl' => env('WEBSOCKET_SSL', false),
        'auth_endpoint' => env('WEBSOCKET_AUTH_ENDPOINT', '/broadcasting/auth'),
    ],

    // Car Manufacturer APIs
    'bmw' => [
        'enabled' => env('BMW_API_ENABLED', false),
        'api_key' => env('BMW_API_KEY'),
        'base_url' => env('BMW_API_BASE_URL', 'https://api.bmw.com'),
    ],

    'mercedes' => [
        'enabled' => env('MERCEDES_API_ENABLED', false),
        'api_key' => env('MERCEDES_API_KEY'),
        'base_url' => env('MERCEDES_API_BASE_URL', 'https://api.mercedes-benz.com'),
    ],

    'volkswagen' => [
        'enabled' => env('VOLKSWAGEN_API_ENABLED', false),
        'api_key' => env('VOLKSWAGEN_API_KEY'),
        'base_url' => env('VOLKSWAGEN_API_BASE_URL', 'https://api.volkswagen.com'),
    ],

    'audi' => [
        'enabled' => env('AUDI_API_ENABLED', false),
        'api_key' => env('AUDI_API_KEY'),
        'base_url' => env('AUDI_API_BASE_URL', 'https://api.audi.com'),
    ],

    'ford' => [
        'enabled' => env('FORD_API_ENABLED', false),
        'api_key' => env('FORD_API_KEY'),
        'base_url' => env('FORD_API_BASE_URL', 'https://api.ford.com'),
    ],

    'toyota' => [
        'enabled' => env('TOYOTA_API_ENABLED', false),
        'api_key' => env('TOYOTA_API_KEY'),
        'base_url' => env('TOYOTA_API_BASE_URL', 'https://api.toyota.com'),
    ],

    'volvo' => [
        'enabled' => env('VOLVO_API_ENABLED', false),
        'api_key' => env('VOLVO_API_KEY'),
        'base_url' => env('VOLVO_API_BASE_URL', 'https://api.volvo.com'),
    ],

    'tesla' => [
        'enabled' => env('TESLA_API_ENABLED', false),
        'api_key' => env('TESLA_API_KEY'),
        'base_url' => env('TESLA_API_BASE_URL', 'https://api.tesla.com'),
    ],

    // Multi-brand Platform APIs
    'smartcar' => [
        'enabled' => env('SMARTCAR_ENABLED', false),
        'client_id' => env('SMARTCAR_CLIENT_ID'),
        'client_secret' => env('SMARTCAR_CLIENT_SECRET'),
        'redirect_uri' => env('SMARTCAR_REDIRECT_URI'),
        'base_url' => env('SMARTCAR_BASE_URL', 'https://api.smartcar.com'),
    ],

    'high_mobility' => [
        'enabled' => env('HIGH_MOBILITY_ENABLED', false),
        'api_key' => env('HIGH_MOBILITY_API_KEY'),
        'base_url' => env('HIGH_MOBILITY_BASE_URL', 'https://api.high-mobility.com'),
    ],

    'otonomo' => [
        'enabled' => env('OTONOMO_ENABLED', false),
        'api_key' => env('OTONOMO_API_KEY'),
        'base_url' => env('OTONOMO_BASE_URL', 'https://api.otonomo.io'),
    ],

    'wejo' => [
        'enabled' => env('WEJO_ENABLED', false),
        'api_key' => env('WEJO_API_KEY'),
        'base_url' => env('WEJO_BASE_URL', 'https://api.wejo.com'),
    ],

    'motordata' => [
        'enabled' => env('MOTORDATA_ENABLED', false),
        'api_key' => env('MOTORDATA_API_KEY'),
        'base_url' => env('MOTORDATA_BASE_URL', 'https://api.motordata.com'),
    ],

    'carapi' => [
        'enabled' => env('CARAPI_ENABLED', false),
        'api_key' => env('CARAPI_API_KEY'),
        'base_url' => env('CARAPI_BASE_URL', 'https://api.carapi.app'),
    ],

    // Parts Marketplace APIs
    'ebay_motors' => [
        'enabled' => env('EBAY_MOTORS_ENABLED', false),
        'app_id' => env('EBAY_MOTORS_APP_ID'),
        'client_id' => env('EBAY_MOTORS_CLIENT_ID'),
        'client_secret' => env('EBAY_MOTORS_CLIENT_SECRET'),
        'base_url' => env('EBAY_MOTORS_BASE_URL', 'https://api.ebay.com'),
        'sandbox_url' => env('EBAY_MOTORS_SANDBOX_URL', 'https://api.sandbox.ebay.com'),
    ],

    'amazon_paapi' => [
        'enabled' => env('AMAZON_PAAPI_ENABLED', false),
        'access_key' => env('AMAZON_PAAPI_ACCESS_KEY'),
        'secret_key' => env('AMAZON_PAAPI_SECRET_KEY'),
        'partner_tag' => env('AMAZON_PAAPI_PARTNER_TAG'),
        'host' => env('AMAZON_PAAPI_HOST', 'webservices.amazon.com'),
        'region' => env('AMAZON_PAAPI_REGION', 'us-east-1'),
    ],

    'autozone' => [
        'enabled' => env('AUTOZONE_ENABLED', false),
        'api_key' => env('AUTOZONE_API_KEY'),
        'base_url' => env('AUTOZONE_BASE_URL', 'https://api.autozone.com'),
    ],

    'rockauto' => [
        'enabled' => env('ROCKAUTO_ENABLED', false),
        'api_key' => env('ROCKAUTO_API_KEY'),
        'base_url' => env('ROCKAUTO_BASE_URL', 'https://api.rockauto.com'),
    ],

    'partsgeek' => [
        'enabled' => env('PARTSGEEK_ENABLED', false),
        'api_key' => env('PARTSGEEK_API_KEY'),
        'base_url' => env('PARTSGEEK_BASE_URL', 'https://api.partsgeek.com'),
    ],

    /*
    | MarketCheck Cars API — inventory search (e.g. /v2/search/car/active).
    | Docs: https://docs.marketcheck.com/docs/api/cars/inventory/inventory-search
    | Sign up: https://www.marketcheck.com/apis/cars/
    */
    'marketcheck' => [
        'enabled' => filter_var(env('MARKETCHECK_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'api_key' => env('MARKETCHECK_API_KEY'),
        'inventory_photo_links' => filter_var(
            env('MARKETCHECK_INVENTORY_PHOTO_LINKS', false),
            FILTER_VALIDATE_BOOLEAN
        ),
        'base_url' => env('MARKETCHECK_BASE_URL', 'https://api.marketcheck.com'),
        'zip' => env('MARKETCHECK_ZIP'),
        'latitude' => env('MARKETCHECK_LATITUDE'),
        'longitude' => env('MARKETCHECK_LONGITUDE'),
        'radius' => env('MARKETCHECK_RADIUS', 100),
        'rows' => env('MARKETCHECK_FEATURED_ROWS', 20),
        'cache_ttl' => env('MARKETCHECK_CACHE_TTL', 21600),
        'country' => env('MARKETCHECK_COUNTRY', 'us'),
        'carousel_car_type' => env('MARKETCHECK_CAROUSEL_CAR_TYPE'),
        'search_make' => env('MARKETCHECK_SEARCH_MAKE'),
        'search_model' => env('MARKETCHECK_SEARCH_MODEL'),
        'price_range' => env('MARKETCHECK_PRICE_RANGE'),
        'price_max' => env('MARKETCHECK_PRICE_MAX'),
        'carousel_database_fallback' => filter_var(
            env(
                'MARKETCHECK_CAROUSEL_DATABASE_FALLBACK',
                env('APP_ENV', 'production') === 'local'
            ),
            FILTER_VALIDATE_BOOLEAN
        ),
        'cache_empty_ttl' => env('MARKETCHECK_CACHE_EMPTY_TTL', 600),
        'luxury_showcase' => filter_var(env('MARKETCHECK_LUXURY_SHOWCASE', false), FILTER_VALIDATE_BOOLEAN),
        'luxury_price_range' => env('MARKETCHECK_LUXURY_PRICE_RANGE', '125000-10000000'),
        'luxury_makes' => env(
            'MARKETCHECK_LUXURY_MAKES',
            'Mercedes-Benz,BMW,Porsche,Ferrari,Lamborghini,Bentley,Rolls-Royce,Aston Martin,McLaren'
        ),
        'outbound_token_ttl_hours' => env('MARKETCHECK_OUTBOUND_TOKEN_TTL', 72),
    ],

];
