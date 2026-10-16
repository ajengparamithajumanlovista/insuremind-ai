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
     * Roles recognized by the system (Role-Based Access Control).
     *
     * - user     : manages their own personal protection.
     * - reviewer : reviews assigned AI Quality Review cases.
     * - admin    : manages the catalog, accounts, and operational processes.
     */
    public const ROLES = ['user', 'reviewer', 'admin'];

    /**
     * Account status. Inactive accounts cannot sign in, but their activity
     * history is retained.
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
     * Whether the user can access the AI Quality Review area.
     */
    public function isReviewer(): bool
    {
        return in_array($this->role, ['reviewer', 'admin'], true);
    }

    /**
     * Whether the account is still allowed to use the platform.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Role label displayed in the interface.
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
     * Account status label displayed in the interface.
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
     * User's financial profile (used for risk analysis).
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Policies recorded by the user.
     */
    public function policies(): HasMany
    {
        return $this->hasMany(Policy::class);
    }

    /**
     * Protection needs analysis history.
     */
    public function riskAssessments(): HasMany
    {
        return $this->hasMany(RiskAssessment::class)->latest();
    }

    /**
     * Life risk simulation history.
     */
    public function riskSimulations(): HasMany
    {
        return $this->hasMany(RiskSimulation::class)->latest();
    }

    /**
     * Chatbot conversation history.
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->oldest();
    }

    /**
     * Policy documents uploaded by the user.
     */
    public function policyDocuments(): HasMany
    {
        return $this->hasMany(PolicyDocument::class)->latest('id');
    }

    /**
     * AI Quality Review cases originating from this user's activity.
     */
    public function reviewCases(): HasMany
    {
        return $this->hasMany(AiReviewCase::class)->latest('id');
    }

    /**
     * AI Quality Review cases handled by this user as a reviewer.
     */
    public function reviewedCases(): HasMany
    {
        return $this->hasMany(AiReviewCase::class, 'reviewed_by')->latest('id');
    }

    /**
     * Audit Log entries created by this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class)->latest('id');
    }

    /**
     * User profile, created automatically if one does not exist.
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
