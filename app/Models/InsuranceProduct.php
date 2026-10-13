<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceProduct extends Model
{
    /**
     * Kategori yang didukung platform.
     *
     * @var list<string>
     */
    public const CATEGORIES = ['health', 'life', 'work', 'education'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'provider',
        'name',
        'category',
        'monthly_premium',
        'annual_premium',
        'coverage_limit',
        'benefits',
        'exclusions',
        'claim_documents',
        'waiting_period',
        'coverage_period',
        'claim_process',
        'is_sample',
        'data_source',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'monthly_premium' => 'float',
            'annual_premium' => 'float',
            'coverage_limit' => 'float',
            'benefits' => 'array',
            'exclusions' => 'array',
            'claim_documents' => 'array',
            'is_sample' => 'bool',
        ];
    }

    /**
     * Rasio manfaat terhadap premi (semakin tinggi semakin efisien).
     */
    public function valueRatio(): float
    {
        $annual = (float) $this->annual_premium > 0
            ? (float) $this->annual_premium
            : (float) $this->monthly_premium * 12;

        if ($annual <= 0) {
            return 0.0;
        }

        return round((float) $this->coverage_limit / $annual, 1);
    }
}
