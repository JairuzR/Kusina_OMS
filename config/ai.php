<?php

return [

    'default_provider' => env('AI_DEFAULT_PROVIDER', 'gemini'),
    'fallback_enabled' => env('AI_FALLBACK_ENABLED', true),
    'fallback_chain'   => ['gemini', 'ollama'],

    'providers' => [
        'gemini' => [
            'api_key'  => env('GEMINI_API_KEY'),
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'model'    => env('GEMINI_MODEL', 'gemini-2.0-flash'),
            'timeout'  => 30,
        ],
        'ollama' => [
            'enabled'  => env('OLLAMA_ENABLED', true),
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'model'    => env('OLLAMA_MODEL', 'llama3.2'),
            'timeout'  => 60,
        ],
    ],

    'features' => [
        'reorder_suggestions' => [
            'provider'    => 'gemini',
            'cache_hours' => 6,
        ],
    ],

];