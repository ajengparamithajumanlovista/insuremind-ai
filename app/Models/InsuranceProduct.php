<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'description',
        'monthly_premium',
        'annual_premium',
        'coverage_limit',
        'benefits',
        'exclusions',
        'claim_documents',
        'waiting_period',
        'coverage_period',
        'entry_age_min',
        'entry_age_max',
        'claim_process',
        'is_sample',
        'is_active',
        'data_source',
        'source_url',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'description' => 'array',
            'monthly_premium' => 'float',
            'annual_premium' => 'float',
            'coverage_limit' => 'float',
            'benefits' => 'array',
            'exclusions' => 'array',
            'claim_documents' => 'array',
            'entry_age_min' => 'integer',
            'entry_age_max' => 'integer',
            'is_sample' => 'bool',
            'is_active' => 'bool',
        ];
    }

    /**
     * Produk yang masih ditawarkan pada katalog pengguna.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Riwayat perubahan katalog produk.
     */
    public function histories(): HasMany
    {
        return $this->hasMany(ProductHistory::class, 'insurance_product_id')->latest('id');
    }

    /**
     * Badge sumber data: membedakan data contoh dari data yang terverifikasi.
     *
     * @return array{label: array{id: string, en: string}, tone: string}
     */
    public function sourceBadge(): array
    {
        if ($this->is_sample) {
            return [
                'label' => ['id' => 'Data Contoh', 'en' => 'Sample Data'],
                'tone' => 'amber',
            ];
        }

        return [
            'label' => ['id' => 'Terverifikasi', 'en' => 'Verified'],
            'tone' => 'green',
        ];
    }

    /**
     * Deskripsi dalam bahasa aktif.
     */
    public function description(string $lang = 'id'): ?string
    {
        $value = $this->description;
        $text = is_array($value) ? ($value[$lang] ?? $value['id'] ?? null) : null;

        return is_string($text) && $text !== '' ? $text : null;
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
