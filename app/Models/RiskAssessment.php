<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskAssessment extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'category',
        'score',
        'level',
        'reasons',
        'suggestions',
        'metrics',
        'snapshot',
    ];

    protected function casts(): array
    {
        return [
            'reasons' => 'array',
            'suggestions' => 'array',
            'metrics' => 'array',
            'snapshot' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label warna untuk indikator tingkat kebutuhan.
     */
    public function tone(): string
    {
        return match ($this->level) {
            'high' => 'rose',
            'medium' => 'amber',
            default => 'green',
        };
    }
}
