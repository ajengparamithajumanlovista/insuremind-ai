<?php

namespace App\Services;

use App\Models\UserProfile;
use App\Support\Bi;

/**
 * Simulator risiko hidup: memproyeksikan kondisi keuangan keluarga
 * sebelum dan sesudah terjadinya risiko (meninggal, sakit kritis,
 * kehilangan penghasilan) dengan dan tanpa perlindungan asuransi.
 *
 * Semua angka adalah estimasi sederhana (tanpa bunga/inflasi) agar
 * mudah dijelaskan dan diaudit oleh pengguna.
 */
class LifeRiskSimulatorService
{
    public const SCENARIOS = ['life', 'critical_illness', 'income_loss'];

    /**
     * @return array{
     *     scenario: array{key: string, label: array{id: string, en: string}, description: array{id: string, en: string}},
     *     horizon: int,
     *     inputs: array<string, float|int>,
     *     without: array<string, mixed>,
     *     with: array<string, mixed>,
     *     comparison: array<string, float|int>,
     *     timeline: array<int, array<string, float|int>>,
     *     notes: array<int, array{id: string, en: string}>
     * }
     */
    public function simulate(UserProfile $profile, string $scenarioKey, float $coverage = 0, float $premium = 0): array
    {
        $scenarioKey = in_array($scenarioKey, self::SCENARIOS, true) ? $scenarioKey : 'life';
        $scenario = $this->scenario($scenarioKey);

        $income = (float) $profile->monthly_income;
        $expense = (float) $profile->monthly_expense;
        $debt = (float) $profile->monthly_debt;
        $savings = (float) $profile->savings;
        $dependents = (int) $profile->dependents;

        $burn = $expense + $debt;
        $horizon = $scenario['horizon'];

        // Arus kas bulanan rumah tangga setelah risiko.
        $shockIncome = match ($scenarioKey) {
            'life' => $income * 0.30,             // sebagian penghasilan keluarga hilang
            'critical_illness' => $income * 0.50, // produktivitas turun saat pemulihan
            'income_loss' => 0.0,                 // penghasilan berhenti sementara
        };

        $shockExtraCost = match ($scenarioKey) {
            'life' => 0.0,
            'critical_illness' => max($income * 0.35, 2_000_000), // biaya pengobatan bulanan
            'income_loss' => 0.0,
        };

        $monthlyBurnWithout = ($burn + $shockExtraCost) - $shockIncome;
        $coverageInput = max(0, $coverage);
        $premiumInput = max(0, $premium);

        // Neto bulanan jika perlindungan aktif (premi mengurangi, manfaat menambah).
        $netMonthlyWith = $monthlyBurnWithout + $premiumInput;

        $shortfallWithout = $this->shortfall($savings, $monthlyBurnWithout, $horizon);
        $shortfallWith = $this->shortfall($savings + $coverageInput, $netMonthlyWith, $horizon);

        return [
            'scenario' => [
                'key' => $scenarioKey,
                'label' => $scenario['label'],
                'description' => $scenario['description'],
            ],
            'horizon' => $horizon,
            'inputs' => [
                'income' => $income,
                'expense' => $expense,
                'debt' => $debt,
                'savings' => $savings,
                'dependents' => $dependents,
                'coverage' => $coverageInput,
                'premium' => $premiumInput,
                'monthly_burn' => $burn,
                'burn_after_risk' => $monthlyBurnWithout,
            ],
            'without' => $shortfallWithout,
            'with' => $shortfallWith,
            'comparison' => [
                'months_without' => $shortfallWithout['months_covered'],
                'months_with' => $shortfallWith['months_covered'],
                'months_gained' => round($shortfallWith['months_covered'] - $shortfallWithout['months_covered'], 1),
                'shortfall_reduction' => round($shortfallWithout['deficit'] - $shortfallWith['deficit'], 2),
                'total_premium_cost' => round($premiumInput * $horizon, 2),
                'net_benefit' => round(($shortfallWithout['deficit'] - $shortfallWith['deficit']) - ($premiumInput * $horizon), 2),
            ],
            'timeline' => $this->timeline($savings, $monthlyBurnWithout, $savings + $coverageInput, $netMonthlyWith, $horizon),
            'notes' => $this->notes($profile, $scenarioKey, $coverageInput, $premiumInput),
        ];
    }

    /**
     * Metadata skenario risiko (label dan penjelasan dua bahasa).
     *
     * @return array{horizon: int, label: array{id: string, en: string}, description: array{id: string, en: string}}
     */
    private function scenario(string $key): array
    {
        return match ($key) {
            'critical_illness' => [
                'horizon' => 24,
                'label' => Bi::of('Sakit Kritis', 'Critical Illness'),
                'description' => Bi::of(
                    'Biaya pengobatan meningkat sementara penghasilan menurun selama masa pemulihan.',
                    'Medical costs rise while income drops during recovery.'
                ),
            ],
            'income_loss' => [
                'horizon' => 12,
                'label' => Bi::of('Kehilangan Penghasilan', 'Income Loss'),
                'description' => Bi::of(
                    'Pemutusan hubungan kerja atau berhentinya penghasilan utama keluarga.',
                    'Job loss or the sudden stop of the main household income.'
                ),
            ],
            default => [
                'horizon' => 60,
                'label' => Bi::of('Meninggal Dunia', 'Death of the Earner'),
                'description' => Bi::of(
                    'Pencari nafkah utama meninggal; keluarga menanggung pengeluaran tanpa penghasilan.',
                    'The main earner passes away; the family carries expenses with no income.'
                ),
            ],
        };
    }

    /**
     * Hitung berapa bulan tabungan mampu menutup kekurangan arus kas.
     *
     * @return array{months_covered: float, deficit: float, surplus: float, final_balance: float}
     */
    private function shortfall(float $savings, float $monthlyDeficit, int $horizon): array
    {
        $balance = $savings;
        $months = 0.0;

        for ($month = 0; $month < $horizon; $month++) {
            if ($monthlyDeficit <= 0) {
                // Tidak ada defisit: tabungan tetap utuh (tanpa memperhitungkan bunga).
                $months = (float) $horizon;
                break;
            }

            if ($balance >= $monthlyDeficit) {
                $balance -= $monthlyDeficit;
                $months++;
            } else {
                $balance = 0;
                break;
            }
        }

        $deficit = max(0, $monthlyDeficit * $horizon - $savings);
        $surplus = max(0, $savings - $monthlyDeficit * $horizon);

        return [
            'months_covered' => round($months, 1),
            'deficit' => round($monthlyDeficit > 0 ? $deficit : 0, 2),
            'surplus' => round($surplus, 2),
            'final_balance' => round($balance, 2),
        ];
    }

    /**
     * Proyeksi saldo tabungan bulan per bulan, dua skenario.
     *
     * @return array<int, array<string, float|int>>
     */
    private function timeline(float $startWithout, float $burnWithout, float $startWith, float $netWith, int $horizon): array
    {
        $rows = [];
        $without = $startWithout;
        $with = $startWith;

        $rows[] = ['month' => 0, 'without' => round($without, 2), 'with' => round($with, 2)];

        for ($month = 1; $month <= $horizon; $month++) {
            $without = max(0, $without - $burnWithout);
            $with = max(0, $with - $netWith);

            $rows[] = ['month' => $month, 'without' => round($without, 2), 'with' => round($with, 2)];
        }

        return $rows;
    }

    /**
     * Catatan interpretasi agar pengguna memahami asumsi simulator.
     *
     * @return array<int, array{id: string, en: string}>
     */
    private function notes(UserProfile $profile, string $scenarioKey, float $coverage, float $premium): array
    {
        $notes = [
            Bi::of(
                'Simulasi mengabaikan bunga, inflasi, dan kenaikan biaya medis tahunan.',
                'The simulation ignores interest, inflation, and annual medical cost increases.'
            ),
        ];

        if ((int) $profile->dependents === 0) {
            $notes[] = Bi::of(
                'Tanpa tanggungan, dampak keuangan dari risiko umumnya lebih ringan.',
                'With no dependents, the financial impact of risk is generally lighter.'
            );
        }

        if ($coverage <= 0) {
            $notes[] = Bi::of(
                'Uang pertanggungan belum diisi, sehingga skenario "dengan asuransi" belum memperhitungkan manfaat klaim.',
                'Coverage is not set, so the "with insurance" scenario does not yet include claim benefits.'
            );
        }

        if ($premium > 0 && $premium > (float) $profile->monthlySurplus()) {
            $notes[] = Bi::of(
                'Premi yang diisi melebihi surplus bulanan Anda saat ini.',
                'The premium entered exceeds your current monthly surplus.'
            );
        }

        if ($scenarioKey === 'income_loss') {
            $notes[] = Bi::of(
                'Skenario kehilangan penghasilan diasumsikan berlangsung satu tahun penuh.',
                'The income-loss scenario assumes a full year without income.'
            );
        }

        return $notes;
    }
}
