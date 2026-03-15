<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Integration Categories
    |--------------------------------------------------------------------------
    |
    | Defines the categories of third-party integrations.
    | Each category can have multiple providers.
    |
    */
    'categories' => [
        'payment' => [
            'label' => 'Payment Gateway',
            'description' => 'Payment processing services (Stripe, PromptPay, VNPay)',
            'adapter' => \App\Services\ApiGateway\Adapters\PaymentAdapter::class,
        ],
        'sms' => [
            'label' => 'SMS Service',
            'description' => 'SMS messaging and OTP services',
            'adapter' => \App\Services\ApiGateway\Adapters\SMSAdapter::class,
        ],
        'ai_agent' => [
            'label' => 'AI Agent',
            'description' => 'AI services: chatbot, translation, TTS, call center',
            'adapter' => \App\Services\ApiGateway\Adapters\AiAgentAdapter::class,
        ],
        'cloud_point' => [
            'label' => 'Cloud Point API',
            'description' => 'Reward/loyalty point management',
            'adapter' => \App\Services\ApiGateway\Adapters\CloudPointAdapter::class,
        ],
        'data_exchange' => [
            'label' => 'Data Exchange API',
            'description' => 'Data import/export/sync services',
            'adapter' => \App\Services\ApiGateway\Adapters\DataExchangeAdapter::class,
        ],
        'authorization' => [
            'label' => 'Authorization API',
            'description' => 'External authorization services (OAuth2, RBAC)',
            'adapter' => \App\Services\ApiGateway\Adapters\AuthorizationAdapter::class,
        ],
        'helpdesk' => [
            'label' => 'HelpDesk API',
            'description' => 'Customer support and ticket management',
            'adapter' => \App\Services\ApiGateway\Adapters\HelpDeskAdapter::class,
        ],
        'translate' => [
            'label' => 'Translation API',
            'description' => 'Multi-language translation services',
            'adapter' => \App\Services\ApiGateway\Adapters\TranslateAdapter::class,
        ],
        'tts' => [
            'label' => 'Text-to-Speech API',
            'description' => 'Voice synthesis services',
            'adapter' => \App\Services\ApiGateway\Adapters\TTSAdapter::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Shared Providers
    |--------------------------------------------------------------------------
    |
    | These providers are shared across all clusters.
    | Credentials can still be per-cluster.
    |
    */
    'shared' => ['payment', 'sms', 'ai_agent'],

    /*
    |--------------------------------------------------------------------------
    | API Gateway Settings
    |--------------------------------------------------------------------------
    */
    'gateway' => [
        'timeout' => env('API_GATEWAY_TIMEOUT', 30),
        'retries' => env('API_GATEWAY_RETRIES', 2),
        'log_enabled' => env('API_GATEWAY_LOG', true),
    ],

];
