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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    /*
    |--------------------------------------------------------------------------
    | AI Chat Assistant (Google Gemini)
    |--------------------------------------------------------------------------
    |
    | Uses the official Gemini API method `models.generateContent`
    | (POST {base_url}/{api_version}/models/{model}:generateContent).
    |
    | The API key is read from the environment only and is never exposed to
    | the browser. If no key is configured, the chat endpoint reports that
    | the AI assistant is unavailable instead of inventing an answer.
    |
    | `GEMINI_MODEL` must be a model identifier currently returned by
    | https://generativelanguage.googleapis.com/v1beta/models (e.g.
    | gemini-3.8-flash, gemini-flash-latest). A model that does not exist
    | makes the API answer 404 NOT_FOUND.
    |
    */

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-3.8-flash'),
        'api_version' => env('GEMINI_API_VERSION', 'v1beta'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com'),
        'timeout' => (int) env('GEMINI_TIMEOUT', env('AI_TIMEOUT', 30)),

        // Conversation bounds (protects tokens, latency, and quota).
        'history_limit' => (int) env('GEMINI_HISTORY_LIMIT', 12),
        'max_output_tokens' => (int) env('GEMINI_MAX_OUTPUT_TOKENS', 1024),
        'temperature' => (float) env('GEMINI_TEMPERATURE', 0.7),
        'thinking_budget' => (int) env('GEMINI_THINKING_BUDGET', 0),

        // Transient-error retries: one extra attempt by default, never while
        // Google asks us to wait longer than retry_max_delay seconds.
        'retry_attempts' => (int) env('GEMINI_RETRY_ATTEMPTS', 2),
        'retry_max_delay' => (int) env('GEMINI_RETRY_MAX_DELAY', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Chat Assistant (Groq, primary provider)
    |--------------------------------------------------------------------------
    |
    | Uses Groq's OpenAI-compatible API
    | (POST {base_url}/chat/completions). Free tier only: no billing is
    | required and none is enabled by this application.
    |
    | The API key is read from the environment only and is never exposed to
    | the browser, the logs, or error messages.
    |
    | `GROQ_MODEL` must be a model id this key can actually use; check
    | GET https://api.groq.com/openai/v1/models or the Groq console. On the
    | `openai/gpt-oss-20b` is Groq's recommended replacement for the
    | decommissioned `llama-3.1-8b-instant`. A model that is unavailable to
    | this key makes the API answer 404 model_not_found.
    |
    */

    'groq' => [
        'key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'openai/gpt-oss-20b'),
        'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
        'timeout' => (int) env('AI_TIMEOUT', env('GROQ_TIMEOUT', 30)),

        // Conversation bounds (protects tokens, latency, and quota).
        'history_limit' => (int) env('AI_HISTORY_LIMIT', 12),
        'max_output_tokens' => (int) env('AI_MAX_OUTPUT_TOKENS', 1024),
        'temperature' => (float) env('AI_TEMPERATURE', 0.7),

        // Only for reasoning models (e.g. openai/gpt-oss-*): low|medium|high.
        // Empty means "do not send the field".
        'reasoning_effort' => env('GROQ_REASONING_EFFORT', ''),

        // Transient-error retries: one extra attempt by default, never while
        // the provider asks us to wait longer than retry_max_delay seconds.
        'retry_attempts' => (int) env('AI_RETRY_ATTEMPTS', 2),
        'retry_max_delay' => (int) env('AI_RETRY_MAX_DELAY', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Provider strategy
    |--------------------------------------------------------------------------
    |
    | default:  provider tried first ("groq" or "gemini").
    | fallback: tried after the primary's bounded retries fail, or immediately
    |           for a non-retryable error. A transient failure that recovers
    |           during a retry does not consume the fallback provider's quota.
    |
    */

    'ai' => [
        'default' => strtolower((string) env('AI_PROVIDER', 'groq')),
        'fallback' => strtolower((string) env('AI_FALLBACK_PROVIDER', 'gemini')),
    ],

];
