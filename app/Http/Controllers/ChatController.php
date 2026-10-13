<?php

namespace App\Http\Controllers;

use App\Services\ChatAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatAssistantService $assistant,
    ) {}

    /**
     * Halaman chatbot beserta riwayat percakapan.
     */
    public function index(Request $request): View
    {
        return view('chat.index', [
            'messages' => $request->user()->chatMessages()->take(50)->get(),
            'aiEnabled' => $this->assistant->hasApiKey(),
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
     * Terima pesan pengguna, simpan, dan kembalikan balasan asisten (JSON).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $user = $request->user();

        $history = $user->chatMessages()
            ->latest('id')
            ->take(10)
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

        $reply = $this->assistant->reply($user, $question, $history);

        $assistantMessage = $user->chatMessages()->create([
            'role' => 'assistant',
            'body_id' => $reply['body_id'],
            'body_en' => $reply['body_en'],
            'meta' => $reply['meta'],
        ]);

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
                'engine' => $reply['meta']['engine'] ?? 'rule-based',
            ],
        ]);
    }
}