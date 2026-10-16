<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A policy document uploaded by a user.
 *
 * The primary status (`status`) is derived from the combination of extraction
 * and analysis results, so a document is never counted in two conflicting
 * categories in the admin statistics.
 */
class PolicyDocument extends Model
{
    /**
     * Possible final statuses for a document.
     */
    public const STATUSES = [
        'uploaded',   // newly uploaded, not yet validated
        'validated',  // file passed validation
        'processing', // extraction / analysis in progress
        'analysed',   // analysis complete
        'review',     // awaiting manual review
        'failed',     // processing failed
    ];

    public const STAGES = [
        'upload',
        'validation',
        'extraction',
        'analysis',
        'summary',
        'review',
    ];

    /**
     * Upload size limit (bytes) and accepted file types.
     */
    public const MAX_SIZE_BYTES = 10_485_760; // 10 MB

    public const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
    ];

    public const ALLOWED_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'original_name',
        'disk',
        'path',
        'mime_type',
        'size_bytes',
        'category',
        'status',
        'extraction_status',
        'analysis_status',
        'summary',
        'references',
        'failure_reason',
        'processed_at',
        'review_requested_at',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'summary' => 'array',
            'references' => 'array',
            'processed_at' => 'datetime',
            'review_requested_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(DocumentProcessingEvent::class)->orderBy('id');
    }

    public function reviewCases(): HasMany
    {
        return $this->hasMany(AiReviewCase::class);
    }

    /**
     * Concise status for the admin table badge.
     */
    public function statusTone(): string
    {
        return match ($this->status) {
            'analysed' => 'green',
            'failed' => 'rose',
            'review' => 'amber',
            'processing' => 'blue',
            default => 'grey',
        };
    }

    /**
     * Whether document processing has finished (successfully or unsuccessfully).
     */
    public function isFinished(): bool
    {
        return in_array($this->status, ['analysed', 'failed', 'review'], true);
    }
}
