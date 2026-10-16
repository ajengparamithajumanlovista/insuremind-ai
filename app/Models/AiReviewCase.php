<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Kasus AI Quality Review.
 *
 * One row represents one unique case: an answer that needs human review.
 * Different reasons (missing references, negative feedback, or questionable
 * references) are stored in the `issues` column so one answer does not create
 * multiple duplicate cases.
 */
class AiReviewCase extends Model
{
    /**
     * Issue types recognized by the system.
     */
    public const ISSUE_TYPES = [
        'missing_reference',
        'mismatched_reference',
        'negative_feedback',
        'analysis_failed',
        'needs_verification',
    ];

    public const STATUSES = [
        'pending',     // awaiting review
        'in_review',   // under review
        'valid',       // marked valid
        'needs_fix',   // needs fixing
        'escalated',   // escalated
        'done',        // review complete
    ];

    public const DECISIONS = [
        'valid',
        'needs_fix',
        'escalated',
        'done',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'chat_message_id',
        'policy_document_id',
        'category',
        'issue_type',
        'issues',
        'question',
        'answer',
        'references',
        'status',
        'has_reference',
        'negative_feedback_count',
        'reviewed_by',
        'reviewed_at',
        'review_decision',
        'review_note',
    ];

    protected function casts(): array
    {
        return [
            'issues' => 'array',
            'references' => 'array',
            'has_reference' => 'bool',
            'negative_feedback_count' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function chatMessage(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(PolicyDocument::class, 'policy_document_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(AiReviewAction::class)->orderByDesc('id');
    }

    /**
     * Cases that have not yet completed review.
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['pending', 'in_review', 'needs_fix', 'escalated']);
    }

    public function scopeFinished($query)
    {
        return $query->whereIn('status', ['valid', 'done']);
    }

    public function statusTone(): string
    {
        return match ($this->status) {
            'pending' => 'amber',
            'in_review' => 'blue',
            'valid', 'done' => 'green',
            'needs_fix' => 'rose',
            'escalated' => 'purple',
            default => 'grey',
        };
    }

    public function isFinished(): bool
    {
        return in_array($this->status, ['valid', 'done'], true);
    }
}
