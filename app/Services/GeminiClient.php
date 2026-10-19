<?php

namespace App\Services;

use App\Exceptions\GeminiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Minimal, dependency-free client for the Google Gemini API.
 *
 * Only one API method is used: `models.generateContent` (non-streaming).
 * Request body, authorization, and response parsing therefore always belong
 * to the same API surface:
 *
 *   POST {base_url}/{api_version}/models/{model}:generateContent
 *   header: x-goog-api-key: <key>
 *   body:   { systemInstruction, contents: [{role, parts:[{text}]}], generationConfig }
 *   reply:  candidates[0].content.parts[*].text
 *
 * Conversation state is deliberately NOT kept here: Laravel HTTP requests are
 * stateless, so the caller passes the full history on every call (the same
 * turn-by-turn behaviour as the Python SDK's `chat.send_message()`).
 */
class GeminiClient
{
    /**
     * Generate one assistant turn.
     *
     * @param  array<int, array{role: string, parts: array<int, array{text: string}>}>  $contents
     *         Chronological conversation, ending with the current user turn.
     * @param  string  $systemInstruction  Stable assistant instructions + verified user context.
     * @param  int|null  $maxOutputTokens  Override for small checks (e.g. a connection probe).
     * @return array{text: string, model: string, finish_reason: string|null, usage: array<string, int>}
     *
     * @throws GeminiException
     */
    public function generateContent(array $contents, string $systemInstruction, ?int $maxOutputTokens = null): array
    {
        $key = trim((string) config('services.gemini.key'));

        if ($key === '') {
            throw new GeminiException(
                'GEMINI_API_KEY is not configured.',
                GeminiException::NOT_CONFIGURED,
            );
        }

        $model = $this->model();

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $systemInstruction]],
            ],
            'contents' => array_values($contents),
            'generationConfig' => $this->generationConfig($maxOutputTokens),
        ];

        try {
            $response = Http::withHeaders([
                'x-goog-api-key' => $key,
                'Content-Type' => 'application/json',
            ])
                ->acceptJson()
                ->timeout(max(5, (int) config('services.gemini.timeout', 30)))
                ->connectTimeout(10)
                ->post($this->endpoint($model), $payload);
        } catch (ConnectionException $e) {
            throw new GeminiException(
                'Could not reach the Gemini API: '.$e->getMessage(),
                GeminiException::NETWORK,
                ['model' => $model],
                $e,
            );
        } catch (Throwable $e) {
            throw new GeminiException(
                'Unexpected error while calling Gemini: '.$e->getMessage(),
                GeminiException::UNKNOWN,
                ['model' => $model],
                $e,
            );
        }

        if ($response->failed()) {
            throw $this->failure($response->status(), $response->json('error'), $model);
        }

        return $this->parse($response->json(), $model);
    }

    /**
     * Model id in the short form used by the endpoint (e.g. "gemini-3.8-flash").
     */
    public function model(): string
    {
        $model = trim((string) config('services.gemini.model'));

        // Accept both "gemini-3.8-flash" and "models/gemini-3.8-flash".
        return str_starts_with($model, 'models/') ? substr($model, 7) : $model;
    }

    /**
     * Whether a key is present (the key itself is never returned).
     */
    public function configured(): bool
    {
        return trim((string) config('services.gemini.key')) !== '';
    }

    /**
     * Verify the configured key and model without generating any text.
     *
     * `GET models` is free and returns every model the key may use, so this is
     * the cheapest honest check of a real connection: a wrong key is rejected
     * by Google, and a model missing from the list is reported as not found.
     *
     * @throws GeminiException
     */
    public function verifyCredentials(): void
    {
        $key = trim((string) config('services.gemini.key'));

        if ($key === '') {
            throw new GeminiException(
                'GEMINI_API_KEY is not configured.',
                GeminiException::NOT_CONFIGURED,
            );
        }

        $model = $this->model();

        try {
            $response = Http::withHeaders(['x-goog-api-key' => $key])
                ->acceptJson()
                // The check must never delay a page render noticeably.
                ->timeout(min(10, max(3, (int) config('services.gemini.timeout', 30))))
                ->connectTimeout(5)
                ->get($this->modelsEndpoint());
        } catch (ConnectionException $e) {
            throw new GeminiException(
                'Could not reach the Gemini API: '.$e->getMessage(),
                GeminiException::NETWORK,
                ['model' => $model],
                $e,
            );
        } catch (Throwable $e) {
            throw new GeminiException(
                'Unexpected error while calling Gemini: '.$e->getMessage(),
                GeminiException::UNKNOWN,
                ['model' => $model],
                $e,
            );
        }

        if ($response->failed()) {
            throw $this->failure($response->status(), $response->json('error'), $model);
        }

        // The model list is paginated; one page is enough to spot a wrong model
        // for the common case where GEMINI_MODEL is misspelled or retired.
        $names = collect((array) $response->json('models', []))
            ->map(fn ($entry) => is_array($entry) ? (string) ($entry['name'] ?? '') : '')
            ->map(fn (string $name) => str_starts_with($name, 'models/') ? substr($name, 7) : $name)
            ->filter()
            ->all();

        if ($names !== [] && ! in_array($model, $names, true)) {
            throw new GeminiException(
                "Configured model {$model} is not available to this API key.",
                GeminiException::MODEL_NOT_FOUND,
                ['model' => $model, 'reason' => 'not_in_model_list'],
            );
        }
    }

    private function modelsEndpoint(): string
    {
        $base = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com'), '/');
        $version = trim((string) config('services.gemini.api_version', 'v1beta'), '/') ?: 'v1beta';

        return sprintf('%s/%s/models', $base, $version);
    }

    private function endpoint(string $model): string
    {
        $base = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com'), '/');
        $version = trim((string) config('services.gemini.api_version', 'v1beta'), '/') ?: 'v1beta';

        return sprintf('%s/%s/models/%s:generateContent', $base, $version, rawurlencode($model));
    }

    /**
     * @return array<string, mixed>
     */
    private function generationConfig(?int $maxOutputTokens = null): array
    {
        $tokens = $maxOutputTokens
            ?? (int) config('services.gemini.max_output_tokens', 1024);

        $config = [
            'temperature' => (float) config('services.gemini.temperature', 0.7),
            'maxOutputTokens' => max(1, $tokens),
        ];

        // Thinking models accept `thinkingConfig`; a small budget keeps replies
        // fast. Lite / non-thinking models may reject it, so this stays opt-in
        // through GEMINI_THINKING_BUDGET (> 0).
        $budget = (int) config('services.gemini.thinking_budget', 0);
        if ($budget > 0 && $maxOutputTokens === null) {
            $config['thinkingConfig'] = ['thinkingBudget' => $budget];
        }

        return $config;
    }

    /**
     * Map a non-2xx response to a typed, secret-free exception.
     *
     * @param  array<string, mixed>|null  $error
     */
    private function failure(int $status, mixed $error, string $model): GeminiException
    {
        $error = is_array($error) ? $error : [];
        $apiStatus = (string) ($error['status'] ?? '');
        $apiMessage = (string) ($error['message'] ?? '');
        $reasonCode = (string) data_get($error, 'details.0.reason', '');

        // Google reports "retry in 55.1s" (RetryInfo.retryDelay) and, for quota
        // problems, a QuotaFailure entry naming the metric and its limit.
        $details = (array) ($error['details'] ?? []);
        $retryDelay = null;
        foreach ($details as $detail) {
            $delay = data_get($detail, 'retryDelay');
            if (is_string($delay) && preg_match('/^(\d+(?:\.\d+)?)s$/', trim($delay), $m)) {
                $retryDelay = (int) ceil((float) $m[1]);
            }
        }
        if ($retryDelay === null && preg_match('/Please retry in (\d+(?:\.\d+)?)s/i', $apiMessage, $m)) {
            $retryDelay = (int) ceil((float) $m[1]);
        }

        $quotaMetric = null;
        foreach ($details as $detail) {
            $metric = data_get($detail, 'violations.0.quotaMetric');
            if (is_string($metric) && $metric !== '') {
                $quotaMetric = $metric;
            }
        }

        $context = [
            'http_status' => $status,
            'api_status' => $apiStatus,
            'api_reason' => $reasonCode,
            'model' => $model,
        ];

        if ($retryDelay !== null) {
            $context['retry_after_seconds'] = $retryDelay;
        }

        if ($quotaMetric !== null) {
            // Keep only the metric name (no project ids or key material).
            $context['quota_metric'] = mb_substr($quotaMetric, 0, 120);
        }

        // The API explains the concrete problem (invalid key, unknown model,
        // quota). Log it; it never contains our secret because we only store
        // the message/status returned by Google.
        $message = trim(sprintf('Gemini HTTP %d %s %s', $status, $apiStatus, $apiMessage));

        return match (true) {
            $status === 401,
            $status === 403 => new GeminiException($message, GeminiException::INVALID_CREDENTIALS, $context),

            $status === 400 && $reasonCode === 'API_KEY_INVALID' => new GeminiException($message, GeminiException::INVALID_CREDENTIALS, $context),

            $status === 404,
            $status === 400 && $reasonCode === 'MODEL_NOT_FOUND' => new GeminiException($message, GeminiException::MODEL_NOT_FOUND, $context),

            // A long wait means a spent daily/total quota, not a short rate
            // window: say so honestly and do not auto-retry.
            $status === 429 => $this->isQuotaExhausted($retryDelay, $quotaMetric)
                ? new GeminiException($message, GeminiException::QUOTA_EXHAUSTED, $context)
                : new GeminiException($message, GeminiException::RATE_LIMITED, $context),

            $status >= 500 => new GeminiException($message, GeminiException::UNAVAILABLE, $context),

            $status === 400 => new GeminiException($message, GeminiException::INVALID_REQUEST, $context),

            default => new GeminiException($message, GeminiException::UNKNOWN, $context),
        };
    }

    /**
     * Whether a 429 is a spent daily quota rather than a per-minute burst.
     *
     * Google's free tier reports both as 429 RESOURCE_EXHAUSTED. The difference
     * matters to users: a per-minute limit clears in seconds, a daily quota
     * only resets hours later.
     */
    private function isQuotaExhausted(?int $retryDelay, ?string $quotaMetric): bool
    {
        if ($retryDelay !== null && $retryDelay > 120) {
            return true;
        }

        return $quotaMetric !== null
            && preg_match('/(per_?day|PerDay)/i', $quotaMetric) === 1;
    }

    /**
     * Extract the generated text from a generateContent response.
     *
     * @param  array<string, mixed>|null  $body
     * @return array{text: string, model: string, finish_reason: string|null, usage: array<string, int>}
     */
    private function parse(mixed $body, string $model): array
    {
        $body = is_array($body) ? $body : [];
        $candidate = (array) data_get($body, 'candidates.0', []);
        $finishReason = isset($candidate['finishReason']) ? (string) $candidate['finishReason'] : null;

        $text = '';
        foreach ((array) data_get($candidate, 'content.parts', []) as $part) {
            // `thought` parts (thinking models) are reasoning, not the answer.
            if (! empty($part['thought'])) {
                continue;
            }

            if (isset($part['text']) && is_string($part['text'])) {
                $text .= $part['text'];
            }
        }

        $text = trim($text);
        $usage = (array) ($body['usageMetadata'] ?? []);
        $blockReason = data_get($body, 'promptFeedback.blockReason');

        if ($text === '') {
            throw new GeminiException(
                'Gemini returned no text'
                    .($finishReason ? " (finishReason={$finishReason})" : '')
                    .($blockReason ? " (blockReason={$blockReason})" : ''),
                GeminiException::EMPTY_RESPONSE,
                [
                    'model' => $model,
                    'finish_reason' => $finishReason,
                    'block_reason' => is_string($blockReason) ? $blockReason : null,
                    'prompt_tokens' => (int) ($usage['promptTokenCount'] ?? 0),
                ],
            );
        }

        return [
            'text' => $text,
            'model' => is_string(data_get($body, 'modelVersion')) ? (string) data_get($body, 'modelVersion') : $model,
            'finish_reason' => $finishReason,
            'usage' => [
                'prompt' => (int) ($usage['promptTokenCount'] ?? 0),
                'candidates' => (int) ($usage['candidatesTokenCount'] ?? 0),
                'total' => (int) ($usage['totalTokenCount'] ?? 0),
            ],
        ];
    }
}
