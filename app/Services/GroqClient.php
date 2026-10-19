<?php

namespace App\Services;

use App\Exceptions\AiProviderException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Minimal, dependency-free client for Groq's OpenAI-compatible API.
 *
 * Only two endpoints are used:
 *
 *   GET  {base_url}/models            -> credential + model check (free)
 *   POST {base_url}/chat/completions  -> one assistant turn
 *
 *   header: Authorization: Bearer <key>
 *   body:   { model, messages: [{role, content}], temperature, max_tokens, stream: false }
 *   reply:  choices[0].message.content
 *
 * Conversation state is deliberately NOT kept here: Laravel HTTP requests are
 * stateless, so the caller passes the full history on every call.
 */
class GroqClient
{
    /**
     * Generate one assistant turn.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     *                                                                      Chronological chat messages, starting with the system prompt and
     *                                                                      ending with the current user turn.
     * @return array{text: string, model: string, finish_reason: string|null, usage: array<string, int>}
     *
     * @throws AiProviderException
     */
    public function chat(array $messages): array
    {
        $key = $this->key();

        if ($key === '') {
            throw $this->notConfigured();
        }

        $model = $this->model();

        $payload = [
            'model' => $model,
            'messages' => array_values($messages),
            'temperature' => (float) config('services.groq.temperature', 0.7),
            'max_tokens' => max(1, (int) config('services.groq.max_output_tokens', 1024)),
            'stream' => false,
        ];

        // `reasoning_effort` is only meaningful for reasoning models; sending it
        // to the others is accepted but pointless, so it stays opt-in.
        $effort = trim((string) config('services.groq.reasoning_effort', ''));
        if ($effort !== '') {
            $payload['reasoning_effort'] = $effort;
        }

        try {
            $response = Http::withToken($key)
                ->acceptJson()
                ->asJson()
                ->timeout(max(5, (int) config('services.groq.timeout', 30)))
                ->connectTimeout(10)
                ->post($this->endpoint('chat/completions'), $payload);
        } catch (ConnectionException $e) {
            throw new AiProviderException(
                'Could not reach the Groq API: '.$e->getMessage(),
                AiProviderException::NETWORK,
                ['model' => $model],
                $e,
                'groq',
            );
        } catch (Throwable $e) {
            throw new AiProviderException(
                'Unexpected error while calling Groq: '.$e->getMessage(),
                AiProviderException::UNKNOWN,
                ['model' => $model],
                $e,
                'groq',
            );
        }

        if ($response->failed()) {
            throw $this->failure($response, $model);
        }

        return $this->parse($response->json(), $model);
    }

    /**
     * Configured model id (e.g. "openai/gpt-oss-20b").
     */
    public function model(): string
    {
        return trim((string) config('services.groq.model'));
    }

    /**
     * Whether a key is present (the key itself is never returned).
     */
    public function configured(): bool
    {
        return $this->key() !== '';
    }

    /**
     * Verify the configured key and model without spending tokens.
     *
     * `GET /models` is free and lists every model this key may use, so this is
     * the cheapest honest check: a wrong key answers 401, and a model missing
     * from the list is reported as not found.
     *
     * @throws AiProviderException
     */
    public function verifyCredentials(): void
    {
        $key = $this->key();

        if ($key === '') {
            throw $this->notConfigured();
        }

        $model = $this->model();

        try {
            $response = Http::withToken($key)
                ->acceptJson()
                // The check must never delay a page render noticeably.
                ->timeout(min(10, max(3, (int) config('services.groq.timeout', 30))))
                ->connectTimeout(5)
                ->get($this->endpoint('models'));
        } catch (ConnectionException $e) {
            throw new AiProviderException(
                'Could not reach the Groq API: '.$e->getMessage(),
                AiProviderException::NETWORK,
                ['model' => $model],
                $e,
                'groq',
            );
        } catch (Throwable $e) {
            throw new AiProviderException(
                'Unexpected error while calling Groq: '.$e->getMessage(),
                AiProviderException::UNKNOWN,
                ['model' => $model],
                $e,
                'groq',
            );
        }

        if ($response->failed()) {
            throw $this->failure($response, $model);
        }

        $ids = collect((array) $response->json('data', []))
            ->map(fn ($entry) => is_array($entry) ? (string) ($entry['id'] ?? '') : '')
            ->filter()
            ->all();

        if ($ids !== [] && $model !== '' && ! in_array($model, $ids, true)) {
            throw new AiProviderException(
                "Configured model {$model} is not available to this Groq key.",
                AiProviderException::MODEL_NOT_FOUND,
                ['model' => $model, 'reason' => 'not_in_model_list'],
                null,
                'groq',
            );
        }
    }

    private function key(): string
    {
        return trim((string) config('services.groq.key'));
    }

    private function notConfigured(): AiProviderException
    {
        return new AiProviderException(
            'GROQ_API_KEY is not configured.',
            AiProviderException::NOT_CONFIGURED,
            [],
            null,
            'groq',
        );
    }

    private function endpoint(string $path): string
    {
        $base = rtrim((string) config('services.groq.base_url', 'https://api.groq.com/openai/v1'), '/');

        return $base.'/'.ltrim($path, '/');
    }

    /**
     * Map a non-2xx response to a typed, secret-free exception.
     *
     * Groq follows OpenAI conventions: `{"error": {"message", "type", "code"}}`,
     * a `401` for a bad key, a `404` for an unknown model, and `429` with a
     * `retry-after` header plus `x-ratelimit-remaining-requests` (the daily
     * allowance). The body message is kept out of the exception message so a
     * key echo can never reach a log line.
     */
    private function failure(Response $response, string $model): AiProviderException
    {
        $status = $response->status();
        $type = (string) $response->json('error.type', '');
        $code = (string) $response->json('error.code', '');
        $retryAfter = (int) $response->header('retry-after');
        $remainingRequests = $response->header('x-ratelimit-remaining-requests');

        $context = [
            'provider' => 'groq',
            'model' => $model,
            'status' => $status,
            'type' => $type !== '' ? $type : null,
            'code' => $code !== '' ? $code : null,
            'retry_after_seconds' => $retryAfter > 0 ? $retryAfter : null,
        ];

        $message = "Groq API error (status={$status}"
            .($type !== '' ? ", type={$type}" : '')
            .($code !== '' ? ", code={$code}" : '')
            .').';

        // Unknown model: Groq answers 404 with code `model_not_found`.
        if ($status === 404 || str_contains(strtolower($type.$code), 'model_not_found')) {
            return new AiProviderException($message, AiProviderException::MODEL_NOT_FOUND, $context, null, 'groq');
        }

        return match (true) {
            $status === 401, $status === 403 => new AiProviderException(
                $message, AiProviderException::INVALID_CREDENTIALS, $context, null, 'groq'
            ),

            // 429: `x-ratelimit-remaining-requests` tracks the daily allowance,
            // so zero remaining plus a long retry-after means today's quota is
            // gone; anything else is a per-minute burst that clears quickly.
            $status === 429 => $this->isQuotaExhausted($retryAfter, $remainingRequests)
                ? new AiProviderException($message, AiProviderException::QUOTA_EXHAUSTED, $context, null, 'groq')
                : new AiProviderException($message, AiProviderException::RATE_LIMITED, $context, null, 'groq'),

            $status === 400, $status === 413, $status === 422 => new AiProviderException(
                $message, AiProviderException::INVALID_REQUEST, $context, null, 'groq'
            ),

            $status >= 500 => new AiProviderException(
                $message, AiProviderException::UNAVAILABLE, $context, null, 'groq'
            ),

            default => new AiProviderException($message, AiProviderException::UNKNOWN, $context, null, 'groq'),
        };
    }

    /**
     * Whether a 429 is a spent daily quota rather than a per-minute burst.
     *
     * Both surface as 429 but mean different things to the user: a per-minute
     * limit clears in seconds, a daily allowance only resets hours later.
     */
    private function isQuotaExhausted(int $retryAfter, ?string $remainingRequests): bool
    {
        if ($remainingRequests !== null && is_numeric($remainingRequests) && (float) $remainingRequests <= 0) {
            return true;
        }

        return $retryAfter > 120;
    }

    /**
     * Extract the generated text from a chat completion response.
     *
     * @param  array<string, mixed>|null  $body
     * @return array{text: string, model: string, finish_reason: string|null, usage: array<string, int>}
     */
    private function parse(mixed $body, string $model): array
    {
        $body = is_array($body) ? $body : [];
        $choice = (array) data_get($body, 'choices.0', []);
        $finishReason = isset($choice['finish_reason']) ? (string) $choice['finish_reason'] : null;

        $content = data_get($choice, 'message.content');
        $text = is_string($content) ? trim($content) : '';

        $usage = (array) ($body['usage'] ?? []);

        if ($text === '') {
            throw new AiProviderException(
                'Groq returned no text'.($finishReason ? " (finish_reason={$finishReason})" : ''),
                AiProviderException::EMPTY_RESPONSE,
                [
                    'provider' => 'groq',
                    'model' => $model,
                    'finish_reason' => $finishReason,
                    'prompt_tokens' => (int) ($usage['prompt_tokens'] ?? 0),
                ],
                null,
                'groq',
            );
        }

        return [
            'text' => $text,
            'model' => is_string($body['model'] ?? null) ? (string) $body['model'] : $model,
            'finish_reason' => $finishReason,
            'usage' => [
                'prompt' => (int) ($usage['prompt_tokens'] ?? 0),
                'candidates' => (int) ($usage['completion_tokens'] ?? 0),
                'total' => (int) ($usage['total_tokens'] ?? 0),
            ],
        ];
    }
}
