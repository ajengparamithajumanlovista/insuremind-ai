@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $toneClass = fn (?string $level) => match ($level) {
            'high' => 'rose',
            'medium' => 'amber',
            'low' => 'green',
            default => 'blue',
        };
    @endphp

    <h1 class="page-title reveal" data-id="Ringkasan Proteksi Anda" data-en="Your Protection Overview">Ringkasan Proteksi Anda</h1>
    <p class="page-subtitle reveal reveal-1"
       data-id="Pantau skor risiko, anggaran premi, dan langkah berikutnya dalam satu tempat."
       data-en="Track your risk score, premium budget, and next steps in one place.">Pantau skor risiko, anggaran premi, dan langkah berikutnya dalam satu tempat.</p>

    {{-- ── Onboarding: profil belum lengkap ─────────────────── --}}
    @unless ($profile->isReady())
        <div class="card reveal reveal-1" style="margin-bottom:16px;border-left:4px solid var(--blue)">
            <div class="section-heading" style="margin-bottom:8px">
                <div>
                    <h2 data-id="Lengkapi profil untuk memulai" data-en="Complete your profile to begin">Lengkapi profil untuk memulai</h2>
                    <p data-id="Kami memerlukan usia dan penghasilan bulanan untuk menghitung analisis risiko dan simulasi."
                       data-en="We need your age and monthly income to calculate risk analysis and simulations.">Kami memerlukan usia dan penghasilan bulanan untuk menghitung analisis risiko dan simulasi.</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary"
                   data-id="Isi Profil" data-en="Fill Profile">Isi Profil</a>
            </div>
        </div>
    @endunless

    {{-- ── Stat cards ────────────────────────────────────────── --}}
    <div class="grid grid-4 reveal reveal-1" style="margin-bottom:16px">
        <div class="stat">
            <div class="stat-label" data-id="Skor Risiko" data-en="Risk Score">Skor Risiko</div>
            <div class="stat-value">{{ $assessment ? $assessment['score'] . '/100' : '—' }}</div>
            @if ($assessment)
                <div class="stat-note">
                    <span class="pill pill-{{ $toneClass($assessment['level']) }}">
                        {{ $assessment['level'] === 'high' ? 'Prioritas tinggi' : ($assessment['level'] === 'medium' ? 'Perlu perhatian' : 'Terjaga') }}
                    </span>
                </div>
            @else
                <div class="stat-note" data-id="Lengkapi profil" data-en="Profile required">Lengkapi profil</div>
            @endif
        </div>

        <div class="stat">
            <div class="stat-label" data-id="Premi Bulanan" data-en="Monthly Premium">Premi Bulanan</div>
            <div class="stat-value">{{ \App\Support\Money::compact($stats['monthly_premium']) }}</div>
            <div class="stat-note">{{ $stats['active_policies'] }} <span data-id="polis aktif" data-en="active policies">polis aktif</span></div>
        </div>

        <div class="stat">
            <div class="stat-label" data-id="Total Perlindungan" data-en="Total Coverage">Total Perlindungan</div>
            <div class="stat-value">{{ \App\Support\Money::compact($stats['total_coverage']) }}</div>
            <div class="stat-note" data-id="Akumulasi uang pertanggungan" data-en="Accumulated coverage">Akumulasi uang pertanggungan</div>
        </div>

        <div class="stat">
            <div class="stat-label" data-id="Dana Darurat" data-en="Emergency Fund">Dana Darurat</div>
            <div class="stat-value">{{ number_format($stats['emergency_months'], 1) }}<span style="font-size:13px"> bulan</span></div>
            <div class="stat-note">{{ $stats['emergency_months'] >= 6 ? 'Aman (≥ 6 bulan)' : 'Target 6 bulan' }}</div>
        </div>
    </div>

    <div class="grid grid-2" style="margin-bottom:16px">
        {{-- ── Skor risiko detail ────────────────────────────── --}}
        <div class="card reveal reveal-2">
            <div class="section-heading">
                <div>
                    <h2 data-id="Analisis Risiko" data-en="Risk Analysis">Analisis Risiko</h2>
                    <p data-id="Hasil terbaru berdasarkan profil Anda" data-en="Latest result based on your profile">Hasil terbaru berdasarkan profil Anda</p>
                </div>
                <a href="{{ route('risk.index') }}" class="subtle-link" data-id="Lihat detail →" data-en="View detail →">Lihat detail →</a>
            </div>

            @if ($assessment)
                <div class="meter {{ $toneClass($assessment['level']) }}" style="margin-bottom:14px">
                    <span style="width: {{ $assessment['score'] }}%"></span>
                </div>

                <ul style="margin:0 0 14px;padding-left:18px;font-size:12.5px;color:#4f5c72">
                    @foreach (array_slice($assessment['reasons'], 0, 3) as $reason)
                        <li style="margin-bottom:6px">{{ $reason['id'] }}</li>
                    @endforeach
                </ul>

                <a href="{{ route('risk.index') }}" class="btn btn-ghost btn-sm"
                   data-id="Jalankan ulang analisis" data-en="Re-run analysis">Jalankan ulang analisis</a>
            @else
                <div class="empty">
                    <strong data-id="Belum ada analisis" data-en="No analysis yet">Belum ada analisis</strong>
                    <span data-id="Lengkapi profil finansial Anda terlebih dahulu."
                          data-en="Complete your financial profile first.">Lengkapi profil finansial Anda terlebih dahulu.</span>
                </div>
            @endif
        </div>

        {{-- ── Pengingat jatuh tempo ─────────────────────────── --}}
        <div class="card reveal reveal-2">
            <div class="section-heading">
                <div>
                    <h2 data-id="Pengingat Jatuh Tempo" data-en="Due Reminders">Pengingat Jatuh Tempo</h2>
                    <p data-id="Polis yang perlu segera dibayar" data-en="Policies needing payment soon">Polis yang perlu segera dibayar</p>
                </div>
                <a href="{{ route('policies.index') }}" class="subtle-link" data-id="Kelola →" data-en="Manage →">Kelola →</a>
            </div>

            @forelse ($upcoming as $policy)
                <div style="display:flex;justify-content:space-between;gap:10px;padding:11px 0;border-bottom:1px solid var(--line)">
                    <div>
                        <div style="font-weight:650;font-size:13px">{{ $policy->policy_name ?: $policy->provider_name }}</div>
                        <div style="font-size:11px;color:var(--muted)">
                            {{ $policy->provider_name }} · {{ optional($policy->next_due_date)->format('d M Y') }}
                        </div>
                    </div>
                    <div style="text-align:right">
                        <span class="pill pill-{{ $policy->reminderStatus() === 'overdue' ? 'rose' : 'amber' }}">
                            {{ $policy->reminderStatus() === 'overdue'
                                ? 'Terlambat ' . abs($policy->daysUntilDue()) . ' hari'
                                : 'H-' . $policy->daysUntilDue() }}
                        </span>
                        <div style="font-size:11px;color:var(--muted);margin-top:4px">
                            {{ \App\Support\Money::idr($policy->premium_amount) }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty">
                    <strong data-id="Tidak ada tagihan mendesak" data-en="No urgent payments">Tidak ada tagihan mendesak</strong>
                    <span data-id="Semua polis Anda dalam kondisi aman."
                          data-en="All your policies are in good standing.">Semua polis Anda dalam kondisi aman.</span>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── Riwayat & akses cepat ─────────────────────────────── --}}
    <div class="grid grid-2">
        <div class="card reveal reveal-3">
            <div class="section-heading">
                <div>
                    <h2 data-id="Riwayat Simulasi" data-en="Simulation History">Riwayat Simulasi</h2>
                    <p data-id="Skenario risiko yang pernah Anda jalankan" data-en="Risk scenarios you have run">Skenario risiko yang pernah Anda jalankan</p>
                </div>
                <a href="{{ route('simulator.index') }}" class="subtle-link" data-id="Buka simulator →" data-en="Open simulator →">Buka simulator →</a>
            </div>

            @forelse ($recentSimulations as $simulation)
                @php
                    $labels = [
                        'life' => ['id' => 'Risiko Jiwa', 'en' => 'Life Risk'],
                        'critical_illness' => ['id' => 'Penyakit Kritis', 'en' => 'Critical Illness'],
                        'income_loss' => ['id' => 'Kehilangan Penghasilan', 'en' => 'Income Loss'],
                    ];
                    $label = $labels[$simulation->scenario] ?? ['id' => $simulation->scenario, 'en' => $simulation->scenario];
                @endphp
                <div style="display:flex;justify-content:space-between;gap:10px;padding:10px 0;border-bottom:1px solid var(--line);font-size:12.5px">
                    <div>
                        <span class="pill pill-blue" data-id="{{ $label['id'] }}" data-en="{{ $label['en'] }}">{{ $label['id'] }}</span>
                        <div style="color:var(--muted);font-size:11px;margin-top:4px">
                            {{ $simulation->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                    <div style="font-variant-numeric:tabular-nums;color:#4f5c72;text-align:right">
                        <div>{{ \App\Support\Money::compact(data_get($simulation->result, 'without.deficit', 0)) }}</div>
                        <div style="font-size:10.5px;color:var(--muted)" data-id="kekurangan tanpa proteksi" data-en="shortfall without cover">kekurangan tanpa proteksi</div>
                    </div>
                </div>
            @empty
                <div class="empty" style="padding:20px 16px">
                    <span data-id="Belum ada simulasi tersimpan. Jalankan simulator untuk menyimpan hasil."
                          data-en="No saved simulations yet. Run the simulator to save a result.">Belum ada simulasi tersimpan. Jalankan simulator untuk menyimpan hasil.</span>
                </div>
            @endforelse
        </div>

        <div class="card reveal reveal-3">
            <div class="section-heading">
                <div>
                    <h2 data-id="Akses Cepat" data-en="Quick Access">Akses Cepat</h2>
                    <p data-id="Fitur utama InsureMind AI" data-en="InsureMind AI key features">Fitur utama InsureMind AI</p>
                </div>
            </div>

            <div class="grid grid-2">
                <a href="{{ route('simulator.index') }}" class="btn btn-ghost" style="justify-content:flex-start"
                   data-id="Simulator Risiko" data-en="Risk Simulator">Simulator Risiko</a>
                <a href="{{ route('comparison.index') }}" class="btn btn-ghost" style="justify-content:flex-start"
                   data-id="Bandingkan Produk" data-en="Compare Products">Bandingkan Produk</a>
                <a href="{{ route('categories.show', 'health') }}" class="btn btn-ghost" style="justify-content:flex-start"
                   data-id="Kategori Produk" data-en="Product Categories">Kategori Produk</a>
                <a href="{{ route('chat.index') }}" class="btn btn-ghost" style="justify-content:flex-start"
                   data-id="Tanya Asisten AI" data-en="Ask AI Assistant">Tanya Asisten AI</a>
            </div>

            <div class="side-note" style="margin:16px 0 0">
                <strong data-id="Basis data produk" data-en="Product database">Basis data produk</strong>
                <span>{{ $stats['products'] }}
                    <span data-id="produk tersedia untuk dibandingkan." data-en="products available to compare.">produk tersedia untuk dibandingkan.</span>
                </span>
            </div>
        </div>
    </div>
@endsection
