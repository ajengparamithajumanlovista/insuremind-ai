<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Satu baris Audit Log.
 *
 * Tabel ini append-only: tidak ada `updated_at`, tidak ada endpoint
 * update/hapus, dan setiap baris memiliki `event_id` (UUID) untuk ditelusuri.
 * Koreksi dilakukan dengan menulis baris baru via `corrects_log_id`.
 */
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    /**
     * Kategori aktivitas yang dipakai konsisten di seluruh halaman admin.
     */
    public const CATEGORIES = [
        'user_management',
        'insurance_product',
        'policy_document',
        'ai_quality_review',
        'ai_analysis',
        'security',
        'authentication',
        'chat',
        'audit',
    ];

    /**
     * Kolom yang DILARANG diisi ke `before`/`after`. AuditLogger menyaring
     * kunci ini secara rekursif sebelum menulis.
     *
     * @var list<string>
     */
    public const REDACTED_KEYS = [
        'password',
        'password_confirmation',
        'current_password',
        'remember_token',
        'token',
        'api_key',
        'secret',
        'authorization',
        'access_token',
        'refresh_token',
        'session_id',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'actor_role',
        'actor_name',
        'category',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'subject_label',
        'result',
        'before',
        'after',
        'reason',
        'ip_address',
        'user_agent',
        'request_id',
        'corrects_log_id',
    ];

    protected function casts(): array
    {
        return [
            'before' => 'array',
            'after' => 'array',
            'subject_id' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AuditLog $log) {
            $log->event_id ??= (string) Str::uuid();
            $log->created_at ??= now();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function correctedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'corrects_log_id');
    }

    public function resultTone(): string
    {
        return $this->result === 'success' ? 'green' : 'rose';
    }

    /**
     * Label kategori yang dapat dibaca pengguna.
     */
    public function categoryLabel(): array
    {
        return match ($this->category) {
            'user_management' => ['id' => 'Manajemen Pengguna', 'en' => 'User Management'],
            'insurance_product' => ['id' => 'Produk Asuransi', 'en' => 'Insurance Product'],
            'policy_document' => ['id' => 'Dokumen Polis', 'en' => 'Policy Document'],
            'ai_quality_review' => ['id' => 'AI Quality Review', 'en' => 'AI Quality Review'],
            'ai_analysis' => ['id' => 'Analisis AI', 'en' => 'AI Analysis'],
            'security' => ['id' => 'Keamanan', 'en' => 'Security'],
            'authentication' => ['id' => 'Autentikasi', 'en' => 'Authentication'],
            'chat' => ['id' => 'Percakapan AI', 'en' => 'AI Chat'],
            'audit' => ['id' => 'Audit Log', 'en' => 'Audit Log'],
            default => ['id' => 'Lainnya', 'en' => 'Other'],
        };
    }
}
