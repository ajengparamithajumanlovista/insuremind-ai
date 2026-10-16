<?php

namespace App\Services;

use App\Models\UserProfile;
use App\Support\Bi;

/**
 * Protection needs analysis (AI Risk Assessment).
 *
 * Results are transparent, rule-based estimates (not definitive predictions
 * that a risk will occur), so users can understand the reasoning.
 */
class RiskAssessmentService
{
    /**
     * @return array{
     *     score: int,
     *     level: string,
     *     metrics: array<string, float|int>,
     *     reasons: array<int, array{id: string, en: string}>,
     *     suggestions: array<int, array{id: string, en: string}>,
     *     categories: array<string, array{level: string, note: array{id: string, en: string}}>
     * }
     */
    public function assess(UserProfile $profile): array
    {
        $income = (float) $profile->monthly_income;
        $expense = (float) $profile->monthly_expense;
        $debt = (float) $profile->monthly_debt;
        $savings = (float) $profile->savings;
        $dependents = (int) $profile->dependents;
        $age = (int) $profile->age;
        $existing = (float) $profile->existing_coverage;

        $surplus = max(0, $income - $expense - $debt);
        $burn = $expense + $debt;
        $emergencyMonths = $burn > 0 ? round($savings / $burn, 1) : 0.0;

        $replacementYears = $dependents > 0 ? 10 : 5;
        $recommendedLife = $income * 12 * $replacementYears;
        $coverageGap = max(0, $recommendedLife - $existing);

        $score = 0;
        $reasons = [];
        $suggestions = [];

        // ── Age ─────────────────────────────────────────────────────────
        if ($age >= 50) {
            $score += 20;
            $reasons[] = Bi::of(
                "Usia {$age} tahun berada pada rentang di mana risiko kesehatan dan kehilangan penghasilan meningkat.",
                "At age {$age}, health risks and the risk of losing income tend to rise."
            );
        } elseif ($age >= 40) {
            $score += 12;
            $reasons[] = Bi::of(
                "Usia {$age} tahun mulai memasuki fase yang perlu penguatan perlindungan.",
                "At {$age}, you are entering a phase that benefits from stronger protection."
            );
        }

        // ── Dependents ──────────────────────────────────────────────────
        if ($dependents >= 1) {
            $score += 18;
            $reasons[] = Bi::of(
                "Terdapat {$dependents} tanggungan keluarga yang bergantung pada penghasilan Anda.",
                "You support {$dependents} family member(s) who rely on your income."
            );

            if ($dependents >= 3) {
                $score += 8;
            }
        } else {
            $reasons[] = Bi::of(
                'Belum ada tanggungan, sehingga kebutuhan penggantian penghasilan relatif lebih rendah.',
                'You have no dependents, so income replacement needs are relatively lower.'
            );
        }

        // ── Emergency fund ──────────────────────────────────────────────
        if ($emergencyMonths < 1) {
            $score += 30;
            $reasons[] = Bi::of(
                'Dana darurat belum tersedia: tabungan belum dapat menutup pengeluaran bulanan.',
                'No emergency fund yet: savings cannot cover monthly expenses.'
            );
            $suggestions[] = Bi::of(
                'Sisihkan dana darurat minimal 3–6 kali pengeluaran bulanan sebelum menambah premi baru.',
                'Build an emergency fund of at least 3–6 months of expenses before adding new premiums.'
            );
        } elseif ($emergencyMonths < 3) {
            $score += 22;
            $reasons[] = Bi::of(
                "Dana darurat hanya menutup sekitar {$emergencyMonths} bulan pengeluaran.",
                "Your emergency fund covers about {$emergencyMonths} months of expenses."
            );
            $suggestions[] = Bi::of(
                'Tingkatkan dana darurat menuju 3–6 bulan pengeluaran bulanan.',
                'Grow your emergency fund toward 3–6 months of monthly expenses.'
            );
        } elseif ($emergencyMonths < 6) {
            $score += 12;
            $reasons[] = Bi::of(
                "Dana darurat menutup sekitar {$emergencyMonths} bulan, mendekati memadai.",
                "Your emergency fund covers about {$emergencyMonths} months, nearing adequate."
            );
        } else {
            $reasons[] = Bi::of(
                'Dana darurat sudah memadai (di atas 6 bulan pengeluaran).',
                'Your emergency fund is adequate (more than 6 months of expenses).'
            );
        }

        // ── Debt payments ───────────────────────────────────────────────
        if ($debt > 0) {
            $score += 10;
            $reasons[] = Bi::of(
                'Masih terdapat cicilan/utang bulanan yang mengurangi kemampuan membayar premi.',
                'You still have monthly debt payments, which limits premium capacity.'
            );
        }

        // ── Cash flow ───────────────────────────────────────────────────
        if ($surplus <= 0 && $income > 0) {
            $score += 15;
            $reasons[] = Bi::of(
                'Penghasilan bulanan habis untuk pengeluaran dan cicilan, sehingga premi tambahan perlu berhati-hati.',
                'Your monthly income is fully absorbed by expenses and debt, so extra premiums need care.'
            );
            $suggestions[] = Bi::of(
                'Periksa kembali anggaran bulanan sebelum menambah komitmen premi.',
                'Review your monthly budget before adding new premium commitments.'
            );
        }

        // ── Existing coverage ──────────────────────────────────────────
        if (! $profile->has_health_insurance) {
            $score += 12;
            $reasons[] = Bi::of(
                'Belum ada asuransi kesehatan, padahal risiko biaya rawat inap dapat menguras tabungan.',
                'You have no health insurance, yet hospitalisation costs can drain savings.'
            );
            $suggestions[] = Bi::of(
                'Pertimbangkan asuransi kesehatan atau pastikan jaminan kesehatan tersedia.',
                'Consider health insurance or ensure health coverage is available.'
            );
        }

        if (! $profile->has_life_insurance) {
            $score += $dependents > 0 ? 18 : 8;
            $reasons[] = Bi::of(
                $dependents > 0
                    ? 'Belum ada asuransi jiwa sementara Anda menanggung keluarga.'
                    : 'Belum ada asuransi jiwa; kebutuhan relatif lebih rendah karena tanpa tanggungan.',
                $dependents > 0
                    ? 'You have no life insurance while supporting a family.'
                    : 'You have no life insurance; the need is relatively lower with no dependents.'
            );
            if ($dependents > 0) {
                $suggestions[] = Bi::of(
                    'Hitung kebutuhan uang pertanggungan jiwa setidaknya 10 kali penghasilan tahunan.',
                    'Aim for life cover of at least 10 times your annual income.'
                );
            }
        }

        if (! $profile->has_work_insurance) {
            $score += 6;
            $reasons[] = Bi::of(
                'Belum ada perlindungan risiko kerja (mis. BPJS Ketenagakerjaan atau jaminan perusahaan).',
                'No work-risk protection yet (e.g. BPJS Ketenagakerjaan or employer coverage).'
            );
            $suggestions[] = Bi::of(
                'Periksa jaminan kecelakaan kerja yang mungkin sudah tersedia dari perusahaan atau BPJS.',
                'Check the work-accident coverage you may already have from your employer or BPJS.'
            );
        }

        if ($coverageGap > 0) {
            $score += 15;
            $reasons[] = Bi::of(
                'Estimasi kebutuhan perlindungan jiwa melebihi uang pertanggungan yang dimiliki saat ini.',
                'Your estimated life cover need exceeds the coverage you currently hold.'
            );
        }

        // ── Saving capacity ─────────────────────────────────────────────
        $savingRate = $income > 0 ? round($surplus / $income * 100, 1) : 0.0;

        if ($income > 0) {
            if ($savingRate < 10) {
                $score += 8;
                $reasons[] = Bi::of(
                    "Rasio menabung sekitar {$savingRate}% dari penghasilan — relatif tipis untuk dana darurat dan premi.",
                    "Your saving rate is about {$savingRate}% of income — thin for both an emergency fund and premiums."
                );
                $suggestions[] = Bi::of(
                    'Targetkan menabung minimal 10% dari penghasilan setiap bulan.',
                    'Target saving at least 10% of your income each month.'
                );
            }
        } else {
            $score = max($score, 60);
            $reasons[] = Bi::of(
                'Penghasilan belum diisi sehingga analisis belum dapat sepenuhnya akurat.',
                'Income is not filled in, so the analysis cannot be fully accurate.'
            );
        }

        return $this->finalize($profile, $score, $reasons, $suggestions, [
            'surplus' => $surplus,
            'emergency_months' => $emergencyMonths,
            'saving_rate' => $savingRate,
            'replacement_years' => $replacementYears,
            'recommended_life' => $recommendedLife,
            'coverage_gap' => $coverageGap,
        ], $income);
    }

    /**
     * Normalize the score, determine the need level, and assign levels by category.
     *
     * @param  array<int, array{id: string, en: string}>  $reasons
     * @param  array<int, array{id: string, en: string}>  $suggestions
     * @param  array<string, float|int>  $metrics
     * @return array<string, mixed>
     */
    private function finalize(UserProfile $profile, int $score, array $reasons, array $suggestions, array $metrics, float $income): array
    {
        $score = (int) min(100, max(0, $score));
        $level = $score >= 67 ? 'high' : ($score >= 34 ? 'medium' : 'low');

        if ($suggestions === []) {
            $suggestions[] = Bi::of(
                'Pertahankan perlindungan yang sudah ada dan tinjau kembali setiap tahun.',
                'Maintain your current protection and review it every year.'
            );
        }

        return [
            'score' => $score,
            'level' => $level,
            'metrics' => $metrics,
            'reasons' => $reasons,
            'suggestions' => $suggestions,
            'categories' => $this->categoryLevels($profile, $metrics, $income),
        ];
    }

    /**
     * Protection need level for each insurance category.
     *
     * @param  array<string, float|int>  $metrics
     * @return array<string, array{level: string, note: array{id: string, en: string}}>
     */
    private function categoryLevels(UserProfile $profile, array $metrics, float $income): array
    {
        $age = (int) $profile->age;
        $dependents = (int) $profile->dependents;
        $surplus = (float) $metrics['surplus'];
        $gap = (float) $metrics['coverage_gap'];
        $educationCost = (float) $profile->education_cost;
        $educationYears = (int) $profile->education_years;

        return [
            'health' => $this->pick(
                ! $profile->has_health_insurance && ($age >= 40 || $dependents > 0),
                ! $profile->has_health_insurance,
                'Risiko biaya pengobatan tinggi dan belum ada asuransi kesehatan.',
                'High medical-cost risk and no health insurance yet.',
                'Belum ada asuransi kesehatan; biaya rawat inap perlu diantisipasi.',
                'No health insurance yet; hospitalisation costs should be anticipated.',
                'Perlindungan kesehatan Anda sudah terpenuhi.',
                'Your health protection is already in place.'
            ),
            'life' => $this->pick(
                $dependents > 0 && ! $profile->has_life_insurance,
                $dependents > 0 && $gap > 0,
                'Penghasilan Anda menopang keluarga tanpa perlindungan jiwa.',
                'Your income supports a family without life cover.',
                'Perlindungan jiwa perlu ditambah untuk menutup selisih kebutuhan.',
                'Life cover should be increased to close the gap.',
                'Kebutuhan perlindungan jiwa relatif rendah.',
                'Life cover needs are relatively low.'
            ),
            'work' => $this->pick(
                ! $profile->has_work_insurance && $surplus <= 0,
                ! $profile->has_work_insurance,
                'Tidak ada perlindungan risiko kerja dan tanpa cadangan arus kas.',
                'No work-risk cover and no cash-flow cushion.',
                'Periksa perlindungan kecelakaan kerja yang mungkin sudah tersedia.',
                'Check the work-accident cover you may already have.',
                'Risiko kerja Anda relatif tertangani.',
                'Your work risk is relatively covered.'
            ),
            'education' => $this->pick(
                $dependents > 0 && $educationCost > 0 && $educationYears > 0 && $educationYears <= 5,
                $dependents > 0 && $educationCost > 0,
                'Dana pendidikan anak dibutuhkan dalam waktu dekat.',
                'Your child\u2019s education funds are needed soon.',
                'Siapkan rencana dana pendidikan secara berkala.',
                'Plan the education fund on a regular basis.',
                'Belum ada kebutuhan dana pendidikan mendesak.',
                'No urgent education funding need.'
            ),
        ];
    }

    /**
     * @return array{level: string, note: array{id: string, en: string}}
     */
    private function pick(
        bool $high,
        bool $medium,
        string $highId,
        string $highEn,
        string $medId,
        string $medEn,
        string $lowId,
        string $lowEn
    ): array {
        if ($high) {
            return ['level' => 'high', 'note' => Bi::of($highId, $highEn)];
        }

        if ($medium) {
            return ['level' => 'medium', 'note' => Bi::of($medId, $medEn)];
        }

        return ['level' => 'low', 'note' => Bi::of($lowId, $lowEn)];
    }
}
