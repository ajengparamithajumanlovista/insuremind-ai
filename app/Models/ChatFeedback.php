<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Umpan balik pengguna atas sebuah jawaban asisten.
 *
 * Feedback negatif merupakan sinyal untuk pemeriksaan lebih lanjut, bukan
 * bukti bahwa jawaban salah (brief bagian 5F).
 */
class ChatFeedback extends Model
{
    public const SENTIMENTS = ['up', 'down'];

    public const REASONS = [
        'incorrect',
        'not_helpful',
        'missing_reference',
        'confusing',
        'other',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'chat_message_id',
        'user_id',
        'sentiment',
        'reason',
        'comment',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isNegative(): bool
    {
        return $this->sentiment === 'down';
    }
}
