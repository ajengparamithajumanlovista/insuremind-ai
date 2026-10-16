<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\RiskAssessmentService;
use Illuminate\Database\Seeder;

/**
 * Sample risk analysis history.
 *
 * Results are calculated using the same RiskAssessmentService as the
 * application, so the `reasons`, `suggestions`, `metrics`, and `snapshot`
 * structures match what the controller writes when a user runs an analysis.
 */
class RiskAssessmentSeeder extends Seeder
{
    public function __construct(
        private readonly RiskAssessmentService $risk = new RiskAssessmentService,
    ) {}

    public function run(): void
    {
        foreach (['user@insuremind.ai', 'budi@insuremind.ai', 'siti@insuremind.ai', 'andi@insuremind.ai'] as $email) {
            $user = User::query()->where('email', $email)->first();
            $profile = $user?->profile;

            if (! $user || ! $profile || ! $profile->isReady()) {
                continue;
            }

            // Avoid duplicates when the seeder is run again.
            if ($user->riskAssessments()->exists()) {
                continue;
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
        }
    }
}
