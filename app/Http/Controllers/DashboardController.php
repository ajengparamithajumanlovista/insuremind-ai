<?php

namespace App\Http\Controllers;

use App\Models\InsuranceProduct;
use App\Services\RiskAssessmentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly RiskAssessmentService $risk,
    ) {}

    /**
     * User status summary: risk score, premiums, policies, and next steps.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $profile = $user->profileOrDefault();

        $assessment = $profile->isReady() ? $this->risk->assess($profile) : null;

        $policies = $user->policies()->orderBy('next_due_date')->get();
        $activePolicies = $policies->where('is_active', true);

        $upcoming = $activePolicies
            ->filter(fn ($policy) => in_array($policy->reminderStatus(), ['overdue', 'due_soon'], true))
            ->sortBy('next_due_date')
            ->values();

        return view('dashboard', [
            'profile' => $profile,
            'assessment' => $assessment,
            'stats' => [
                'monthly_premium' => round($activePolicies->sum(fn ($p) => $p->monthlyPremium()), 2),
                'total_coverage' => round($activePolicies->sum('coverage_amount'), 2),
                'active_policies' => $activePolicies->count(),
                'emergency_months' => $profile->emergencyFundMonths(),
                'surplus' => $profile->monthlySurplus(),
                'products' => InsuranceProduct::count(),
            ],
            'upcoming' => $upcoming,
            'recentSimulations' => $user->riskSimulations()->take(4)->get(),
            'recentAssessments' => $user->riskAssessments()->take(4)->get(),
        ]);
    }
}
