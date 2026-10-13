<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Peran yang dikenal sistem (Role-Based Access Control).
     *
     * - user     : mengelola perlindungan pribadinya sendiri.
     * - reviewer : memeriksa kasus AI Quality Review yang ditugaskan.
     * - admin    : mengelola katalog, akun, dan proses operasional.
     */
    public const ROLES = ['user', 'reviewer', 'admin'];

    /**
     * Status akun. Akun nonaktif tidak boleh login, namun riwayat
     * aktivitasnya tetap tersimpan.
     */
    public const STATUSES = ['active', 'inactive', 'suspended'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'last_activity_at',
        'admin_note',
    ];

    /**
     * Determine whether the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Apakah pengguna boleh mengakses area AI Quality Review.
     */
    public function isReviewer(): bool
    {
        return in_array($this->role, ['reviewer', 'admin'], true);
    }

    /**
     * Apakah akun masih diizinkan menggunakan platform.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Label peran untuk ditampilkan pada antarmuka.
     *
     * @return array{id: string, en: string}
     */
    public function roleLabel(): array
    {
        return match ($this->role) {
            'admin' => ['id' => 'Admin', 'en' => 'Admin'],
            'reviewer' => ['id' => 'Reviewer', 'en' => 'Reviewer'],
            default => ['id' => 'Pengguna Biasa', 'en' => 'Regular User'],
        };
    }

    /**
     * Label status akun untuk ditampilkan pada antarmuka.
     *
     * @return array{id: string, en: string}
     */
    public function statusLabel(): array
    {
        return match ($this->status) {
            'inactive' => ['id' => 'Nonaktif', 'en' => 'Inactive'],
            'suspended' => ['id' => 'Ditangguhkan', 'en' => 'Suspended'],
            default => ['id' => 'Aktif', 'en' => 'Active'],
        };
    }

    public function statusTone(): string
    {
        return match ($this->status) {
            'inactive' => 'grey',
            'suspended' => 'rose',
            default => 'green',
        };
    }

    /**
     * Profil finansial pengguna (dipakai untuk analisis risiko).
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Daftar polis yang dicatat pengguna.
     */
    public function policies(): HasMany
    {
        return $this->hasMany(Policy::class);
    }

    /**
     * Riwayat analisis kebutuhan perlindungan.
     */
    public function riskAssessments(): HasMany
    {
        return $this->hasMany(RiskAssessment::class)->latest();
    }

    /**
     * Riwayat simulasi risiko kehidupan.
     */
    public function riskSimulations(): HasMany
    {
        return $this->hasMany(RiskSimulation::class)->latest();
    }

    /**
     * Riwayat percakapan dengan chatbot.
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->oldest();
    }

    /**
     * Dokumen polis yang diunggah pengguna.
     */
    public function policyDocuments(): HasMany
    {
        return $this->hasMany(PolicyDocument::class)->latest('id');
    }

    /**
     * Kasus AI Quality Review yang berasal dari aktivitas pengguna ini.
     */
    public function reviewCases(): HasMany
    {
        return $this->hasMany(AiReviewCase::class)->latest('id');
    }

    /**
     * Kasus AI Quality Review yang ditangani pengguna ini sebagai reviewer.
     */
    public function reviewedCases(): HasMany
    {
        return $this->hasMany(AiReviewCase::class, 'reviewed_by')->latest('id');
    }

    /**
     * Catatan Audit Log yang dibuat oleh pengguna ini.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class)->latest('id');
    }

    /**
     * Profil pengguna, dibuat otomatis bila belum ada.
     */
    public function profileOrDefault(): UserProfile
    {
        return $this->profile ?? $this->profile()->create([]);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
