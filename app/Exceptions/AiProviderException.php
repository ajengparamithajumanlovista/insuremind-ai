<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A generative AI provider (Groq, Gemini) did not produce a usable answer.
 *
 * Shared base class so `ChatAssistantService` can handle every provider the
 * same way, while the frontend still receives one stable, whitelisted set of
 * `reason()` codes that it maps to localized notices.
 *
 * The message is safe to log server-side (it never contains the API key,
 * authorization headers, or user conversation content).
 */
class AiProviderException extends RuntimeException
{
    /** No API key configured on the server for this provider. */
    public const NOT_CONFIGURED = 'not_configured';

    /** API key rejected (401/403, or Google's API_KEY_INVALID). */
    public const INVALID_CREDENTIALS = 'invalid_credentials';

    /** Configured model name does not exist for this key (404 NOT_FOUND). */
    public const MODEL_NOT_FOUND = 'model_not_found';

    /** Request rejected because of its content or format (400s, 413, 422). */
    public const INVALID_REQUEST = 'invalid_request';

    /** Short rate limit (429): waiting a few seconds helps. */
    public const RATE_LIMITED = 'rate_limited';

    /**
     * The provider's daily (or total) quota is spent: waiting a few seconds
     * does not help, so the UI must say so instead of "retry in a moment".
     */
    public const QUOTA_EXHAUSTED = 'quota_exhausted';

    /** Provider returned 5xx (temporary service problem). */
    public const UNAVAILABLE = 'unavailable';

    /** Network failure, DNS error, or timeout. */
    public const NETWORK = 'network';

    /** Provider answered, but with no text, a blocked prompt, or a truncated reply. */
    public const EMPTY_RESPONSE = 'empty_response';

    /** Any other failure. */
    public const UNKNOWN = 'unknown';

    /**
     * @param  string  $provider  Short provider id ("groq", "gemini").
     * @param  array<string, mixed>  $context  Safe diagnostic data (no secrets).
     */
    public function __construct(
        string $message,
        private readonly string $reason = self::UNKNOWN,
        private readonly array $context = [],
        ?\Throwable $previous = null,
        private readonly string $provider = 'ai',
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function reason(): string
    {
        return $this->reason;
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return $this->context;
    }

    /**
     * Provider id that failed ("groq", "gemini"), for logs and the UI only.
     */
    public function provider(): string
    {
        return $this->provider;
    }

    /**
     * Whether retrying the same request later may succeed.
     *
     * An exhausted daily quota is deliberately excluded: retrying within this
     * request cannot help, and pretending otherwise wastes time and quota.
     */
    public function isRetryable(): bool
    {
        return in_array($this->reason, [self::RATE_LIMITED, self::UNAVAILABLE, self::NETWORK], true);
    }

    /**
     * Seconds the provider asked us to wait, when it said so.
     */
    public function retryAfterSeconds(): ?int
    {
        $delay = $this->context['retry_after_seconds'] ?? null;

        return is_int($delay) && $delay > 0 ? $delay : null;
    }
}
