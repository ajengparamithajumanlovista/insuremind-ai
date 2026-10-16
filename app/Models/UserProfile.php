<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'age',
        'occupation',
        'city',
        'monthly_income',
        'monthly_expense',
        'monthly_debt',
        'savings',
        'dependents',
        'has_health_insurance',
        'has_life_insurance',
        'has_work_insurance',
        'existing_coverage',
        'education_years',
        'education_cost',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'monthly_income' => 'float',
            'monthly_expense' => 'float',
            'monthly_debt' => 'float',
            'savings' => 'float',
            'existing_coverage' => 'float',
            'education_cost' => 'float',
            'has_health_insurance' => 'bool',
            'has_life_insurance' => 'bool',
            'has_work_insurance' => 'bool',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Money available each month (income − expenses − installments).
     */
    public function monthlySurplus(): float
    {
        return max(0, (float) $this->monthly_income - (float) $this->monthly_expense - (float) $this->monthly_debt);
    }

    /**
     * How many months current savings can cover expenses if income is lost.
     */
    public function emergencyFundMonths(): float
    {
        $burn = (float) $this->monthly_expense + (float) $this->monthly_debt;

        if ($burn <= 0) {
            return 0.0;
        }

        return round((float) $this->savings / $burn, 1);
    }

    /**
     * Whether the profile has enough information for analysis.
     */
    public function isReady(): bool
    {
        return (int) $this->age > 0 && (float) $this->monthly_income > 0;
    }
}
