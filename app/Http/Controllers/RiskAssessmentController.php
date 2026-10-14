<?php

namespace App\Http\Controllers;

use App\Services\RiskAssessmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiskAssessmentController extends Controller
{
    public function __construct(
        private readonly RiskAssessmentService $risk,
    ) {}

    /**
     * Tampilkan analisis risiko terbaru dari profil saat ini.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $profile = $user->profileOrDefault();

        $assessment = $profile->isReady() ? $this->risk->assess($profile) : null;

        return view('risk.index', [
            'profile' => $profile,
            'assessment' => $assessment,
            'history' => $user->riskAssessments()->take(10)->get(),
        ]);
    }

    /**
     * Jalankan (ulang) analisis dan simpan hasilnya sebagai riwayat.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->profileOrDefault();

        if (! $profile->isReady()) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Lengkapi usia dan penghasilan terlebih dahulu.|Please complete your age and income first.');
        }

        $assessment = $this->risk->assess($profile);

        $user->riskAssessments()->create([
            'category' => 'general',
            'score' => $assessment['score'],
            'level' => $assessment['level'],
            'reasons' => $assessment['reasons'],
            'suggestions' => $assessment['suggestions'],
            'metrics' => $assessment['metrics'] + ['categories' => $assessment['categories']],
            'snapshot' => [
                'monthly_income' => (float) $profile->monthly_income,
                'monthly_expense' => (float) $profile->monthly_expense,
                'monthly_debt' => (float) $profile->monthly_debt,
                'savings' => (float) $profile->savings,
                'dependents' => (int) $profile->dependents,
                'age' => (int) $profile->age,
                'existing_coverage' => (float) $profile->existing_coverage,
                'has_health_insurance' => (bool) $profile->has_health_insurance,
                'has_life_insurance' => (bool) $profile->has_life_insurance,
                'has_work_insurance' => (bool) $profile->has_work_insurance,
            ],
        ]);

        return redirect()
            ->route('risk.index')
            ->with('status', 'Analisis risiko berhasil dijalankan.|Risk analysis completed successfully.');
    }
}
