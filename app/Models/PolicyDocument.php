<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Dokumen polis yang diunggah pengguna.
 *
 * Status utama (`status`) diturunkan dari kombinasi ekstraksi & analisis
 * sehingga satu dokumen tidak pernah dihitung pada dua kategori yang saling
 * bertentangan pada statistik admin.
 */
class PolicyDocument extends Model
{
    /**
     * Status akhir yang mungkin dimiliki sebuah dokumen.
     */
    public const STATUSES = [
        'uploaded',   // baru diunggah, belum divalidasi
        'validated',  // berkas lolos validasi
        'processing', // ekstraksi / analisis sedang berjalan
        'analysed',   // selesai dianalisis
        'review',     // menunggu pemeriksaan manual
        'failed',     // gagal diproses
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
     * Batas ukuran unggahan (byte) dan tipe berkas yang diterima.
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
     * Status ringkas untuk badge tabel admin.
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
     * Apakah dokumen sudah selesai diproses (berhasil maupun gagal).
     */
    public function isFinished(): bool
    {
        return in_array($this->status, ['analysed', 'failed', 'review'], true);
    }
}
