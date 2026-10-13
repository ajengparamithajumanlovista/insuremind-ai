<?php

namespace App\Http\Controllers;

use App\Services\LifeRiskSimulatorService;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LifeSimulatorController extends Controller
{
    public function __construct(
        private readonly LifeRiskSimulatorService $simulator,
    ) {}

    /**
     * Halaman simulator risiko hidup.
     *
     * Mendukung GET (parameter pada URL) dan menampilkan hasil secara langsung.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $profile = $user->profileOrDefault();

        $scenario = (string) $request->query('scenario', 'life');
        $coverage = Money::parse($request->query('coverage', 0));
        $premium = Money::parse($request->query('premium', 0));

        $result = $profile->isReady()
            ? $this->simulator->simulate($profile, $scenario, $coverage, $premium)
            : null;

        return view('simulator.index', [
            'profile' => $profile,
            'scenario' => $scenario,
            'coverage' => $coverage,
            'premium' => $premium,
            'result' => $result,
            'history' => $user->riskSimulations()->take(6)->get(),
        ]);
    }

    /**
     * Simpan hasil simulasi sebagai riwayat.
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

        $data = $request->validate([
            'scenario' => ['required', 'string', 'in:life,critical_illness,income_loss'],
            'coverage' => ['nullable', 'string', 'max:30'],
            'premium' => ['nullable', 'string', 'max:30'],
        ]);

        $coverage = Money::parse($data['coverage'] ?? 0);
        $premium = Money::parse($data['premium'] ?? 0);

        $result = $this->simulator->simulate($profile, $data['scenario'], $coverage, $premium);

        $user->riskSimulations()->create([
            'scenario' => $data['scenario'],
            'inputs' => [
                'coverage' => $coverage,
                'premium' => $premium,
                'profile' => $result['inputs'],
            ],
            'result' => $result,
        ]);

        return redirect()
            ->route('simulator.index', [
                'scenario' => $data['scenario'],
                'coverage' => $coverage,
                'premium' => $premium,
            ])
            ->with('status', 'Simulasi berhasil disimpan.|Simulation saved successfully.');
    }
}