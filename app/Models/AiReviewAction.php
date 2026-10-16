<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Review history entry for an AI Quality Review case.
 *
 * Each reviewer decision is stored with its identity, timestamp, and notes so
 * the reasoning behind the decision can be traced. This table is append-only.
 */
class AiReviewAction extends Model
{
    public const ACTIONS = [
        'claim',       // start review
        'valid',       // mark as valid
        'needs_fix',   // needs fixing
        'escalate',    // escalate
        'done',        // mark as complete
        'reopen',      // reopen
        'note',        // note without changing the status
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ai_review_case_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'note',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(AiReviewCase::class, 'ai_review_case_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
