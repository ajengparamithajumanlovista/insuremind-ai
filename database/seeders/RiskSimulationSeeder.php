<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\LifeRiskSimulatorService;
use Illuminate\Database\Seeder;

/**
 * Sample life risk simulation history.
 *
 * Uses the same LifeRiskSimulatorService as the application, so the `inputs`
 * and `result` columns match what the controller writes when a user saves a
 * simulation. All three scenarios (life, critical_illness, income_loss) are
 * included so the simulator history looks complete.
 */
class RiskSimulationSeeder extends Seeder
{
    public function __construct(
        private readonly LifeRiskSimulatorService $simulator = new LifeRiskSimulatorService,
    ) {}

    public function run(): void
    {
        foreach ($this->scenarios() as $scenario => [$email, $coverage, $premium]) {
            $user = User::query()->where('email', $email)->first();
            $profile = $user?->profile;

            if (! $user || ! $profile || ! $profile->isReady()) {
                continue;
            }

            // Avoid duplicates when the seeder is run again.
            if ($user->riskSimulations()->where('scenario', $scenario)->exists()) {
                continue;
            }

            $result = $this->simulator->simulate($profile, $scenario, $coverage, $premium);

            $user->riskSimulations()->create([
                'scenario' => $scenario,
                'inputs' => [
                    'coverage' => $coverage,
                    'premium' => $premium,
                    'profile' => $result['inputs'],
                ],
                'result' => $result,
            ]);
        }
    }

    /**
     * @return array<string, array{0: string, 1: float, 2: float}>
     */
    private function scenarios(): array
    {
        return [
            'life' => ['user@insuremind.ai', 750_000_000, 1_950_000],
            'critical_illness' => ['user@insuremind.ai', 500_000_000, 850_000],
            'income_loss' => ['budi@insuremind.ai', 250_000_000, 520_000],
        ];
    }
}
