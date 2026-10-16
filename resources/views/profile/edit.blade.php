@extends('layouts.app')

@section('title', 'Profil Keuangan')

@section('content')
    <h1 class="page-title reveal" data-id="Profil Keuangan" data-en="Financial Profile">Profil Keuangan</h1>
    <p class="page-subtitle reveal reveal-1"
       data-id="Data ini menjadi dasar perhitungan analisis risiko, simulasi, dan rekomendasi produk. Tidak ada data yang dibagikan."
       data-en="This data powers your risk analysis, simulations, and product recommendations. Nothing is shared.">Data ini menjadi dasar perhitungan analisis risiko, simulasi, dan rekomendasi produk. Tidak ada data yang dibagikan.</p>

    <form method="POST" action="{{ route('profile.update') }}" class="reveal reveal-1">
        @csrf
        @method('PUT')

        <div class="grid grid-2" style="align-items:start">
            {{-- ── Personal details & income ──────────────────── --}}
            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Data Dasar" data-en="Basic Data">Data Dasar</h2>
                        <p data-id="Usia dan penghasilan wajib diisi untuk mengaktifkan analisis."
                           data-en="Age and income are required to enable the analysis.">Usia dan penghasilan wajib diisi untuk mengaktifkan analisis.</p>
                    </div>
                </div>

                <div class="field">
                    <label for="age" data-id="Usia (tahun)" data-en="Age (years)">Usia (tahun)</label>
                    <input type="number" id="age" name="age" min="15" max="100"
                           value="{{ old('age', $profile->age) }}" placeholder="32">
                </div>

                <div class="field">
                    <label for="occupation" data-id="Pekerjaan" data-en="Occupation">Pekerjaan</label>
                    <input type="text" id="occupation" name="occupation"
                           value="{{ old('occupation', $profile->occupation) }}"
                           data-id-placeholder="Contoh: Staf keuangan"
                           data-en-placeholder="e.g. Finance staff" placeholder="Contoh: Staf keuangan">
                </div>

                <div class="field">
                    <label for="city" data-id="Kota" data-en="City">Kota</label>
                    <input type="text" id="city" name="city"
                           value="{{ old('city', $profile->city) }}"
                           data-id-placeholder="Contoh: Jakarta"
                           data-en-placeholder="e.g. Jakarta" placeholder="Contoh: Jakarta">
                </div>

                <div class="field">
                    <label for="monthly_income" data-id="Penghasilan bulanan (Rp)" data-en="Monthly income (IDR)">Penghasilan bulanan (Rp)</label>
                    <input type="text" id="monthly_income" name="monthly_income" inputmode="numeric"
                           value="{{ old('monthly_income', $profile->monthly_income ? number_format($profile->monthly_income, 0, ',', '.') : '') }}"
                           data-id-placeholder="Contoh: 12.000.000"
                           data-en-placeholder="e.g. 12,000,000" placeholder="Contoh: 12.000.000">
                </div>

                <div class="field">
                    <label for="dependents" data-id="Jumlah tanggungan" data-en="Number of dependents">Jumlah tanggungan</label>
                    <input type="number" id="dependents" name="dependents" min="0" max="20"
                           value="{{ old('dependents', $profile->dependents) }}" placeholder="2">
                </div>
            </div>

            {{-- ── Monthly cash flow ──────────────────────────── --}}
            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Arus Kas Bulanan" data-en="Monthly Cash Flow">Arus Kas Bulanan</h2>
                        <p data-id="Digunakan untuk menghitung surplus dan ketahanan dana darurat."
                           data-en="Used to calculate surplus and emergency-fund resilience.">Digunakan untuk menghitung surplus dan ketahanan dana darurat.</p>
                    </div>
                </div>

                <div class="field">
                    <label for="monthly_expense" data-id="Pengeluaran bulanan (Rp)" data-en="Monthly expenses (IDR)">Pengeluaran bulanan (Rp)</label>
                    <input type="text" id="monthly_expense" name="monthly_expense" inputmode="numeric"
                           value="{{ old('monthly_expense', $profile->monthly_expense ? number_format($profile->monthly_expense, 0, ',', '.') : '') }}"
                           data-id-placeholder="Contoh: 6.500.000"
                           data-en-placeholder="e.g. 6,500,000" placeholder="Contoh: 6.500.000">
                </div>

                <div class="field">
                    <label for="monthly_debt" data-id="Cicilan utang bulanan (Rp)" data-en="Monthly debt instalments (IDR)">Cicilan utang bulanan (Rp)</label>
                    <input type="text" id="monthly_debt" name="monthly_debt" inputmode="numeric"
                           value="{{ old('monthly_debt', $profile->monthly_debt ? number_format($profile->monthly_debt, 0, ',', '.') : '') }}"
                           data-id-placeholder="Contoh: 1.500.000"
                           data-en-placeholder="e.g. 1,500,000" placeholder="Contoh: 1.500.000">
                </div>

                <div class="field">
                    <label for="savings" data-id="Tabungan & aset likuid (Rp)" data-en="Savings & liquid assets (IDR)">Tabungan &amp; aset likuid (Rp)</label>
                    <input type="text" id="savings" name="savings" inputmode="numeric"
                           value="{{ old('savings', $profile->savings ? number_format($profile->savings, 0, ',', '.') : '') }}"
                           data-id-placeholder="Contoh: 40.000.000"
                           data-en-placeholder="e.g. 40,000,000" placeholder="Contoh: 40.000.000">
                </div>

                <div class="field">
                    <label for="existing_coverage" data-id="Uang pertanggungan yang sudah dimiliki (Rp)"
                           data-en="Existing coverage you already own (IDR)">Uang pertanggungan yang sudah dimiliki (Rp)</label>
                    <input type="text" id="existing_coverage" name="existing_coverage" inputmode="numeric"
                           value="{{ old('existing_coverage', $profile->existing_coverage ? number_format($profile->existing_coverage, 0, ',', '.') : '') }}"
                           data-id-placeholder="Contoh: 100.000.000"
                           data-en-placeholder="e.g. 100,000,000" placeholder="Contoh: 100.000.000">
                </div>
            </div>
        </div>

        {{-- ── Existing coverage & education plans ──────────────── --}}
        <div class="card reveal reveal-2" style="margin-top:16px">
            <div class="section-heading">
                <div>
                    <h2 data-id="Perlindungan Saat Ini & Rencana" data-en="Current Protection & Plans">Perlindungan Saat Ini &amp; Rencana</h2>
                    <p data-id="Membantu analisis menghindari rekomendasi yang tumpang tindih."
                       data-en="Helps the analysis avoid overlapping recommendations.">Membantu analisis menghindari rekomendasi yang tumpang tindih.</p>
                </div>
            </div>

            <div class="grid grid-3">
                <label class="check">
                    <input type="hidden" name="has_health_insurance" value="0">
                    <input type="checkbox" name="has_health_insurance" value="1"
                           @checked(old('has_health_insurance', $profile->has_health_insurance))>
                    <span data-id="Sudah punya asuransi kesehatan" data-en="Already have health insurance">Sudah punya asuransi kesehatan</span>
                </label>

                <label class="check">
                    <input type="hidden" name="has_life_insurance" value="0">
                    <input type="checkbox" name="has_life_insurance" value="1"
                           @checked(old('has_life_insurance', $profile->has_life_insurance))>
                    <span data-id="Sudah punya asuransi jiwa" data-en="Already have life insurance">Sudah punya asuransi jiwa</span>
                </label>

                <label class="check">
                    <input type="hidden" name="has_work_insurance" value="0">
                    <input type="checkbox" name="has_work_insurance" value="1"
                           @checked(old('has_work_insurance', $profile->has_work_insurance))>
                    <span data-id="Sudah punya perlindungan risiko kerja" data-en="Already have work-risk cover">Sudah punya perlindungan risiko kerja</span>
                </label>
            </div>

            <div class="grid grid-2" style="margin-top:16px">
                <div class="field">
                    <label for="education_years" data-id="Dana pendidikan dibutuhkan dalam (tahun)"
                           data-en="Education fund needed within (years)">Dana pendidikan dibutuhkan dalam (tahun)</label>
                    <input type="number" id="education_years" name="education_years" min="0" max="25"
                           value="{{ old('education_years', $profile->education_years) }}" placeholder="5">
                </div>

                <div class="field">
                    <label for="education_cost" data-id="Estimasi biaya pendidikan (Rp)"
                           data-en="Estimated education cost (IDR)">Estimasi biaya pendidikan (Rp)</label>
                    <input type="text" id="education_cost" name="education_cost" inputmode="numeric"
                           value="{{ old('education_cost', $profile->education_cost ? number_format($profile->education_cost, 0, ',', '.') : '') }}"
                           data-id-placeholder="Contoh: 150.000.000"
                           data-en-placeholder="e.g. 150,000,000" placeholder="Contoh: 150.000.000">
                </div>
            </div>

            <div class="field" style="margin-top:16px">
                <label for="notes" data-id="Catatan tambahan" data-en="Additional notes">Catatan tambahan</label>
                <textarea id="notes" name="notes" rows="3"
                          data-id-placeholder="Kondisi kesehatan, riwayat klaim, atau target keuangan…"
                          data-en-placeholder="Health condition, claim history, or financial goals…"
                          placeholder="Kondisi kesehatan, riwayat klaim, atau target keuangan…">{{ old('notes', $profile->notes) }}</textarea>
            </div>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px" class="reveal reveal-3">
            <button type="submit" class="btn btn-primary"
                    data-id="Simpan Profil" data-en="Save Profile">Simpan Profil</button>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost"
               data-id="Kembali ke Dashboard" data-en="Back to Dashboard">Kembali ke Dashboard</a>
        </div>
    </form>
@endsection
