<?php

namespace App\Http\Controllers;

use App\Models\InsuranceProduct;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PolicyController extends Controller
{
    /**
     * Daftar polis dan pengingat jatuh tempo.
     */
    public function index(Request $request): View
    {
        $policies = $request->user()->policies()->orderBy('next_due_date')->get();

        return view('policies.index', [
            'policies' => $policies,
            'active' => $policies->where('is_active', true)->values(),
            'categories' => InsuranceProduct::CATEGORIES,
            'stats' => [
                'count' => $policies->where('is_active', true)->count(),
                'monthly_premium' => round($policies->where('is_active', true)->sum(fn ($p) => $p->monthlyPremium()), 2),
                'total_coverage' => round($policies->where('is_active', true)->sum('coverage_amount'), 2),
                'due_soon' => $policies->filter(fn ($p) => $p->is_active && in_array($p->reminderStatus(), ['overdue', 'due_soon'], true))->count(),
            ],
        ]);
    }

    /**
     * Simpan polis baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $request->user()->policies()->create($this->payload($data));

        return redirect()
            ->route('policies.index')
            ->with('status', 'Polis berhasil ditambahkan.|Policy added successfully.');
    }

    /**
     * Perbarui data polis.
     */
    public function update(Request $request, int $policy): RedirectResponse
    {
        $model = $request->user()->policies()->findOrFail($policy);
        $data = $this->validated($request);

        $model->update($this->payload($data));

        return redirect()
            ->route('policies.index')
            ->with('status', 'Polis berhasil diperbarui.|Policy updated successfully.');
    }

    /**
     * Hapus polis.
     */
    public function destroy(Request $request, int $policy): RedirectResponse
    {
        $request->user()->policies()->findOrFail($policy)->delete();

        return redirect()
            ->route('policies.index')
            ->with('status', 'Polis berhasil dihapus.|Policy deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'provider_name' => ['required', 'string', 'max:120'],
            'policy_name' => ['nullable', 'string', 'max:120'],
            'policy_number' => ['nullable', 'string', 'max:80'],
            'category' => ['required', Rule::in(InsuranceProduct::CATEGORIES)],
            'premium_amount' => ['required', 'string', 'max:30'],
            'coverage_amount' => ['nullable', 'string', 'max:30'],
            'payment_period' => ['required', Rule::in(['monthly', 'quarterly', 'semiannual', 'annual'])],
            'start_date' => ['nullable', 'date'],
            'next_due_date' => ['nullable', 'date'],
            'reminder_days' => ['nullable', 'integer', 'min:0', 'max:90'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'provider_name' => 'nama perusahaan',
            'premium_amount' => 'nominal premi',
            'coverage_amount' => 'uang pertanggungan',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        return [
            'provider_name' => $data['provider_name'],
            'policy_name' => $data['policy_name'] ?? null,
            'policy_number' => $data['policy_number'] ?? null,
            'category' => $data['category'],
            'premium_amount' => Money::parse($data['premium_amount']),
            'coverage_amount' => Money::parse($data['coverage_amount'] ?? 0),
            'payment_period' => $data['payment_period'],
            'start_date' => $data['start_date'] ?? null,
            'next_due_date' => $data['next_due_date'] ?? null,
            'reminder_days' => (int) ($data['reminder_days'] ?? 14),
            'notes' => $data['notes'] ?? null,
            'is_active' => true,
        ];
    }
}