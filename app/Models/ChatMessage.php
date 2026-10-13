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
     * Umpan balik pengguna atas pesan asisten ini.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(ChatFeedback::class);
    }

    /**
     * Kasus review yang dipicu oleh pesan ini.
     */
    public function reviewCases(): HasMany
    {
        return $this->hasMany(AiReviewCase::class);
    }

    /**
     * Apakah mesin jawaban benar-benar menyertakan referensi yang dapat
     * diverifikasi. Dipakai untuk mendeteksi kasus "tanpa referensi pasal".
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
     * Isi pesan sesuai bahasa aktif.
     */
    public function body(string $lang = 'id'): string
    {
        return $lang === 'en' ? (string) $this->body_en : (string) $this->body_id;
    }
}
