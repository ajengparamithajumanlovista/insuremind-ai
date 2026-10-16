<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * User feedback on an assistant answer.
 *
 * Negative feedback is a signal for further review, not proof that an answer
 * is wrong (brief section 5F).
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
     * The table name is specified explicitly because "feedback" is
     * uncountable, so Laravel's automatic pluralization produces
     * "chat_feedback", which does not match the `chat_feedbacks` table created
     * by the migration.
     *
     * @var string
     */
    protected $table = 'chat_feedbacks';

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
