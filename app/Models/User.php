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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Determine whether the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
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
            'password' => 'hashed',
        ];
    }
}
