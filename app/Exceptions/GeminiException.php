<?php

namespace App\Exceptions;

/**
 * A Gemini API call that did not produce a usable answer.
 *
 * A thin Gemini-specific type over {@see AiProviderException} so the rest of
 * the app can treat every generative provider uniformly, while Gemini keeps
 * its own class name for clarity at the call site.
 *
 * The message is safe to log server-side (it never contains the API key,
 * authorization headers, or user conversation content). `reason()` returns a
 * stable, whitelisted code that the frontend maps to a localized notice.
 *
 * The constants are re-declared here (same values as the parent) because
 * `GeminiException::RATE_LIMITED` etc. are referenced directly by the views
 * and the controller.
 */
class GeminiException extends AiProviderException
{
    /** No API key configured on the server. */
    public const NOT_CONFIGURED = 'not_configured';

    /** API key rejected (400 API_KEY_INVALID / 403 PERMISSION_DENIED). */
    public const INVALID_CREDENTIALS = 'invalid_credentials';

    /** Configured model name does not exist (404 NOT_FOUND). */
    public const MODEL_NOT_FOUND = 'model_not_found';

    /** Request rejected because of its content or format (other 400s). */
    public const INVALID_REQUEST = 'invalid_request';

    /** Quota exhausted or rate limited (429 RESOURCE_EXHAUSTED). */
    public const RATE_LIMITED = 'rate_limited';

    /**
     * The API key's daily (or total) quota is spent: waiting a few seconds does
     * not help, so the UI must say so instead of "@retry in a moment".
     */
    public const QUOTA_EXHAUSTED = 'quota_exhausted';

    /** Gemini returned 5xx (temporary service problem). */
    public const UNAVAILABLE = 'unavailable';

    /** Network failure, DNS error, or timeout. */
    public const NETWORK = 'network';

    /** Gemini answered, but with no text, a blocked prompt, or a truncated reply. */
    public const EMPTY_RESPONSE = 'empty_response';

    /** Any other failure. */
    public const UNKNOWN = 'unknown';

    /**
     * @param  array<string, mixed>  $context  Safe diagnostic data (no secrets).
     */
    public function __construct(
        string $message,
        string $reason = self::UNKNOWN,
        array $context = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $reason, $context, $previous, 'gemini');
    }
}
