<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatMessage extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'role',
        'body_id',
        'body_en',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User feedback on this assistant message.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(ChatFeedback::class);
    }

    /**
     * Review case triggered by this message.
     */
    public function reviewCases(): HasMany
    {
        return $this->hasMany(AiReviewCase::class);
    }

    /**
     * Whether the answer engine actually included a verifiable reference.
     * Used to detect cases with "no cited reference".
     */
    public function hasVerifiableReference(): bool
    {
        $meta = $this->meta ?? [];

        if (is_array($meta) && ! empty($meta['references'])) {
            return true;
        }

        return $this->role === 'assistant'
            && is_string($this->body_id)
            && str_contains($this->body_id, 'Sumber:');
    }

    /**
     * Message content in the active language.
     */
    public function body(string $lang = 'id'): string
    {
        return $lang === 'en' ? (string) $this->body_en : (string) $this->body_id;
    }
}
