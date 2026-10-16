<?php

namespace App\Services;

use App\Models\InsuranceProduct;
use App\Models\UserProfile;
use App\Support\Bi;
use Illuminate\Support\Collection;

/**
 * Side-by-side comparison of insurance products.
 *
 * Builds comparison metrics, highlights the best values, estimates profile
 * fit, and generates rule-based recommendations with transparent reasoning.
 */
class InsuranceComparisonService
{
    /**
     * Retrieve products using the category/search filters.
     *
     * @return Collection<int, InsuranceProduct>
     */
    public function products(?string $category = null, ?string $search = null): Collection
    {
        return InsuranceProduct::query()
            ->when($category && in_array($category, InsuranceProduct::CATEGORIES, true), fn ($query) => $query->where('category', $category))
            ->when($search, fn ($query) => $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('provider', 'like', "%{$search}%");
            }))
            ->orderBy('category')
            ->orderBy('monthly_premium')
            ->get();
    }

    /**
     * Compare a set of products and determine which values to highlight.
     *
     * @param  Collection<int, InsuranceProduct>  $products
     * @return array{
     *     products: Collection<int, InsuranceProduct>,
     *     highlights: array<string, int>,
     *     metrics: array<int, array<string, mixed>>,
     *     categories: array<int, string>
     * }
     */
    public function compare(Collection $products): array
    {
        $products = $products->values();

        $highlights = [
            'cheapest' => $this->extremeIndex($products, 'monthly_premium', 'min'),
            'highest_coverage' => $this->extremeIndex($products, 'coverage_limit', 'max'),
            'best_value' => $this->extremeIndex($products, null, 'max', fn (InsuranceProduct $p) => $p->valueRatio()),
            'shortest_wait' => $this->extremeIndex($products, 'waiting_period', 'min'),
        ];

        $metrics = $products->map(function (InsuranceProduct $product, int $index) use ($highlights) {
            return [
                'id' => $product->id,
                'index' => $index,
                'value_ratio' => $product->valueRatio(),
                'highlight' => array_search($index, $highlights, true) ?: null,
                'is_sample' => $product->is_sample,
            ];
        })->all();

        return [
            'products' => $products,
            'highlights' => $highlights,
            'metrics' => $metrics,
            'categories' => $products->pluck('category')->unique()->values()->all(),
        ];
    }

    /**
     * Index of the product with the extreme value in a column (or via callback).
     *
     * @param  Collection<int, InsuranceProduct>  $products
     * @param  callable(InsuranceProduct): (float|int)|null  $callback
     */
    private function extremeIndex(Collection $products, ?string $column, string $direction, ?callable $callback = null): ?int
    {
        if ($products->isEmpty()) {
            return null;
        }

        $value = fn (InsuranceProduct $p) => (float) ($callback ? $callback($p) : $p->{$column});

        // Ignore zero/empty values so unfilled data is not highlighted by mistake.
        $candidates = $products->filter(fn (InsuranceProduct $p) => $value($p) > 0);

        if ($candidates->isEmpty()) {
            return null;
        }

        $best = $direction === 'max' ? $candidates->sortByDesc($value)->first() : $candidates->sortBy($value)->first();

        return $products->search(fn (InsuranceProduct $p) => $p->id === $best->id) ?: null;
    }

    /**
     * Product-to-user-profile fit score and its reasoning.
     *
     * @return array{score: int, label: array{id: string, en: string}, reasons: array<int, array{id: string, en: string}>}
     */
    public function suitability(InsuranceProduct $product, ?UserProfile $profile): array
    {
        $score = 50;
        $reasons = [];

        $coverage = (float) $product->coverage_limit;
        $premium = (float) $product->monthly_premium;
        $ratio = $product->valueRatio();

        // ── Value efficiency ────────────────────────────────────────────
        if ($ratio >= 100) {
            $score += 18;
            $reasons[] = Bi::of(
                'Rasio manfaat terhadap premi sangat tinggi (di atas 100 kali premi tahunan).',
                'The benefit-to-premium ratio is very high (above 100 times the annual premium).'
            );
        } elseif ($ratio >= 50) {
            $score += 10;
            $reasons[] = Bi::of(
                'Rasio manfaat terhadap premi tergolong baik.',
                'The benefit-to-premium ratio is good.'
            );
        } elseif ($ratio > 0 && $ratio < 15) {
            $score -= 10;
            $reasons[] = Bi::of(
                'Rasio manfaat terhadap premi relatif rendah; pertimbangkan produk lain.',
                'The benefit-to-premium ratio is relatively low; consider other products.'
            );
        }

        if ($profile) {
            $score += $this->profileBonus($product, $profile, $coverage, $reasons);
        }

        if ($product->is_sample) {
            $reasons[] = Bi::of(
                'Produk ini merupakan data simulasi untuk keperluan pembelajaran.',
                'This product is simulated data for learning purposes.'
            );
        }

        $score = (int) min(100, max(0, $score));
        $label = match (true) {
            $score >= 75 => Bi::of('Sangat sesuai', 'Highly suitable'),
            $score >= 55 => Bi::of('Cukup sesuai', 'Moderately suitable'),
            default => Bi::of('Perlu dipertimbangkan', 'Needs consideration'),
        };

        return ['score' => $score, 'label' => $label, 'reasons' => $reasons];
    }

    /**
     * Adjust score based on premium affordability and fit with the user's needs.
     *
     * @param  array<int, array{id: string, en: string}>  $reasons
     */
    private function profileBonus(InsuranceProduct $product, UserProfile $profile, float $coverage, array &$reasons): int
    {
        $bonus = 0;
        $premium = (float) $product->monthly_premium;
        $surplus = $profile->monthlySurplus();
        $maxComfortable = $surplus * 0.2;
        $dependents = (int) $profile->dependents;

        // ── Keterjangkauan ──────────────────────────────────────────────
        if ($premium > 0 && $maxComfortable > 0 && $premium <= $maxComfortable) {
            $bonus += 15;
            $reasons[] = Bi::of(
                'Premi berada di bawah 20% surplus bulanan Anda, sehingga masih nyaman dibayar.',
                'The premium is below 20% of your monthly surplus, so it stays comfortable.'
            );
        } elseif ($surplus > 0 && $premium > $surplus) {
            $bonus -= 20;
            $reasons[] = Bi::of(
                'Premi melebihi surplus bulanan Anda saat ini.',
                'The premium exceeds your current monthly surplus.'
            );
        } elseif ($maxComfortable > 0) {
            $bonus += 4;
            $reasons[] = Bi::of(
                'Premi masih terjangkau, tetapi mendekati batas 20% surplus bulanan.',
                'The premium is still affordable but close to the 20% surplus guideline.'
            );
        }

        // ── Category match with profile conditions ──────────────────────
        $bonus += match ($product->category) {
            'health' => $profile->has_health_insurance ? -8 : 12,
            'life' => (! $profile->has_life_insurance && $dependents > 0) ? 15 : 5,
            'work' => $profile->has_work_insurance ? 0 : 8,
            'education' => ($dependents > 0 && (float) $profile->education_cost > 0) ? 10 : 2,
            default => 0,
        };

        if (! $profile->has_health_insurance && $product->category === 'health') {
            $reasons[] = Bi::of(
                'Anda belum memiliki asuransi kesehatan, sehingga kategori ini relevan.',
                'You do not yet have health insurance, so this category is relevant.'
            );
        }

        if ($dependents > 0 && $product->category === 'life') {
            $reasons[] = Bi::of(
                'Anda memiliki tanggungan, sehingga perlindungan jiwa bernilai tinggi.',
                'You have dependents, so life cover carries high value.'
            );
        }

        // ── Adequacy of life insurance coverage ────────────────────────
        if ($product->category === 'life' && $coverage > 0) {
            $need = (float) $profile->monthly_income * 12 * ($dependents > 0 ? 10 : 5);

            if ($need > 0 && $coverage >= $need) {
                $bonus += 8;
                $reasons[] = Bi::of(
                    'Uang pertanggungan sudah menutup estimasi kebutuhan perlindungan jiwa Anda.',
                    'The coverage already meets your estimated life cover need.'
                );
            } elseif ($need > 0 && $coverage < $need * 0.5) {
                $bonus -= 8;
                $reasons[] = Bi::of(
                    'Uang pertanggungan masih jauh di bawah estimasi kebutuhan perlindungan jiwa Anda.',
                    'The coverage is still well below your estimated life cover need.'
                );
            }
        }

        return $bonus;
    }
}
