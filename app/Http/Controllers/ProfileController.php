<?php

namespace App\Http\Controllers;

use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Formulir profil finansial pengguna.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'profile' => $request->user()->profileOrDefault(),
        ]);
    }

    /**
     * Simpan profil finansial.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'age' => ['nullable', 'integer', 'min:15', 'max:100'],
            'occupation' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'monthly_income' => ['nullable', 'string', 'max:30'],
            'monthly_expense' => ['nullable', 'string', 'max:30'],
            'monthly_debt' => ['nullable', 'string', 'max:30'],
            'savings' => ['nullable', 'string', 'max:30'],
            'dependents' => ['nullable', 'integer', 'min:0', 'max:20'],
            'existing_coverage' => ['nullable', 'string', 'max:30'],
            'education_years' => ['nullable', 'integer', 'min:0', 'max:25'],
            'education_cost' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'age' => 'usia',
            'monthly_income' => 'penghasilan bulanan',
            'monthly_expense' => 'pengeluaran bulanan',
            'monthly_debt' => 'cicilan bulanan',
            'savings' => 'tabungan',
            'dependents' => 'jumlah tanggungan',
        ]);

        $profile = $request->user()->profileOrDefault();

        $profile->fill([
            'age' => $data['age'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'city' => $data['city'] ?? null,
            'monthly_income' => Money::parse($data['monthly_income'] ?? 0),
            'monthly_expense' => Money::parse($data['monthly_expense'] ?? 0),
            'monthly_debt' => Money::parse($data['monthly_debt'] ?? 0),
            'savings' => Money::parse($data['savings'] ?? 0),
            'dependents' => (int) ($data['dependents'] ?? 0),
            'has_health_insurance' => $request->boolean('has_health_insurance'),
            'has_life_insurance' => $request->boolean('has_life_insurance'),
            'has_work_insurance' => $request->boolean('has_work_insurance'),
            'existing_coverage' => Money::parse($data['existing_coverage'] ?? 0),
            'education_years' => (int) ($data['education_years'] ?? 0),
            'education_cost' => Money::parse($data['education_cost'] ?? 0),
            'notes' => $data['notes'] ?? null,
        ]);

        $profile->save();

        return redirect()
            ->route('profile.edit')
            ->with('status', 'Profil berhasil disimpan.|Profile saved successfully.');
    }
}
