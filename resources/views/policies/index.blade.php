@extends('layouts.app')

@section('title', 'Polis & Pengingat')
@push('head')
    <meta data-id-page-title data-title-id="Polis &amp; Pengingat" data-title-en="Policies &amp; Reminders">
@endpush

@section('content')
    @php
        $categoryNames = [
            'health' => ['id' => 'Kesehatan', 'en' => 'Health'],
            'life' => ['id' => 'Jiwa', 'en' => 'Life'],
            'work' => ['id' => 'Risiko Kerja', 'en' => 'Work Risk'],
            'education' => ['id' => 'Pendidikan', 'en' => 'Education'],
        ];
        $periodNames = [
            'monthly' => ['id' => 'Bulanan', 'en' => 'Monthly'],
            'quarterly' => ['id' => 'Triwulanan', 'en' => 'Quarterly'],
            'semiannual' => ['id' => 'Semesteran', 'en' => 'Semi-annual'],
            'annual' => ['id' => 'Tahunan', 'en' => 'Annual'],
        ];
        $statusPill = fn (string $status) => match ($status) {
            'overdue' => 'pill-rose',
            'due_soon' => 'pill-amber',
            'upcoming' => 'pill-blue',
            default => 'pill-grey',
        };
        $statusText = fn ($policy) => match ($policy->reminderStatus()) {
            'overdue' => 'Terlambat ' . abs((int) $policy->daysUntilDue()) . ' hari',
            'due_soon' => 'H-' . (int) $policy->daysUntilDue(),
            'upcoming' => 'Aman',
            default => 'Tanpa tanggal',
        };
    @endphp

    <h1 class="page-title reveal" data-id="Polis & Pengingat" data-en="Policies & Reminders">Polis &amp; Pengingat</h1>
    <p class="page-subtitle reveal reveal-1"
       data-id="Catat polis Anda agar premi dan jatuh tempo tidak terlewat."
       data-en="Record your policies so premiums and due dates are never missed.">Catat polis Anda agar premi dan jatuh tempo tidak terlewat.</p>

    {{-- ── Statistik ─────────────────────────────────────────── --}}
    <div class="grid grid-4 reveal reveal-1" style="margin-top:16px">
        <div class="stat">
            <div class="stat-label" data-id="Polis Aktif" data-en="Active Policies">Polis Aktif</div>
            <div class="stat-value">{{ $stats['count'] }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Premi Bulanan" data-en="Monthly Premium">Premi Bulanan</div>
            <div class="stat-value">{{ \App\Support\Money::compact($stats['monthly_premium']) }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Total Perlindungan" data-en="Total Coverage">Total Perlindungan</div>
            <div class="stat-value">{{ \App\Support\Money::compact($stats['total_coverage']) }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Segera Jatuh Tempo" data-en="Due Soon">Segera Jatuh Tempo</div>
            <div class="stat-value" style="color: {{ $stats['due_soon'] > 0 ? 'var(--rose)' : 'var(--green)' }}">{{ $stats['due_soon'] }}</div>
        </div>
    </div>

    {{-- ── Add a policy ─────────────────────────────────────── --}}
    <div class="card reveal reveal-2" style="margin-top:16px">
        <div class="section-heading">
            <div>
                <h2 data-id="Tambah Polis" data-en="Add a Policy">Tambah Polis</h2>
                <p data-id="Isi data polis untuk mengaktifkan pengingat jatuh tempo."
                   data-en="Fill in the policy details to enable due-date reminders.">Isi data polis untuk mengaktifkan pengingat jatuh tempo.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('policies.store') }}">
            @csrf

            <div class="grid grid-3">
                <div class="field">
                    <label for="provider_name" data-id="Perusahaan asuransi *" data-en="Insurance provider *">Perusahaan asuransi *</label>
                    <input type="text" id="provider_name" name="provider_name" value="{{ old('provider_name') }}"
                           data-id-placeholder="Contoh: SehatKu" data-en-placeholder="e.g. SehatKu" placeholder="Contoh: SehatKu" required>
                </div>

                <div class="field">
                    <label for="policy_name" data-id="Nama produk / polis" data-en="Product / policy name">Nama produk / polis</label>
                    <input type="text" id="policy_name" name="policy_name" value="{{ old('policy_name') }}"
                           data-id-placeholder="Contoh: Rawat Inap Plus" data-en-placeholder="e.g. Inpatient Plus" placeholder="Contoh: Rawat Inap Plus">
                </div>

                <div class="field">
                    <label for="policy_number" data-id="Nomor polis" data-en="Policy number">Nomor polis</label>
                    <input type="text" id="policy_number" name="policy_number" value="{{ old('policy_number') }}"
                           data-id-placeholder="Contoh: SK-2026-00123" data-en-placeholder="e.g. SK-2026-00123" placeholder="Contoh: SK-2026-00123">
                </div>

                <div class="field">
                    <label for="category" data-id="Kategori *" data-en="Category *">Kategori *</label>
                    <select id="category" name="category" required>
                        @foreach ($categories as $key)
                            <option value="{{ $key }}" @selected(old('category') === $key)>{{ $categoryNames[$key]['id'] ?? $key }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="premium_amount" data-id="Nominal premi (Rp) *" data-en="Premium amount (IDR) *">Nominal premi (Rp) *</label>
                    <input type="text" id="premium_amount" name="premium_amount" inputmode="numeric" value="{{ old('premium_amount') }}"
                           data-id-placeholder="Contoh: 450.000" data-en-placeholder="e.g. 450,000" placeholder="Contoh: 450.000" required>
                </div>

                <div class="field">
                    <label for="coverage_amount" data-id="Uang pertanggungan (Rp)" data-en="Coverage amount (IDR)">Uang pertanggungan (Rp)</label>
                    <input type="text" id="coverage_amount" name="coverage_amount" inputmode="numeric" value="{{ old('coverage_amount') }}"
                           data-id-placeholder="Contoh: 250.000.000" data-en-placeholder="e.g. 250,000,000" placeholder="Contoh: 250.000.000">
                </div>

                <div class="field">
                    <label for="payment_period" data-id="Periode pembayaran *" data-en="Payment period *">Periode pembayaran *</label>
                    <select id="payment_period" name="payment_period" required>
                        @foreach ($periodNames as $key => $label)
                            <option value="{{ $key }}" @selected(old('payment_period') === $key)>{{ $label['id'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="start_date" data-id="Tanggal mulai" data-en="Start date">Tanggal mulai</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}">
                </div>

                <div class="field">
                    <label for="next_due_date" data-id="Jatuh tempo berikutnya" data-en="Next due date">Jatuh tempo berikutnya</label>
                    <input type="date" id="next_due_date" name="next_due_date" value="{{ old('next_due_date') }}">
                </div>

                <div class="field">
                    <label for="reminder_days" data-id="Ingatkan sebelum (hari)" data-en="Remind before (days)">Ingatkan sebelum (hari)</label>
                    <input type="number" id="reminder_days" name="reminder_days" min="0" max="90" value="{{ old('reminder_days', 14) }}">
                </div>

                <div class="field" style="grid-column: span 2">
                    <label for="notes" data-id="Catatan" data-en="Notes">Catatan</label>
                    <input type="text" id="notes" name="notes" value="{{ old('notes') }}"
                           data-id-placeholder="Manfaat utama, nomor layanan, dsb."
                           data-en-placeholder="Key benefits, hotline, etc." placeholder="Manfaat utama, nomor layanan, dsb.">
                </div>
            </div>

            <div style="margin-top:16px">
                <button type="submit" class="btn btn-primary"
                        data-id="Simpan Polis" data-en="Save Policy">Simpan Polis</button>
            </div>
        </form>
    </div>

    {{-- ── Policy list ───────────────────────────────────────── --}}
    <div class="card reveal reveal-3" style="margin-top:16px">
        <div class="section-heading">
            <div>
                <h2 data-id="Daftar Polis" data-en="Policy List">Daftar Polis</h2>
                <p data-id="Semua polis yang Anda catat." data-en="All the policies you have recorded.">Semua polis yang Anda catat.</p>
            </div>
            <span class="subtle-link">{{ $policies->count() }} <span data-id="polis" data-en="policies">polis</span></span>
        </div>

        @if ($policies->isEmpty())
            <div class="empty">
                <strong data-id="Belum ada polis" data-en="No policies yet">Belum ada polis</strong>
                <span data-id="Tambahkan polis pertama Anda melalui formulir di atas."
                      data-en="Add your first policy using the form above.">Tambahkan polis pertama Anda melalui formulir di atas.</span>
            </div>
        @else
            <div style="display:grid;gap:14px">
                @foreach ($policies as $policy)
                    <div style="border:1px solid var(--line);border-radius:14px;padding:15px">
                        <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:start">
                            <div>
                                <div style="font-weight:700;font-size:13.5px">
                                    {{ $policy->policy_name ?: $policy->provider_name }}
                                </div>
                                <div style="font-size:11.5px;color:var(--muted);margin-top:3px">
                                    {{ $policy->provider_name }}
                                    @if ($policy->policy_number) · {{ $policy->policy_number }} @endif
                                    · {{ $categoryNames[$policy->category]['id'] ?? $policy->category }}
                                </div>
                            </div>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center">
                                <span class="pill {{ $statusPill($policy->reminderStatus()) }}">{{ $statusText($policy) }}</span>
                                <span class="pill {{ $policy->is_active ? 'pill-green' : 'pill-grey' }}">
                                    {{ $policy->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-4" style="margin-top:13px">
                            <div>
                                <div class="stat-label" data-id="Premi" data-en="Premium">Premi</div>
                                <div style="font-weight:650;font-size:12.5px;margin-top:3px">{{ \App\Support\Money::idr($policy->premium_amount) }}</div>
                            </div>
                            <div>
                                <div class="stat-label" data-id="Setara Bulanan" data-en="Monthly Equiv.">Setara Bulanan</div>
                                <div style="font-weight:650;font-size:12.5px;margin-top:3px">{{ \App\Support\Money::idr($policy->monthlyPremium()) }}</div>
                            </div>
                            <div>
                                <div class="stat-label" data-id="Pertanggungan" data-en="Coverage">Pertanggungan</div>
                                <div style="font-weight:650;font-size:12.5px;margin-top:3px">{{ \App\Support\Money::idr($policy->coverage_amount) }}</div>
                            </div>
                            <div>
                                <div class="stat-label" data-id="Jatuh Tempo" data-en="Due Date">Jatuh Tempo</div>
                                <div style="font-weight:650;font-size:12.5px;margin-top:3px">
                                    {{ $policy->next_due_date ? $policy->next_due_date->format('d M Y') : '—' }}
                                </div>
                            </div>
                        </div>

                        @if ($policy->notes)
                            <p style="font-size:11.5px;color:var(--muted);margin:11px 0 0">{{ $policy->notes }}</p>
                        @endif

                        @include('policies.partials.edit-form', ['policy' => $policy, 'categories' => $categories, 'categoryNames' => $categoryNames, 'periodNames' => $periodNames])
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
