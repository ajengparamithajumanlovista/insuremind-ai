@extends('layouts.app')

@section('title', 'Simulator Risiko')

@section('content')
    @php
        $scenarios = [
            'life' => [
                'id' => 'Risiko Jiwa',
                'en' => 'Life Risk',
                'descId' => 'Pencari nafkah meninggal dan keluarga kehilangan sebagian besar penghasilan.',
                'descEn' => 'The earner passes away and the family loses most of its income.',
            ],
            'critical_illness' => [
                'id' => 'Penyakit Kritis',
                'en' => 'Critical Illness',
                'descId' => 'Produktivitas turun dan muncul biaya pengobatan bulanan yang besar.',
                'descEn' => 'Productivity drops and large monthly medical costs appear.',
            ],
            'income_loss' => [
                'id' => 'Kehilangan Penghasilan',
                'en' => 'Income Loss',
                'descId' => 'Penghasilan berhenti sementara, misalnya karena pemutusan hubungan kerja.',
                'descEn' => 'Income stops temporarily, for example due to a layoff.',
            ],
        ];
        $scenarioIcons = ['life' => '🛡', 'critical_illness' => '✚', 'income_loss' => '⇣'];
    @endphp

    <h1 class="page-title reveal" data-id="Simulator Risiko" data-en="Risk Simulator">Simulator Risiko</h1>
    <p class="page-subtitle reveal reveal-1"
       data-id="Lihat dampak keuangan bila risiko terjadi — dengan dan tanpa perlindungan asuransi."
       data-en="See the financial impact if risk occurs — with and without insurance protection.">Lihat dampak keuangan bila risiko terjadi — dengan dan tanpa perlindungan asuransi.</p>

    @unless ($profile->isReady())
        <div class="card reveal reveal-1" style="margin-top:16px;border-left:4px solid var(--amber)">
            <div class="section-heading" style="margin-bottom:8px">
                <div>
                    <h2 data-id="Profil belum lengkap" data-en="Profile incomplete">Profil belum lengkap</h2>
                    <p data-id="Isi usia dan penghasilan bulanan untuk menjalankan simulasi."
                       data-en="Fill in your age and monthly income to run the simulation.">Isi usia dan penghasilan bulanan untuk menjalankan simulasi.</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary"
                   data-id="Isi Profil" data-en="Fill Profile">Isi Profil</a>
            </div>
        </div>
    @else
        <div class="grid grid-3 reveal reveal-1" style="margin-top:16px">
            @foreach ($scenarios as $key => $meta)
                <a href="{{ route('simulator.index', ['scenario' => $key]) }}"
                   class="card" style="border-color: {{ $scenario === $key ? 'var(--blue)' : 'var(--line)' }};box-shadow: {{ $scenario === $key ? '0 8px 20px #315ee822' : 'none' }}">
                    <div style="display:flex;align-items:center;gap:10px">
                        <span style="font-size:19px">{{ $scenarioIcons[$key] }}</span>
                        <strong style="font-size:13.5px" data-id="{{ $meta['id'] }}" data-en="{{ $meta['en'] }}">{{ $meta['id'] }}</strong>
                    </div>
                    <p style="font-size:11.5px;color:var(--muted);margin:9px 0 0"
                       data-id="{{ $meta['descId'] }}" data-en="{{ $meta['descEn'] }}">{{ $meta['descId'] }}</p>
                </a>
            @endforeach
        </div>

        {{-- ── Formulir input ────────────────────────────────── --}}
        <div class="card reveal reveal-2" style="margin-top:16px">
            <div class="section-heading">
                <div>
                    <h2 data-id="Input Perlindungan" data-en="Protection Inputs">Input Perlindungan</h2>
                    <p data-id="Masukkan uang pertanggungan dan premi bulanan yang Anda pertimbangkan."
                       data-en="Enter the coverage and monthly premium you are considering.">Masukkan uang pertanggungan dan premi bulanan yang Anda pertimbangkan.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('simulator.store') }}">
                @csrf
                <input type="hidden" name="scenario" value="{{ $scenario }}">

                <div class="grid grid-2">
                    <div class="field">
                        <label for="coverage" data-id="Uang pertanggungan (Rp)" data-en="Coverage amount (IDR)">Uang pertanggungan (Rp)</label>
                        <input type="text" id="coverage" name="coverage" inputmode="numeric"
                               value="{{ old('coverage', $coverage ? number_format($coverage, 0, ',', '.') : '') }}"
                               data-id-placeholder="Contoh: 500.000.000"
                               data-en-placeholder="e.g. 500,000,000" placeholder="Contoh: 500.000.000">
                    </div>

                    <div class="field">
                        <label for="premium" data-id="Premi bulanan (Rp)" data-en="Monthly premium (IDR)">Premi bulanan (Rp)</label>
                        <input type="text" id="premium" name="premium" inputmode="numeric"
                               value="{{ old('premium', $premium ? number_format($premium, 0, ',', '.') : '') }}"
                               data-id-placeholder="Contoh: 500.000"
                               data-en-placeholder="e.g. 500,000" placeholder="Contoh: 500.000">
                    </div>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px">
                    <button type="submit" class="btn btn-primary"
                            data-id="Simpan Simulasi" data-en="Save Simulation">Simpan Simulasi</button>
                    <a href="{{ route('simulator.index', ['scenario' => $scenario]) }}" class="btn btn-ghost"
                       data-id="Hitung Ulang" data-en="Recalculate">Hitung Ulang</a>
                </div>
            </form>
        </div>

        @if ($result)
            {{-- ── Hasil: tanpa vs dengan perlindungan ──────── --}}
            <div class="grid grid-2 reveal reveal-2" style="align-items:start;margin-top:16px">
                <div class="card" style="border-left:4px solid var(--rose)">
                    <div class="section-heading">
                        <div>
                            <h2 data-id="Tanpa Perlindungan" data-en="Without Protection">Tanpa Perlindungan</h2>
                            <p data-id="Proyeksi bila tidak ada klaim asuransi."
                               data-en="Projection with no insurance claim.">Proyeksi bila tidak ada klaim asuransi.</p>
                        </div>
                        <span class="pill pill-rose" data-id="Rentan" data-en="Vulnerable">Rentan</span>
                    </div>

                    <div class="stat-label" data-id="Kekurangan Dana" data-en="Funding Shortfall">Kekurangan Dana</div>
                    <div style="font-size:24px;font-weight:800;letter-spacing:-1px;margin:6px 0 12px">
                        {{ \App\Support\Money::idr($result['without']['deficit']) }}
                    </div>

                    <div class="meter rose" style="margin-bottom:12px">
                        @php $covPct = $result['horizon'] > 0 ? min(100, ($result['without']['months_covered'] / $result['horizon']) * 100) : 0; @endphp
                        <span style="width: {{ $covPct }}%"></span>
                    </div>

                    <div style="font-size:12px;color:var(--muted)">
                        <span data-id="Tabungan bertahan" data-en="Savings last">Tabungan bertahan</span>
                        <strong style="color:var(--ink)">{{ number_format($result['without']['months_covered'], 1) }}</strong>
                        <span data-id="dari" data-en="of">dari</span>
                        <strong style="color:var(--ink)">{{ $result['horizon'] }}</strong>
                        <span data-id="bulan" data-en="months">bulan</span>
                    </div>
                </div>

                <div class="card" style="border-left:4px solid var(--green)">
                    <div class="section-heading">
                        <div>
                            <h2 data-id="Dengan Perlindungan" data-en="With Protection">Dengan Perlindungan</h2>
                            <p data-id="Proyeksi bila manfaat klaim cair tepat waktu."
                               data-en="Projection when the claim payout is received on time.">Proyeksi bila manfaat klaim cair tepat waktu.</p>
                        </div>
                        <span class="pill pill-green" data-id="Terlindungi" data-en="Protected">Terlindungi</span>
                    </div>

                    <div class="stat-label" data-id="Kekurangan Dana" data-en="Funding Shortfall">Kekurangan Dana</div>
                    <div style="font-size:24px;font-weight:800;letter-spacing:-1px;margin:6px 0 12px">
                        {{ \App\Support\Money::idr($result['with']['deficit']) }}
                    </div>

                    <div class="meter green" style="margin-bottom:12px">
                        @php $covPctWith = $result['horizon'] > 0 ? min(100, ($result['with']['months_covered'] / $result['horizon']) * 100) : 0; @endphp
                        <span style="width: {{ $covPctWith }}%"></span>
                    </div>

                    <div style="font-size:12px;color:var(--muted)">
                        <span data-id="Tabungan bertahan" data-en="Savings last">Tabungan bertahan</span>
                        <strong style="color:var(--ink)">{{ number_format($result['with']['months_covered'], 1) }}</strong>
                        <span data-id="dari" data-en="of">dari</span>
                        <strong style="color:var(--ink)">{{ $result['horizon'] }}</strong>
                        <span data-id="bulan" data-en="months">bulan</span>
                    </div>
                </div>
            </div>
        @if ($result)
            {{-- ── Ringkasan selisih ─────────────────────────── --}}
            <div class="grid grid-4 reveal reveal-3" style="margin-top:16px">
                <div class="stat">
                    <div class="stat-label" data-id="Bulan Tambahan" data-en="Extra Months">Bulan Tambahan</div>
                    <div class="stat-value">+{{ number_format($result['comparison']['months_gained'], 1) }}</div>
                </div>
                <div class="stat">
                    <div class="stat-label" data-id="Defisit Berkurang" data-en="Shortfall Reduced">Defisit Berkurang</div>
                    <div class="stat-value">{{ \App\Support\Money::compact($result['comparison']['shortfall_reduction']) }}</div>
                </div>
                <div class="stat">
                    <div class="stat-label" data-id="Total Premi" data-en="Total Premium">Total Premi</div>
                    <div class="stat-value">{{ \App\Support\Money::compact($result['comparison']['total_premium_cost']) }}</div>
                </div>
                <div class="stat">
                    <div class="stat-label" data-id="Manfaat Bersih" data-en="Net Benefit">Manfaat Bersih</div>
                    <div class="stat-value" style="color: {{ $result['comparison']['net_benefit'] >= 0 ? 'var(--green)' : 'var(--rose)' }}">
                        {{ \App\Support\Money::compact($result['comparison']['net_benefit']) }}
                    </div>
                </div>
            </div>

            {{-- ── Catatan asumsi ────────────────────────────── --}}
            <div class="card reveal reveal-3" style="margin-top:16px">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Catatan Interpretasi" data-en="Interpretation Notes">Catatan Interpretasi</h2>
                        <p data-id="Asumsi yang dipakai simulator." data-en="Assumptions used by the simulator.">Asumsi yang dipakai simulator.</p>
                    </div>
                </div>

                <ul style="margin:0;padding-left:18px;font-size:12.5px;color:#4f5c72;display:grid;gap:8px">
                    @foreach ($result['notes'] as $note)
                        <li data-id="{{ $note['id'] }}" data-en="{{ $note['en'] }}">{{ $note['id'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ── Riwayat simulasi ─────────────────────────────── --}}
        <div class="card reveal reveal-3" style="margin-top:16px">
            <div class="section-heading">
                <div>
                    <h2 data-id="Riwayat Simulasi" data-en="Simulation History">Riwayat Simulasi</h2>
                    <p data-id="Simulasi yang pernah Anda simpan." data-en="Simulations you have saved.">Simulasi yang pernah Anda simpan.</p>
                </div>
            </div>

            @if ($history->isEmpty())
                <div class="empty">
                    <span data-id="Belum ada simulasi tersimpan." data-en="No saved simulations yet.">Belum ada simulasi tersimpan.</span>
                </div>
            @else
                <div class="table-wrap">
                    <table class="data">
                        <thead>
                            <tr>
                                <th data-id="Tanggal" data-en="Date">Tanggal</th>
                                <th data-id="Skenario" data-en="Scenario">Skenario</th>
                                <th class="num" data-id="Uang Pertanggungan" data-en="Coverage">Uang Pertanggungan</th>
                                <th class="num" data-id="Premi" data-en="Premium">Premi</th>
                                <th class="num" data-id="Defisit Tanpa" data-en="Shortfall (No Cover)">Defisit Tanpa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($history as $row)
                                <tr>
                                    <td>{{ $row->created_at?->format('d M Y H:i') }}</td>
                                    <td>
                                        <span class="pill pill-blue">
                                            {{ $scenarios[$row->scenario]['id'] ?? $row->scenario }}
                                        </span>
                                    </td>
                                    <td class="num">{{ \App\Support\Money::compact(data_get($row->inputs, 'coverage', 0)) }}</td>
                                    <td class="num">{{ \App\Support\Money::compact(data_get($row->inputs, 'premium', 0)) }}</td>
                                    <td class="num">{{ \App\Support\Money::compact(data_get($row->result, 'without.deficit', 0)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endunless
@endsection
