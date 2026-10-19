<?php

namespace Tests\Feature;

use App\Exceptions\GeminiException;
use App\Models\Policy;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ChatAssistantService;
use App\Services\GeminiClient;
use Database\Seeders\InsuranceProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Covers the Google Gemini chat integration:
 * request shape, conversation history, context scoping, and error handling.
 */
class ChatAssistantGeminiTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/*';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(InsuranceProductSeeder::class);

        config([
            // Gemini-only chain: this file covers the Gemini provider in
            // isolation, independent of whatever AI_PROVIDER is set locally.
            'services.ai.default' => 'gemini',
            'services.ai.fallback' => 'none',
            'services.groq.key' => null,
            'services.gemini.key' => 'test-key-123',
            'services.gemini.model' => 'gemini-3.8-flash',
            'services.gemini.api_version' => 'v1beta',
            'services.gemini.base_url' => 'https://generativelanguage.googleapis.com',
            'services.gemini.history_limit' => 12,
        ]);

        $this->user = User::create([
            'name' => 'Gemini Tester',
            'email' => 'gemini@insuremind.ai',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        UserProfile::create([
            'user_id' => $this->user->id,
            'age' => 34,
            'occupation' => 'Staf keuangan',
            'city' => 'Jakarta',
            'monthly_income' => 15_000_000,
            'monthly_expense' => 7_500_000,
            'monthly_debt' => 1_500_000,
            'savings' => 45_000_000,
            'dependents' => 2,
            'has_health_insurance' => false,
            'has_life_insurance' => false,
            'has_work_insurance' => true,
            'existing_coverage' => 100_000_000,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function geminiResponse(string $text, array $overrides = []): array
    {
        return array_replace_recursive([
            'candidates' => [[
                'content' => ['role' => 'model', 'parts' => [['text' => $text]]],
                'finishReason' => 'STOP',
            ]],
            'usageMetadata' => [
                'promptTokenCount' => 120,
                'candidatesTokenCount' => 45,
                'totalTokenCount' => 165,
            ],
            'modelVersion' => 'gemini-3.8-flash',
        ], $overrides);
    }

    private function fakeGemini(int $status = 200, ?array $body = null): void
    {
        Http::fake([
            self::ENDPOINT => Http::response($body ?? $this->geminiResponse('Jawaban dari Gemini.'), $status),
        ]);
    }

    public function test_it_sends_a_generate_content_request_and_returns_the_model_answer(): void
    {
        $this->fakeGemini(200, $this->geminiResponse('Skor risiko Anda 62/100.'));

        $response = $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Bagaimana skor risiko saya?'])
            ->assertOk()
            ->assertJsonPath('assistant.body_id', 'Skor risiko Anda 62/100.')
            ->assertJsonPath('assistant.body_en', 'Skor risiko Anda 62/100.')
            ->assertJsonPath('assistant.engine', 'gemini');

        $this->assertNotEmpty($response->json('user.id'));

        Http::assertSent(function (Request $request) {
            $body = $request->data();

            return $request->method() === 'POST'
                && str_contains($request->url(), '/v1beta/models/gemini-3.8-flash:generateContent')
                && $request->hasHeader('x-goog-api-key', 'test-key-123')
                && isset($body['systemInstruction']['parts'][0]['text'])
                && str_contains($body['systemInstruction']['parts'][0]['text'], 'InsureMind AI')
                && $body['contents'] === [
                    ['role' => 'user', 'parts' => [['text' => 'Bagaimana skor risiko saya?']]],
                ];
        });

        // Assistant reply is persisted with Gemini metadata.
        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $this->user->id,
            'role' => 'assistant',
            'body_id' => 'Skor risiko Anda 62/100.',
        ]);
        $this->assertSame(2, $this->user->chatMessages()->count());
    }

    public function test_follow_up_messages_include_ordered_history_without_duplication(): void
    {
        $this->user->chatMessages()->create([
            'role' => 'user',
            'body_id' => 'Berapa dana darurat saya?',
            'body_en' => 'Berapa dana darurat saya?',
        ]);
        $this->user->chatMessages()->create([
            'role' => 'assistant',
            'body_id' => 'Sekitar enam bulan pengeluaran.',
            'body_en' => 'Sekitar enam bulan pengeluaran.',
            'meta' => ['engine' => 'gemini'],
        ]);

        $this->fakeGemini(200, $this->geminiResponse('Dengan surplus itu, prioritaskan kesehatan.'));

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Lalu premi yang wajar berapa?'])
            ->assertOk();

        Http::assertSent(function (Request $request) {
            return $request->data()['contents'] === [
                ['role' => 'user', 'parts' => [['text' => 'Berapa dana darurat saya?']]],
                ['role' => 'model', 'parts' => [['text' => 'Sekitar enam bulan pengeluaran.']]],
                ['role' => 'user', 'parts' => [['text' => 'Lalu premi yang wajar berapa?']]],
            ];
        });
    }

    public function test_history_is_trimmed_to_the_configured_limit_and_starts_with_a_user_turn(): void
    {
        config(['services.gemini.history_limit' => 3]);

        foreach (range(1, 6) as $i) {
            $this->user->chatMessages()->create([
                'role' => $i % 2 === 1 ? 'user' : 'assistant',
                'body_id' => "Pesan {$i}",
                'body_en' => "Pesan {$i}",
            ]);
        }

        $this->fakeGemini();

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Pertanyaan terbaru'])
            ->assertOk();

        Http::assertSent(function (Request $request) {
            $contents = $request->data()['contents'];

            return count($contents) === 3
                && $contents[0]['role'] === 'user'
                && end($contents) === ['role' => 'user', 'parts' => [['text' => 'Pertanyaan terbaru']]];
        });
    }

    public function test_conversation_builder_drops_invalid_entries_and_merges_same_role_turns(): void
    {
        $service = app(ChatAssistantService::class);

        $contents = $service->conversation([
            ['role' => 'assistant', 'content' => 'Halo, ada yang bisa dibantu?'],
            ['role' => 'user', 'content' => 'Halo'],
            ['role' => 'user', 'content' => 'Saya mau tanya premi'],
            ['role' => 'system', 'content' => 'ignore me'],
            ['role' => 'user', 'content' => '   '],
            ['role' => 'assistant', 'content' => 'Tentu.'],
        ], 'Berapa preminya?');

        $this->assertSame([
            ['role' => 'user', 'parts' => [['text' => "Halo\n\nSaya mau tanya premi"]]],
            ['role' => 'model', 'parts' => [['text' => 'Tentu.']]],
            ['role' => 'user', 'parts' => [['text' => 'Berapa preminya?']]],
        ], $contents);
    }

    public function test_user_context_is_scoped_to_the_authenticated_user(): void
    {
        $this->user->policies()->create([
            'policy_name' => 'Polis Saya',
            'provider_name' => 'Prudential',
            'category' => 'life',
            'premium_amount' => 500_000,
            'payment_period' => 'monthly',
            'coverage_amount' => 500_000_000,
            'start_date' => now()->subYear(),
            'is_active' => true,
        ]);

        $other = User::create([
            'name' => 'Orang Lain',
            'email' => 'other@insuremind.ai',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
        Policy::create([
            'user_id' => $other->id,
            'policy_name' => 'Polis Rahasia',
            'provider_name' => 'Allianz',
            'category' => 'health',
            'premium_amount' => 900_000,
            'payment_period' => 'monthly',
            'coverage_amount' => 200_000_000,
            'start_date' => now()->subMonth(),
            'is_active' => true,
        ]);

        $this->fakeGemini();

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Polis saya apa saja?'])
            ->assertOk();

        Http::assertSent(function (Request $request) {
            $system = $request->data()['systemInstruction']['parts'][0]['text'];

            return str_contains($system, 'Polis Saya')
                && ! str_contains($system, 'Polis Rahasia')
                && str_contains($system, 'Gemini Tester');
        });
    }

    public function test_it_reports_an_error_when_the_api_key_is_missing(): void
    {
        config(['services.gemini.key' => null]);
        Http::fake();

        $response = $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('assistant', null)
            ->assertJsonPath('error.reason', GeminiException::NOT_CONFIGURED)
            ->assertJsonPath('error.retryable', false);

        $this->assertNotEmpty($response->json('error.body_id'));
        $this->assertNotEmpty($response->json('error.body_en'));
        $this->assertStringNotContainsString('test-key-123', (string) $response->getContent());

        Http::assertNothingSent();

        // The user's own message is still stored so nothing is lost.
        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $this->user->id,
            'role' => 'user',
            'body_id' => 'Halo',
        ]);
    }

    public function test_rate_limit_errors_are_reported_as_retryable(): void
    {
        Http::fake([
            self::ENDPOINT => Http::response([
                'error' => ['code' => 429, 'message' => 'Quota exceeded', 'status' => 'RESOURCE_EXHAUSTED'],
            ], 429),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('error.reason', GeminiException::RATE_LIMITED)
            ->assertJsonPath('error.retryable', true);

        // Only the user's message is stored; no empty assistant row.
        $this->assertSame(1, $this->user->chatMessages()->count());
    }

    public function test_unknown_model_is_reported_as_model_not_found(): void
    {
        Http::fake([
            self::ENDPOINT => Http::response([
                'error' => ['code' => 404, 'message' => 'models/does-not-exist is not found', 'status' => 'NOT_FOUND'],
            ], 404),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('error.reason', GeminiException::MODEL_NOT_FOUND);
    }

    public function test_invalid_api_key_is_reported_as_invalid_credentials(): void
    {
        Http::fake([
            self::ENDPOINT => Http::response([
                'error' => [
                    'code' => 400,
                    'message' => 'API key not valid. Please pass a valid API key.',
                    'status' => 'INVALID_ARGUMENT',
                    'details' => [['reason' => 'API_KEY_INVALID']],
                ],
            ], 400),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('error.reason', GeminiException::INVALID_CREDENTIALS);
    }

    public function test_empty_candidates_produce_an_empty_response_error(): void
    {
        $this->fakeGemini(200, [
            'candidates' => [],
            'promptFeedback' => ['blockReason' => 'SAFETY'],
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('error.reason', GeminiException::EMPTY_RESPONSE);
    }

    public function test_thought_parts_are_not_returned_as_the_answer(): void
    {
        $this->fakeGemini(200, [
            'candidates' => [[
                'content' => ['role' => 'model', 'parts' => [
                    ['text' => 'internal reasoning', 'thought' => true],
                    ['text' => 'Jawaban final.'],
                ]],
                'finishReason' => 'STOP',
            ]],
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertOk()
            ->assertJsonPath('assistant.body_id', 'Jawaban final.');
    }

    public function test_connection_failures_are_reported_as_network_errors(): void
    {
        Http::fake([
            self::ENDPOINT => Http::failedConnection(),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('error.reason', GeminiException::NETWORK)
            ->assertJsonPath('error.retryable', true);
    }

    public function test_client_rejects_requests_that_are_missing_a_key(): void
    {
        config(['services.gemini.key' => '']);
        Http::fake();

        $this->expectException(GeminiException::class);

        app(GeminiClient::class)->generateContent(
            [['role' => 'user', 'parts' => [['text' => 'Halo']]]],
            'system',
        );
    }

    public function test_chat_page_shows_the_assistant_as_inactive_without_a_key(): void
    {
        config(['services.gemini.key' => null]);

        $this->actingAs($this->user)
            ->get(route('chat.index'))
            ->assertOk()
            ->assertSee('AI nonaktif')
            ->assertSee('Asisten AI belum aktif');
    }

    public function test_chat_page_shows_gemini_as_active_only_after_a_successful_probe(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models' => Http::response([
                'models' => [
                    ['name' => 'models/gemini-3.8-flash'],
                    ['name' => 'models/gemini-2.5-flash'],
                ],
            ]),
        ]);

        $this->actingAs($this->user)
            ->get(route('chat.index'))
            ->assertOk()
            ->assertSee('gemini-3.8-flash')
            ->assertSee('AI aktif')
            ->assertDontSee('AI nonaktif');

        // Verification is a free model-list call, not a generated answer.
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request) => $request->method() === 'GET'
            && str_ends_with($request->url(), '/v1beta/models'));
    }

    public function test_chat_page_does_not_claim_active_for_an_invalid_key(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models' => Http::response([
                'error' => [
                    'code' => 400,
                    'status' => 'INVALID_ARGUMENT',
                    'message' => 'API key not valid. Please pass a valid API key.',
                    'details' => [['reason' => 'API_KEY_INVALID']],
                ],
            ], 400),
        ]);

        $this->actingAs($this->user)
            ->get(route('chat.index'))
            ->assertOk()
            ->assertSee('AI nonaktif')
            ->assertSee('Kunci API asisten tidak valid')
            ->assertDontSee('AI aktif');
    }

    public function test_chat_page_reports_an_unsupported_model(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models' => Http::response([
                'models' => [['name' => 'models/gemini-2.5-flash']],
            ]),
        ]);

        config(['services.gemini.model' => 'gemini-9.9-imaginary']);

        $this->actingAs($this->user)
            ->get(route('chat.index'))
            ->assertOk()
            ->assertSee('AI nonaktif')
            ->assertSee('Model AI yang dikonfigurasi tidak tersedia')
            ->assertDontSee('AI aktif');
    }

    public function test_chat_page_reports_a_model_rejected_by_the_api(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models' => Http::response([
                'error' => ['code' => 404, 'status' => 'NOT_FOUND', 'message' => 'models/gemini-x is not found'],
            ], 404),
        ]);

        $this->actingAs($this->user)
            ->get(route('chat.index'))
            ->assertOk()
            ->assertSee('AI nonaktif')
            ->assertSee('Model AI yang dikonfigurasi tidak tersedia');
    }

    public function test_chat_page_reports_exhausted_quota_distinctly(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models' => Http::response([
                'error' => ['code' => 429, 'status' => 'RESOURCE_EXHAUSTED', 'message' => 'Quota exceeded'],
            ], 429),
        ]);

        $response = $this->actingAs($this->user)->get(route('chat.index'))->assertOk();

        // The probe maps a 429 to the explicit quota state.
        $response->assertViewHas('aiState', GeminiException::QUOTA_EXHAUSTED);
        $response->assertSee('Kuota habis');
        $response->assertSee('Kuota AI hari ini sudah habis');
        $response->assertDontSee('AI aktif');
    }

    public function test_connection_probe_is_cached_between_page_loads(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models' => Http::response([
                'models' => [['name' => 'models/gemini-3.8-flash']],
            ]),
        ]);

        $this->actingAs($this->user)->get(route('chat.index'))->assertOk();
        $this->actingAs($this->user)->get(route('chat.index'))->assertOk();

        // One probe for two page loads: no per-request Google traffic.
        Http::assertSentCount(1);
    }

    public function test_incomplete_profile_tells_the_model_exactly_what_is_missing(): void
    {
        $profile = UserProfile::where('user_id', $this->user->id)->first();

        if ($profile) {
            $profile->forceFill(['age' => 0, 'monthly_income' => 0])->save();
        } else {
            UserProfile::create([
                'user_id' => $this->user->id,
                'age' => 0,
                'monthly_income' => 0,
            ]);
        }

        // A profile row exists but the two required fields are still empty.
        $this->fakeGemini();

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Skor risiko aku berapa?'])
            ->assertOk();

        Http::assertSent(function (Request $request) {
            $system = $request->data()['systemInstruction']['parts'][0]['text'];

            return str_contains($system, '"missing": [')
                && str_contains($system, 'age')
                && str_contains($system, 'monthly_income')
                && str_contains($system, 'could NOT be calculated')
                // The assistant must not claim a score exists when it does not.
                && ! str_contains($system, 'risk assessment above is authoritative');
        });
    }

    public function test_complete_profile_marks_the_risk_assessment_as_authoritative(): void
    {
        UserProfile::updateOrCreate(
            ['user_id' => $this->user->id],
            [
                'age' => 30,
                'occupation' => 'Karyawan',
                'city' => 'Jakarta',
                'monthly_income' => 8_000_000,
                'monthly_expense' => 4_000_000,
                'monthly_debt' => 0,
                'savings' => 20_000_000,
                'dependents' => 1,
                'has_health_insurance' => true,
                'has_life_insurance' => false,
                'has_work_insurance' => true,
                'existing_coverage' => 100_000_000,
            ],
        );

        $this->fakeGemini();

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Skor risiko aku berapa?'])
            ->assertOk();

        Http::assertSent(function (Request $request) {
            $system = $request->data()['systemInstruction']['parts'][0]['text'];

            return str_contains($system, 'risk assessment above is authoritative')
                && str_contains($system, '"missing": []')
                && ! str_contains($system, 'could NOT be calculated');
        });
    }

    public function test_a_truncated_answer_is_flagged_instead_of_ending_mid_sentence(): void
    {
        $this->fakeGemini(200, $this->geminiResponse('Asuransi jiwa adalah', [
            'candidates' => [['finishReason' => 'MAX_TOKENS']],
        ]));

        $reply = app(ChatAssistantService::class)
            ->reply($this->user, 'Apa itu asuransi jiwa?');

        $this->assertStringContainsString('Asuransi jiwa adalah', $reply['body_id']);
        $this->assertStringContainsString('Jawabannya kepotong', $reply['body_id']);
        // The contract is intact: the metadata still reports why it stopped.
        $this->assertSame('MAX_TOKENS', $reply['meta']['finish_reason']);
    }

    public function test_a_truncated_answer_is_flagged_in_english_for_english_users(): void
    {
        $this->fakeGemini(200, $this->geminiResponse('Life insurance is', [
            'candidates' => [['finishReason' => 'MAX_TOKENS']],
        ]));

        $reply = app(ChatAssistantService::class)
            ->reply($this->user, 'What is life insurance?');

        $this->assertStringContainsString('got cut off', $reply['body_id']);
        $this->assertStringNotContainsString('kepotong', $reply['body_id']);
    }

    public function test_no_request_is_made_to_google_without_a_key(): void
    {
        config(['services.gemini.key' => '']);
        Http::fake();

        app(ChatAssistantService::class)->connection();

        Http::assertNothingSent();
    }

    public function test_credential_check_sends_the_key_in_header_only_and_never_leaks_it(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models' => Http::response([
                'error' => [
                    'code' => 400,
                    'status' => 'INVALID_ARGUMENT',
                    'message' => 'API key not valid. Please pass a valid API key.',
                    'details' => [['reason' => 'API_KEY_INVALID']],
                ],
            ], 400),
        ]);

        $state = app(ChatAssistantService::class)->connection();
        $this->assertSame(GeminiException::INVALID_CREDENTIALS, $state['reason']);

        Http::assertSent(function (Request $request) {
            // The secret travels in the header, never in the URL or query string.
            return $request->hasHeader('x-goog-api-key', 'test-key-123')
                && ! str_contains($request->url(), 'test-key-123');
        });

        // The UI state never carries the secret either.
        $this->assertStringNotContainsString('test-key-123', json_encode($state));
    }

    public function test_a_successful_reply_recovers_the_connection_state(): void
    {
        // Page load sees an invalid key and caches "not active".
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models' => Http::sequence()
                ->push(['error' => ['code' => 400, 'status' => 'INVALID_ARGUMENT', 'details' => [['reason' => 'API_KEY_INVALID']]]], 400)
                ->push(['models' => [['name' => 'models/gemini-3.8-flash']]]),
            self::ENDPOINT => Http::response([
                'candidates' => [[
                    'content' => ['role' => 'model', 'parts' => [['text' => 'Jawaban asli dari Gemini.']]],
                    'finishReason' => 'STOP',
                ]],
            ]),
        ]);

        $this->actingAs($this->user)->get(route('chat.index'))->assertSee('AI nonaktif');

        // A later successful reply clears the cached failure...
        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Apa itu asuransi jiwa?'])
            ->assertOk();

        // ...and the next page load verifies again and shows "AI aktif".
        $this->actingAs($this->user)
            ->get(route('chat.index'))
            ->assertSee('AI aktif');
    }

    /**
     * The exact 429 body Google returned during the incident: a spent DAILY
     * free-tier quota (limit 20) with RetryInfo.retryDelay of ~19 hours.
     *
     * @return array<string, mixed>
     */
    private function exhaustedDailyQuotaBody(): array
    {
        return [
            'error' => [
                'code' => 429,
                'message' => "You exceeded your current quota, please check your plan and billing details. \n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 20, model: gemini-3.8-flash\nPlease retry in 19h40m45.98s.",
                'status' => 'RESOURCE_EXHAUSTED',
                'details' => [
                    [
                        '@type' => 'type.googleapis.com/google.rpc.QuotaFailure',
                        'violations' => [[
                            'quotaMetric' => 'generativelanguage.googleapis.com/generate_content_free_tier_requests',
                            'quotaId' => 'GenerateRequestsPerDayPerProjectPerModel-FreeTier',
                            'quotaValue' => '20',
                        ]],
                    ],
                    [
                        '@type' => 'type.googleapis.com/google.rpc.RetryInfo',
                        'retryDelay' => '70901s',
                    ],
                ],
            ],
        ];
    }

    public function test_a_spent_daily_quota_is_reported_honestly_and_not_retried(): void
    {
        Http::fake([
            self::ENDPOINT => Http::response($this->exhaustedDailyQuotaBody(), 429),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo, apa itu asuransi jiwa?'])
            ->assertStatus(503)
            ->assertJsonPath('error.reason', GeminiException::QUOTA_EXHAUSTED)
            // Not a transient problem: the UI must not invite a pointless retry.
            ->assertJsonPath('error.retryable', false);

        // Exactly one call: a 20-request daily budget must not be burned on
        // retries that cannot succeed.
        Http::assertSentCount(1);

        $body = (string) $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo lagi'])
            ->json('error.body_id');

        $this->assertStringContainsString('Kuota harian', $body);
        $this->assertStringNotContainsString('beberapa saat', $body);
        $this->assertStringNotContainsString('test-key-123', $body);
    }

    public function test_a_per_minute_rate_limit_is_retried_after_a_short_delay(): void
    {
        Http::fake([
            self::ENDPOINT => Http::sequence()
                ->push([
                    'error' => [
                        'code' => 429,
                        'message' => 'Quota exceeded for metric: generate_content_free_tier_requests, limit: 5. Please retry in 1s.',
                        'status' => 'RESOURCE_EXHAUSTED',
                        'details' => [
                            ['@type' => 'type.googleapis.com/google.rpc.QuotaFailure', 'violations' => [[
                                'quotaMetric' => 'generativelanguage.googleapis.com/generate_content_free_tier_requests',
                                'quotaId' => 'GenerateRequestsPerMinutePerProjectPerModel-FreeTier',
                            ]]],
                            ['@type' => 'type.googleapis.com/google.rpc.RetryInfo', 'retryDelay' => '1s'],
                        ],
                    ],
                ], 429)
                ->push($this->geminiResponse('Jawaban setelah percobaan kedua.')),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertOk()
            ->assertJsonPath('assistant.body_id', 'Jawaban setelah percobaan kedua.');

        // The retried call produced one assistant turn: no duplicate user message.
        $this->assertSame(2, $this->user->chatMessages()->count());
        $this->assertSame(1, $this->user->chatMessages()->where('role', 'user')->count());
        Http::assertSentCount(2);
    }

    public function test_an_overloaded_model_is_retried_as_a_transient_error(): void
    {
        // The real 503 seen in the logs: "experiencing high demand".
        Http::fake([
            self::ENDPOINT => Http::sequence()
                ->push(['error' => ['code' => 503, 'status' => 'UNAVAILABLE', 'message' => 'This model is currently experiencing high demand.']], 503)
                ->push($this->geminiResponse('Baik, ini jawabannya.')),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertOk()
            ->assertJsonPath('assistant.body_id', 'Baik, ini jawabannya.');

        Http::assertSentCount(2);
    }

    public function test_retries_are_capped(): void
    {
        config(['services.gemini.retry_attempts' => 2]);

        Http::fake([
            self::ENDPOINT => Http::response(['error' => ['code' => 503, 'status' => 'UNAVAILABLE', 'message' => 'high demand']], 503),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('error.reason', GeminiException::UNAVAILABLE)
            ->assertJsonPath('error.retryable', true);

        Http::assertSentCount(2);
    }

    public function test_a_retry_is_skipped_when_google_asks_for_a_long_wait(): void
    {
        config(['services.gemini.retry_attempts' => 3, 'services.gemini.retry_max_delay' => 20]);

        Http::fake([
            self::ENDPOINT => Http::response([
                'error' => [
                    'code' => 429,
                    'status' => 'RESOURCE_EXHAUSTED',
                    'message' => 'Rate limited. Please retry in 55.1s.',
                    'details' => [['@type' => 'type.googleapis.com/google.rpc.RetryInfo', 'retryDelay' => '55s']],
                ],
            ], 429),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('error.reason', GeminiException::RATE_LIMITED);

        // Waiting 55s inside a web request is worse than an honest error.
        Http::assertSentCount(1);
    }

    public function test_the_error_response_never_contains_the_key_or_quota_internals(): void
    {
        Http::fake([
            self::ENDPOINT => Http::response($this->exhaustedDailyQuotaBody(), 429),
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Halo']);

        $content = (string) $response->getContent();
        $this->assertStringNotContainsString('test-key-123', $content);
        $this->assertStringNotContainsString('generate_content_free_tier_requests', $content);
    }

    public function test_the_chat_page_renders_assistant_answers_with_safe_formatting(): void
    {
        $this->user->chatMessages()->create([
            'role' => 'assistant',
            'body_id' => 'Ada dua hal penting: **usia** dan *pengeluaran*.',
            'body_en' => 'Two things matter: **age** and *expenses*.',
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models' => Http::response([
                'models' => [['name' => 'models/gemini-3.8-flash']],
            ]),
        ]);

        $html = $this->actingAs($this->user)->get(route('chat.index'))->assertOk()->getContent();

        // The chat page itself never parses model text as HTML.
        $chatScript = substr($html, (int) strpos($html, "const form = document.getElementById('chatForm')"));

        $this->assertStringContainsString('renderMarkdown', $chatScript);
        $this->assertStringContainsString("document.createElement('strong')", $chatScript);
        $this->assertStringContainsString('createTextNode', $chatScript);
        $this->assertStringNotContainsString('innerHTML', $chatScript);
        // No third-party Markdown library is loaded (and therefore no XSS surface).
        $this->assertStringNotContainsString('marked.parse', $chatScript);
    }

    public function test_the_chat_page_distinguishes_a_spent_quota_from_a_hard_failure(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models' => Http::response(
                $this->exhaustedDailyQuotaBody()['error'],
                429,
            ),
        ]);

        $this->actingAs($this->user)
            ->get(route('chat.index'))
            ->assertOk()
            ->assertViewHas('aiState', GeminiException::QUOTA_EXHAUSTED)
            ->assertSee('Kuota AI hari ini sudah habis')
            ->assertDontSee('AI aktif');
    }
}
