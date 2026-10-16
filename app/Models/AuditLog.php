<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * A single Audit Log row.
 *
 * This table is append-only: it has no `updated_at` column or update/delete
 * endpoint, and each row has an `event_id` (UUID) for traceability.
 * Corrections are made by writing a new row via `corrects_log_id`.
 */
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    /**
     * Activity categories used consistently throughout the admin pages.
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
     * Fields that MUST NOT be included in `before`/`after`. AuditLogger
     * recursively filters these keys before writing.
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
     * Human-readable category labels.
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
