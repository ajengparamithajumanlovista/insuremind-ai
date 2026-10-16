<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Policy extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'provider_name',
        'policy_name',
        'policy_number',
        'category',
        'premium_amount',
        'coverage_amount',
        'payment_period',
        'start_date',
        'next_due_date',
        'reminder_days',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'premium_amount' => 'float',
            'coverage_amount' => 'float',
            'start_date' => 'date',
            'next_due_date' => 'date',
            'is_active' => 'bool',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Days until the due date (negative means it has passed).
     */
    public function daysUntilDue(): ?int
    {
        if (! $this->next_due_date) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->next_due_date->startOfDay(), false);
    }

    /**
     * Reminder status: overdue | due_soon | upcoming | none.
     */
    public function reminderStatus(): string
    {
        $days = $this->daysUntilDue();

        if ($days === null) {
            return 'none';
        }

        if ($days < 0) {
            return 'overdue';
        }

        if ($days <= (int) $this->reminder_days) {
            return 'due_soon';
        }

        return 'upcoming';
    }

    /**
     * Monthly premium amount (normalized from the payment period).
     */
    public function monthlyPremium(): float
    {
        return round((float) $this->premium_amount / match ($this->payment_period) {
            'quarterly' => 3,
            'semiannual' => 6,
            'annual' => 12,
            default => 1,
        }, 2);
    }
}
