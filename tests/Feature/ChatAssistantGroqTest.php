<?php

namespace Tests\Feature;

use App\Exceptions\AiProviderException;
use App\Models\ChatMessage;
use App\Models\InsuranceProduct;
use App\Models\User;
use App\Models\UserProfile;
use Database\Seeders\InsuranceProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Covers the Groq integration and the provider strategy (Groq primary,
 * Gemini optional fallback).
 *
 * Every response is faked, so no real API quota is ever consumed.
 */
class ChatAssistantGroqTest extends TestCase
{
    use RefreshDatabase;

    private const CHAT_ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';

    private const MODELS_ENDPOINT = 'https://api.groq.com/openai/v1/models';

    private const MODEL = 'openai/gpt-oss-20b';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(InsuranceProductSeeder::class);

        config([
            'services.ai.default' => 'groq',
            'services.ai.fallback' => 'gemini',
            'services.groq.key' => 'gsk_test_key',
            'services.groq.model' => self::MODEL,
            'services.groq.base_url' => 'https://api.groq.com/openai/v1',
            'services.groq.history_limit' => 12,
            'services.gemini.key' => null,
            'services.gemini.model' => 'gemini-3.1-flash-lite',
        ]);

        Cache::flush();

        $this->user = $this->makeUser('groq@insuremind.ai');
    }

    private function makeUser(string $email): User
    {
        return User::create([
            'name' => 'Groq Tester',
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function completion(string $text, array $overrides = []): array
    {
        return array_replace_recursive([
            'id' => 'chatcmpl-test',
            'object' => 'chat.completion',
            'model' => self::MODEL,
            'choices' => [[
                'index' => 0,
                'message' => ['role' => 'assistant', 'content' => $text],
                'finish_reason' => 'stop',
            ]],
            'usage' => [
                'prompt_tokens' => 210,
                'completion_tokens' => 64,
                'total_tokens' => 274,
            ],
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers
     */
    private function fakeGroq(int $status = 200, ?array $body = null, array $headers = []): void
    {
        Http::fake([
            self::CHAT_ENDPOINT => Http::response($body ?? $this->completion('Halo! Ada yang bisa aku bantu?'), $status, $headers),
            self::MODELS_ENDPOINT => Http::response(['object' => 'list', 'data' => [['id' => self::MODEL]]], 200),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function geminiCompletion(string $text): array
    {
        return [
            'candidates' => [[
                'content' => ['role' => 'model', 'parts' => [['text' => $text]]],
                'finishReason' => 'STOP',
            ]],
            'modelVersion' => 'gemini-3.1-flash-lite',
        ];
    }

    public function test_a_successful_groq_answer_keeps_the_frontend_contract(): void
    {
        $this->fakeGroq(200, $this->completion('Premi yang wajar sekitar 5-10% dari penghasilanmu.'));

        $response = $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Berapa premi yang wajar?'])
            ->assertOk()
            ->assertJsonPath('assistant.body_id', 'Premi yang wajar sekitar 5-10% dari penghasilanmu.')
            ->assertJsonPath('assistant.body_en', 'Premi yang wajar sekitar 5-10% dari penghasilanmu.')
            ->assertJsonPath('assistant.engine', 'groq')
            ->assertJsonPath('assistant.model', self::MODEL);

        // The same keys the old frontend already relied on.
        $this->assertSame(['id', 'body_id', 'body_en'], array_keys($response->json('user')));
        $this->assertArrayHasKey('id', $response->json('assistant'));
        $this->assertNull($response->json('error'));

        Http::assertSent(function (Request $request) {
            $body = $request->data();

            return $request->url() === self::CHAT_ENDPOINT
                && $request->method() === 'POST'
                && $request->hasHeader('Authorization', 'Bearer gsk_test_key')
                && $body['model'] === self::MODEL
                && $body['stream'] === false
                && $body['messages'][0]['role'] === 'system'
                && str_contains($body['messages'][0]['content'], 'InsureMind AI')
                && $body['messages'][1] === ['role' => 'user', 'content' => 'Berapa premi yang wajar?'];
        });

        // Both turns persisted, engine recorded, no duplicates.
        $this->assertSame(2, $this->user->chatMessages()->count());
        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $this->user->id,
            'role' => 'assistant',
            'body_id' => 'Premi yang wajar sekitar 5-10% dari penghasilanmu.',
        ]);
    }

    public function test_the_api_key_never_leaves_the_authorization_header(): void
    {
        $this->fakeGroq();

        $response = $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo']);

        $this->assertStringNotContainsString('gsk_test_key', $response->getContent());

        Http::assertSent(function (Request $request) {
            return $request->url() === self::CHAT_ENDPOINT
                && ! str_contains($request->body(), 'gsk_test_key');
        });
    }

    public function test_follow_ups_carry_the_conversation_history_in_order(): void
    {
        $this->user->chatMessages()->create([
            'role' => 'user', 'body_id' => 'Aku baru punya BPJS aja.', 'body_en' => 'Aku baru punya BPJS aja.',
        ]);
        $this->user->chatMessages()->create([
            'role' => 'assistant', 'body_id' => 'Oke, lalu bagaimana dengan asuransi swasta?', 'body_en' => 'x',
            'meta' => ['engine' => 'groq'],
        ]);

        $this->fakeGroq(200, $this->completion('Kalau baru BPJS, mulai dari asuransi kesehatan tambahan.'));

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Terus aku harus mulai dari mana?'])
            ->assertOk();

        Http::assertSent(function (Request $request) {
            return $request->url() === self::CHAT_ENDPOINT
                && array_slice($request->data()['messages'], 1) === [
                    ['role' => 'user', 'content' => 'Aku baru punya BPJS aja.'],
                    ['role' => 'assistant', 'content' => 'Oke, lalu bagaimana dengan asuransi swasta?'],
                    ['role' => 'user', 'content' => 'Terus aku harus mulai dari mana?'],
                ];
        });
    }

    public function test_chat_page_restores_the_latest_fifty_messages_chronologically_on_refresh(): void
    {
        $timestamp = now()->startOfSecond();

        foreach (range(1, 26) as $turn) {
            $turnTime = $timestamp->copy()->addSeconds($turn);
            $this->user->chatMessages()->create([
                'role' => 'user',
                'body_id' => sprintf('Turn %02d question', $turn),
                'body_en' => sprintf('Turn %02d question', $turn),
                'created_at' => $turnTime,
            ]);
            $this->user->chatMessages()->create([
                'role' => 'assistant',
                'body_id' => sprintf('Turn %02d answer', $turn),
                'body_en' => sprintf('Turn %02d answer', $turn),
                'created_at' => $turnTime,
            ]);
        }

        $expectedIds = ChatMessage::query()
            ->where('user_id', $this->user->id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->pluck('id')
            ->slice(-50)
            ->values()
            ->map(fn ($id) => 'data-message-id="'.$id.'"')
            ->all();

        $this->fakeGroq();

        foreach ([1, 2] as $_refresh) {
            $response = $this->actingAs($this->user)->get(route('chat.index'))->assertOk();
            $html = $response->getContent();

            $response->assertSeeInOrder($expectedIds);
            $response->assertSee('Turn 26 answer');
            $response->assertDontSee('Turn 01 question');
            $this->assertSame(50, substr_count($html, 'data-message-id="'));
        }

        $this->assertSame(52, $this->user->chatMessages()->count());
    }

    public function test_chat_send_button_keeps_its_label_and_posts_to_the_chat_route(): void
    {
        $this->fakeGroq();

        $response = $this->actingAs($this->user)
            ->get(route('chat.index'))
            ->assertOk()
            ->assertSee('<form id="chatForm" class="chat-composer-form" method="POST" action="'.route('chat.store').'">', false)
            ->assertSee('<button type="submit" id="chatSubmit" class="btn btn-primary chat-composer-submit">', false)
            ->assertSee('<span data-id="Kirim" data-en="Send">Kirim</span>', false)
            ->assertSee('name="_token"', false)
            ->assertSee('placeholder="Type your question..."', false)
            ->assertSee('data-en="Ask anything about your insurance needs."', false);

        $html = $response->getContent();
        $this->assertStringContainsString('<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">', $html);
        $this->assertStringContainsString('flex: 0 0 96px;', $html);
        $this->assertStringContainsString('height: 52px;', $html);
    }

    public function test_ai_history_uses_created_at_then_id_for_equal_timestamp_turns(): void
    {
        $timestamp = now()->startOfSecond();
        foreach ([
            ['user', 'First question'],
            ['assistant', 'First answer'],
            ['user', 'Second question'],
            ['assistant', 'Second answer'],
        ] as [$role, $body]) {
            $this->user->chatMessages()->create([
                'role' => $role,
                'body_id' => $body,
                'body_en' => $body,
                'created_at' => $timestamp,
            ]);
        }

        $this->fakeGroq();

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Final follow-up'])
            ->assertOk();

        Http::assertSent(function (Request $request) {
            return $request->url() === self::CHAT_ENDPOINT
                && array_slice($request->data()['messages'], 1) === [
                    ['role' => 'user', 'content' => 'First question'],
                    ['role' => 'assistant', 'content' => 'First answer'],
                    ['role' => 'user', 'content' => 'Second question'],
                    ['role' => 'assistant', 'content' => 'Second answer'],
                    ['role' => 'user', 'content' => 'Final follow-up'],
                ];
        });
    }

    public function test_a_missing_key_is_reported_without_calling_groq(): void
    {
        config(['services.groq.key' => null]);

        Http::fake();

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('assistant', null)
            ->assertJsonPath('error.reason', AiProviderException::NOT_CONFIGURED)
            ->assertJsonPath('error.provider', 'none');

        Http::assertNothingSent();

        // The user's question is still saved: a failed call never loses it.
        $this->assertSame(1, $this->user->chatMessages()->count());
    }

    public function test_an_invalid_key_is_reported_as_invalid_credentials(): void
    {
        $this->fakeGroq(401, ['error' => [
            'message' => 'Invalid API Key', 'type' => 'invalid_request_error', 'code' => 'invalid_api_key',
        ]]);

        config(['services.gemini.key' => null]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('error.reason', AiProviderException::INVALID_CREDENTIALS)
            ->assertJsonPath('error.provider', 'groq')
            ->assertJsonPath('error.retryable', false);

        // Never retried: a bad key cannot heal by trying again.
        Http::assertSentCount(1);
    }

    public function test_an_unsupported_model_is_reported_as_model_not_found(): void
    {
        $this->fakeGroq(404, ['error' => [
            'message' => 'The model `llama-9` does not exist',
            'type' => 'invalid_request_error',
            'code' => 'model_not_found',
        ]]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('error.reason', AiProviderException::MODEL_NOT_FOUND);
    }

    public function test_a_per_minute_rate_limit_is_retried_once(): void
    {
        config(['services.groq.retry_attempts' => 2]);

        Http::fake([
            self::CHAT_ENDPOINT => Http::sequence()
                ->push(['error' => ['message' => 'Rate limit reached', 'type' => 'tokens', 'code' => 'rate_limit_exceeded']], 429, ['retry-after' => '1'])
                ->push($this->completion('Siap, aku bantu jelaskan.'), 200),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertOk()
            ->assertJsonPath('assistant.body_id', 'Siap, aku bantu jelaskan.');

        Http::assertSentCount(2);
    }

    public function test_a_spent_daily_quota_is_reported_honestly_and_not_retried(): void
    {
        $this->fakeGroq(429, ['error' => [
            'message' => 'Rate limit reached for requests', 'type' => 'requests', 'code' => 'rate_limit_exceeded',
        ]], ['retry-after' => '3600', 'x-ratelimit-remaining-requests' => '0']);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('error.reason', AiProviderException::QUOTA_EXHAUSTED)
            ->assertJsonPath('error.retryable', false);

        Http::assertSentCount(1);
    }

    public function test_a_timeout_is_reported_as_a_network_error(): void
    {
        Http::fake([
            self::CHAT_ENDPOINT => fn () => throw new ConnectionException('cURL error 28: Operation timed out'),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('error.reason', AiProviderException::NETWORK);
    }

    public function test_a_server_error_is_reported_as_unavailable(): void
    {
        config(['services.groq.retry_attempts' => 1]);

        $this->fakeGroq(503, ['error' => ['message' => 'Service Unavailable', 'type' => 'server_error']]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('error.reason', AiProviderException::UNAVAILABLE);

        Http::assertSentCount(1);
    }

    public function test_gemini_takes_over_when_groq_key_is_rejected(): void
    {
        config(['services.gemini.key' => 'gemini-test-key']);

        Http::fake([
            self::CHAT_ENDPOINT => Http::response(['error' => ['message' => 'Invalid API Key', 'type' => 'invalid_request_error']], 401),
            'https://generativelanguage.googleapis.com/v1beta/models/*' => Http::response(
                $this->geminiCompletion('Dari Gemini: mulai dari asuransi kesehatan dulu.'), 200
            ),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Mulai dari mana?'])
            ->assertOk()
            ->assertJsonPath('assistant.engine', 'gemini')
            ->assertJsonPath('assistant.body_id', 'Dari Gemini: mulai dari asuransi kesehatan dulu.');

        // The fallback is recorded, so reporting can see why it was used.
        $meta = ChatMessage::query()->where('role', 'assistant')->firstOrFail()->meta;
        $this->assertSame('gemini', $meta['engine']);
        $this->assertSame('groq', $meta['fallback_from']);
        $this->assertSame(AiProviderException::INVALID_CREDENTIALS, $meta['fallback_reason']);
    }

    public function test_a_groq_server_error_is_retried_and_does_not_burn_gemini(): void
    {
        config(['services.gemini.key' => 'gemini-test-key', 'services.groq.retry_attempts' => 2]);

        Http::fake([
            self::CHAT_ENDPOINT => Http::sequence()
                ->push(['error' => ['message' => 'overloaded', 'type' => 'server_error']], 503)
                ->push($this->completion('Jawaban dari Groq.'), 200),
            'https://generativelanguage.googleapis.com/v1beta/models/*' => Http::response(
                $this->geminiCompletion('Gemini tidak seharusnya dipanggil.'), 200
            ),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertOk()
            ->assertJsonPath('assistant.engine', 'groq');

        Http::assertNotSent(fn (Request $request) => str_contains($request->url(), 'generativelanguage'));
    }

    public function test_gemini_takes_over_after_groq_retries_are_exhausted(): void
    {
        config([
            'services.gemini.key' => 'gemini-test-key',
            'services.groq.retry_attempts' => 1,
        ]);

        Http::fake([
            self::CHAT_ENDPOINT => Http::response(
                ['error' => ['message' => 'temporarily unavailable', 'type' => 'server_error']],
                503,
            ),
            'https://generativelanguage.googleapis.com/v1beta/models/*' => Http::response(
                $this->geminiCompletion('Jawaban cadangan dari Gemini.'),
                200,
            ),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Apa rekomendasinya?'])
            ->assertOk()
            ->assertJsonPath('assistant.engine', 'gemini')
            ->assertJsonPath('assistant.body_id', 'Jawaban cadangan dari Gemini.');

        $meta = ChatMessage::query()->where('role', 'assistant')->firstOrFail()->meta;
        $this->assertSame('groq', $meta['fallback_from']);
        $this->assertSame(AiProviderException::UNAVAILABLE, $meta['fallback_reason']);
    }

    public function test_gemini_takes_over_when_groq_cannot_be_reached(): void
    {
        config([
            'services.gemini.key' => 'gemini-test-key',
            'services.groq.retry_attempts' => 1,
        ]);

        Http::fake([
            self::CHAT_ENDPOINT => fn () => throw new ConnectionException('Connection timed out'),
            'https://generativelanguage.googleapis.com/v1beta/models/*' => Http::response(
                $this->geminiCompletion('Jawaban setelah gangguan jaringan.'),
                200,
            ),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Bisa bantu?'])
            ->assertOk()
            ->assertJsonPath('assistant.engine', 'gemini')
            ->assertJsonPath('assistant.body_id', 'Jawaban setelah gangguan jaringan.');
    }

    public function test_both_providers_failing_returns_one_honest_error_without_local_answers(): void
    {
        config(['services.gemini.key' => 'gemini-test-key', 'services.groq.retry_attempts' => 1]);

        Http::fake([
            self::CHAT_ENDPOINT => Http::response(['error' => ['message' => 'Invalid API Key', 'type' => 'invalid_request_error']], 401),
            'https://generativelanguage.googleapis.com/v1beta/models/*' => Http::response(['error' => [
                'code' => 403, 'status' => 'PERMISSION_DENIED', 'message' => 'API key not valid',
            ]], 403),
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('assistant', null)
            ->assertJsonPath('error.provider', 'gemini')
            ->assertJsonPath('error.reason', AiProviderException::INVALID_CREDENTIALS);

        // No fabricated local answer, and the single user turn is preserved.
        $this->assertSame(1, $this->user->chatMessages()->count());
        $this->assertStringNotContainsString('gemini-test-key', $response->getContent());
        $this->assertStringNotContainsString('gsk_test_key', $response->getContent());
    }

    public function test_english_and_indonesian_conversations_are_passed_through_unchanged(): void
    {
        // Two consecutive turns in one conversation: English, then Indonesian.
        Http::fake([
            self::CHAT_ENDPOINT => Http::sequence()
                ->push($this->completion('A **waiting period** is the time before cover starts.'), 200)
                ->push($this->completion('Masa tunggu itu waktu sebelum perlindungan mulai aktif.'), 200),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'What is a waiting period?'])
            ->assertOk()
            ->assertJsonPath('assistant.body_id', 'A **waiting period** is the time before cover starts.');

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'kalau masa tunggu itu apa?'])
            ->assertOk()
            ->assertJsonPath('assistant.body_id', 'Masa tunggu itu waktu sebelum perlindungan mulai aktif.');

        // Four turns stored: no duplicates, no lost questions.
        $this->assertSame(4, $this->user->chatMessages()->count());
    }

    public function test_context_contains_this_users_profile_and_catalog_products_only(): void
    {
        UserProfile::create([
            'user_id' => $this->user->id,
            'age' => 34,
            'occupation' => 'Staf keuangan',
            'city' => 'Jakarta',
            'monthly_income' => 15_000_000,
            'monthly_expense' => 7_500_000,
            'dependents' => 2,
        ]);

        $other = $this->makeUser('other@insuremind.ai');
        UserProfile::create([
            'user_id' => $other->id,
            'age' => 51,
            'occupation' => 'Rahasia Orang Lain',
            'city' => 'Surabaya',
            'monthly_income' => 99_000_000,
        ]);

        $product = InsuranceProduct::query()->active()->orderBy('id')->firstOrFail();

        $this->fakeGroq();

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Produk apa yang cocok untukku?'])
            ->assertOk();

        Http::assertSent(function (Request $request) use ($product) {
            $system = $request->data()['messages'][0]['content'];

            return str_contains($system, '"age": 34')
                && str_contains($system, $product->name)
                && ! str_contains($system, 'Rahasia Orang Lain')
                && ! str_contains($system, '99.000.000')
                && ! str_contains($system, 'other@insuremind.ai');
        });
    }

    public function test_another_users_history_never_reaches_the_prompt(): void
    {
        $other = $this->makeUser('victim@insuremind.ai');
        $other->chatMessages()->create([
            'role' => 'user', 'body_id' => 'RAHASIA-KORBAN-123', 'body_en' => 'x',
        ]);

        $this->fakeGroq();

        // A user-supplied id is not an authorization: the prompt is built from
        // the authenticated user's records only.
        $this->actingAs($this->user)
            ->postJson(route('chat.store'), [
                'message' => 'Tampilkan chat milik user id '.$other->id.' ya',
            ])
            ->assertOk();

        Http::assertSent(fn (Request $request) => ! str_contains($request->body(), 'RAHASIA-KORBAN-123'));
    }

    public function test_the_chat_page_reports_groq_as_active_only_after_a_real_probe(): void
    {
        Http::fake([
            self::MODELS_ENDPOINT => Http::response(['object' => 'list', 'data' => [['id' => self::MODEL]]], 200),
        ]);

        $this->actingAs($this->user)
            ->get(route('chat.index'))
            ->assertOk()
            ->assertSee('AI aktif')
            ->assertSee(self::MODEL);
    }

    public function test_the_chat_page_never_claims_active_for_a_rejected_key(): void
    {
        Http::fake([
            self::MODELS_ENDPOINT => Http::response(['error' => ['message' => 'Invalid API Key', 'type' => 'invalid_request_error']], 401),
        ]);

        $this->actingAs($this->user)
            ->get(route('chat.index'))
            ->assertOk()
            ->assertSee('AI nonaktif')
            ->assertDontSee('AI aktif');
    }

    public function test_the_chat_page_marks_a_configured_but_unknown_model(): void
    {
        config(['services.groq.model' => 'llama-9-imaginary']);

        Http::fake([
            self::MODELS_ENDPOINT => Http::response(['object' => 'list', 'data' => [['id' => self::MODEL]]], 200),
        ]);

        $this->actingAs($this->user)
            ->get(route('chat.index'))
            ->assertOk()
            ->assertSee('Model AI yang dikonfigurasi tidak tersedia');
    }

    public function test_untrusted_markup_in_a_model_answer_is_escaped_on_the_page(): void
    {
        $this->fakeGroq(200, $this->completion('<script>alert(1)</script> Halo'));

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertOk();

        $this->actingAs($this->user)
            ->get(route('chat.index'))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }
}
