<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu tahapan pada riwayat pemrosesan dokumen.
 *
 * Contoh: dokumen diunggah → validasi selesai → ekstraksi dimulai →
 * ekstraksi selesai → analisis dimulai → analisis selesai.
 */
class DocumentProcessingEvent extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'policy_document_id',
        'stage',
        'status',
        'message',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(PolicyDocument::class, 'policy_document_id');
    }

    public function tone(): string
    {
        return match ($this->status) {
            'succeeded' => 'green',
            'failed' => 'rose',
            'started', 'running' => 'blue',
            default => 'grey',
        };
    }
}
