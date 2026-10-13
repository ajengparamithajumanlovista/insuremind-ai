<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Isi pesan sesuai bahasa aktif.
     */
    public function body(string $lang = 'id'): string
    {
        return $lang === 'en' ? (string) $this->body_en : (string) $this->body_id;
    }
}
