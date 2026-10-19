<?php

namespace App\Http\Controllers;

use App\Exceptions\AiProviderException;
use App\Services\ChatAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatAssistantService $assistant,
    ) {}

    /**
     * Chatbot page and conversation history.
     */
    public function index(Request $request): View
    {
        $connection = $this->assistant->connection();
        $messages = $request->user()->chatMessages()
            ->reorder()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return view('chat.index', [
            'messages' => $messages,
            'aiEnabled' => $connection['connected'],
            'aiConfigured' => $connection['configured'],
            'aiReason' => $connection['reason'],
            'aiState' => $connection['state'],
            'engineLabel' => $this->assistant->engineLabel(),
            'modelLabel' => $connection['model'],
            'suggestions' => [
                ['id' => 'Bagaimana skor risiko saya?', 'en' => 'How is my risk score?'],
                ['id' => 'Berapa dana darurat yang saya butuhkan?', 'en' => 'How much emergency fund do I need?'],
                ['id' => 'Berapa anggaran premi yang wajar?', 'en' => 'What is a reasonable premium budget?'],
                ['id' => 'Apakah saya perlu asuransi jiwa?', 'en' => 'Do I need life insurance?'],
                ['id' => 'Kapan jatuh tempo polis saya?', 'en' => 'When are my policy due dates?'],
            ],
        ]);
    }

    /**
     * Accept and save the user's message, then return the assistant's reply (JSON).
     *
     * The user turn is persisted before the model is called, so a failed AI
     * request never loses the user's question. Failures return HTTP 503 with
     * the same JSON shape the frontend already understands.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:'.ChatAssistantService::MAX_MESSAGE_LENGTH],
        ]);

        $user = $request->user();

        // Oldest-first history WITHOUT the current message (it is created below).
        $history = $user->chatMessages()
            ->reorder()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(2 * (int) config('services.ai.history_limit', config('services.groq.history_limit', 12)))
            ->get()
            ->reverse()
            ->map(fn ($message) => [
                'role' => $message->role === 'assistant' ? 'assistant' : 'user',
                'content' => (string) $message->body_id,
            ])
            ->values()
            ->all();

        $question = trim($data['message']);

        $userMessage = $user->chatMessages()->create([
            'role' => 'user',
            'body_id' => $question,
            'body_en' => $question,
            'meta' => ['engine' => 'user'],
        ]);

        try {
            $reply = $this->assistant->reply($user, $question, $history);
        } catch (AiProviderException $e) {
            // Safe diagnostics only: provider, reason, status/model. Never the
            // key, the request headers, or the user's text.
            Log::error('ChatController: semua provider AI gagal menjawab.', [
                'provider' => $e->provider(),
                'reason' => $e->reason(),
                'details' => $e->context(),
            ]);

            // The live call just proved the cached connection state may be
            // stale (e.g. a revoked key or an exhausted quota).
            if (in_array($e->reason(), [
                AiProviderException::NOT_CONFIGURED,
                AiProviderException::INVALID_CREDENTIALS,
                AiProviderException::MODEL_NOT_FOUND,
                AiProviderException::RATE_LIMITED,
                AiProviderException::QUOTA_EXHAUSTED,
                AiProviderException::NETWORK,
                AiProviderException::UNAVAILABLE,
            ], true)) {
                $this->assistant->forgetConnectionState();
            }

            return response()->json([
                'user' => [
                    'id' => $userMessage->id,
                    'body_id' => $userMessage->body_id,
                    'body_en' => $userMessage->body_en,
                ],
                'assistant' => null,
                'error' => [
                    'reason' => $e->reason(),
                    'provider' => $e->provider(),
                    'retryable' => $e->isRetryable(),
                    'body_id' => $this->errorText($e->reason(), 'id'),
                    'body_en' => $this->errorText($e->reason(), 'en'),
                ],
            ], 503);
        }

        $assistantMessage = $user->chatMessages()->create([
            'role' => 'assistant',
            'body_id' => $reply['body_id'],
            'body_en' => $reply['body_en'],
            'meta' => $reply['meta'],
        ]);

        // A real answer proves the connection is healthy; refresh the badge.
        $this->assistant->forgetConnectionState();

        return response()->json([
            'user' => [
                'id' => $userMessage->id,
                'body_id' => $userMessage->body_id,
                'body_en' => $userMessage->body_en,
            ],
            'assistant' => [
                'id' => $assistantMessage->id,
                'body_id' => $assistantMessage->body_id,
                'body_en' => $assistantMessage->body_en,
                'engine' => $reply['meta']['engine'] ?? 'groq',
                'model' => $reply['meta']['model'] ?? null,
            ],
        ]);
    }

    /**
     * User-facing failure text. Never includes API details or credentials, so
     * the message stays meaningful to the user while the details go to the log.
     * Wording stays provider-neutral because either provider may fail last.
     */
    private function errorText(string $reason, string $locale): string
    {
        $messages = [
            AiProviderException::NOT_CONFIGURED => [
                'id' => 'Asisten AI belum aktif karena kunci API belum dikonfigurasi. Hubungi administrator aplikasi.',
                'en' => 'The AI assistant is not active because no API key is configured. Please contact the application administrator.',
            ],
            AiProviderException::INVALID_CREDENTIALS => [
                'id' => 'Asisten AI tidak dapat terhubung karena kunci API tidak valid. Hubungi administrator aplikasi.',
                'en' => 'The AI assistant cannot connect because the API key is invalid. Please contact the application administrator.',
            ],
            AiProviderException::MODEL_NOT_FOUND => [
                'id' => 'Model AI yang dikonfigurasi tidak tersedia. Hubungi administrator aplikasi.',
                'en' => 'The configured AI model is unavailable. Please contact the application administrator.',
            ],
            AiProviderException::RATE_LIMITED => [
                'id' => 'Asisten AI sedang mencapai batas permintaan. Coba lagi dalam beberapa saat.',
                'en' => 'The AI assistant has reached its request limit. Please try again in a moment.',
            ],
            AiProviderException::QUOTA_EXHAUSTED => [
                'id' => 'Kuota harian asisten AI hari ini sudah habis, jadi pertanyaan ini belum bisa dijawab. Asisten bisa dipakai lagi setelah kuota direset. Pertanyaanmu sudah tersimpan di riwayat.',
                'en' => 'Today\'s daily quota for the AI assistant is used up, so this question could not be answered. The assistant works again once the quota resets. Your question is saved in the history.',
            ],
            AiProviderException::INVALID_REQUEST => [
                'id' => 'Permintaan tidak dapat diproses oleh asisten AI. Coba ubah atau perpendek pertanyaan Anda.',
                'en' => 'The AI assistant could not process that request. Try rewording or shortening your question.',
            ],
            AiProviderException::EMPTY_RESPONSE => [
                'id' => 'Asisten AI tidak menghasilkan jawaban untuk pertanyaan ini. Coba tanyakan dengan cara lain.',
                'en' => 'The AI assistant produced no answer for this question. Try asking it differently.',
            ],
            AiProviderException::UNAVAILABLE => [
                'id' => 'Layanan asisten AI sedang tidak tersedia. Coba lagi nanti.',
                'en' => 'The AI assistant service is currently unavailable. Please try again later.',
            ],
            AiProviderException::NETWORK => [
                'id' => 'Asisten AI tidak dapat dihubungi karena masalah jaringan. Coba lagi.',
                'en' => 'The AI assistant could not be reached due to a network problem. Please try again.',
            ],
        ];

        return $messages[$reason][$locale]
            ?? ($locale === 'id'
                ? 'Asisten AI gagal menjawab karena kesalahan tak terduga. Coba lagi nanti.'
                : 'The AI assistant failed to answer due to an unexpected error. Please try again later.');
    }
}
