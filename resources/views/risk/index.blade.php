@extends('layouts.app')

@section('title', 'Analisis Risiko')

@section('content')
    @php
        $toneClass = fn (?string $level) => match ($level) {
            'high' => 'rose',
            'medium' => 'amber',
            'low' => 'green',
            default => 'blue',
        };
        $pillClass = fn (?string $level) => match ($level) {
            'high' => 'pill-rose',
            'medium' => 'pill-amber',
            'low' => 'pill-green',
            default => 'pill-grey',
        };
        $levelLabel = [
            'high' => ['id' => 'Prioritas tinggi', 'en' => 'High priority'],
            'medium' => ['id' => 'Perlu perhatian', 'en' => 'Needs attention'],
            'low' => ['id' => 'Terjaga', 'en' => 'Well protected'],
        ];
        $categoryNames = [
            'health' => ['id' => 'Kesehatan', 'en' => 'Health'],
            'life' => ['id' => 'Jiwa', 'en' => 'Life'],
            'work' => ['id' => 'Risiko Kerja', 'en' => 'Work Risk'],
            'education' => ['id' => 'Pendidikan', 'en' => 'Education'],
        ];
    @endphp

    <div style="display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap;align-items:end" class="reveal">
        <div>
            <h1 class="page-title" data-id="Analisis Risiko" data-en="Risk Assessment">Analisis Risiko</h1>
            <p class="page-subtitle"
               data-id="Estimasi kebutuhan perlindungan berdasarkan profil finansial Anda. Hasil bersifat indikatif dan transparan."
               data-en="An estimate of your protection needs based on your financial profile. Results are indicative and transparent.">Estimasi kebutuhan perlindungan berdasarkan profil finansial Anda. Hasil bersifat indikatif dan transparan.</p>
        </div>

        @if ($assessment)
            <form method="POST" action="{{ route('risk.store') }}">
                @csrf
                <button type="submit" class="btn btn-primary"
                        data-id="Simpan Hasil Analisis" data-en="Save Assessment Result">Simpan Hasil Analisis</button>
            </form>
        @endif
    </div>

    @unless ($profile->isReady())
        <div class="card reveal reveal-1" style="margin-top:16px;border-left:4px solid var(--amber)">
            <div class="section-heading" style="margin-bottom:8px">
                <div>
                    <h2 data-id="Profil belum lengkap" data-en="Profile incomplete">Profil belum lengkap</h2>
                    <p data-id="Isi usia dan penghasilan bulanan untuk menjalankan analisis risiko."
                       data-en="Fill in your age and monthly income to run the risk analysis.">Isi usia dan penghasilan bulanan untuk menjalankan analisis risiko.</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary"
                   data-id="Isi Profil" data-en="Fill Profile">Isi Profil</a>
            </div>
        </div>
    @else
        <div class="grid grid-2 reveal reveal-1" style="align-items:start;margin-top:16px">
            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Skor Risiko Proteksi" data-en="Protection Risk Score">Skor Risiko Proteksi</h2>
                        <p data-id="Semakin tinggi skor, semakin besar kebutuhan perlindungan."
                           data-en="The higher the score, the greater the protection need.">Semakin tinggi skor, semakin besar kebutuhan perlindungan.</p>
                    </div>
                    <span class="pill {{ $pillClass($assessment['level']) }}">
                        {{ $levelLabel[$assessment['level']]['id'] ?? $assessment['level'] }}
                    </span>
                </div>

                <div style="font-size:38px;font-weight:800;letter-spacing:-1.5px;margin-bottom:10px">
                    {{ $assessment['score'] }}<span style="font-size:16px;color:var(--muted);font-weight:600">/100</span>
                </div>

                <div class="meter {{ $toneClass($assessment['level']) }}">
                    <span style="width: {{ $assessment['score'] }}%"></span>
                </div>
            </div>

            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Metrik Keuangan" data-en="Financial Metrics">Metrik Keuangan</h2>
                        <p data-id="Angka kunci yang membentuk skor di samping."
                           data-en="Key figures that shape the score alongside.">Angka kunci yang membentuk skor di samping.</p>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div>
                        <div class="stat-label" data-id="Surplus Bulanan" data-en="Monthly Surplus">Surplus Bulanan</div>
                        <div style="font-weight:700;font-size:15px;margin-top:4px">{{ \App\Support\Money::idr($assessment['metrics']['surplus']) }}</div>
                    </div>
                    <div>
                        <div class="stat-label" data-id="Dana Darurat" data-en="Emergency Fund">Dana Darurat</div>
                        <div style="font-weight:700;font-size:15px;margin-top:4px">{{ number_format($assessment['metrics']['emergency_months'], 1) }} <span data-id="bulan" data-en="months">bulan</span></div>
                    </div>
                    <div>
                        <div class="stat-label" data-id="Kebutuhan Jiwa" data-en="Life Cover Need">Kebutuhan Jiwa</div>
                        <div style="font-weight:700;font-size:15px;margin-top:4px">{{ \App\Support\Money::compact($assessment['metrics']['recommended_life']) }}</div>
                    </div>
                    <div>
                        <div class="stat-label" data-id="Selisih Perlindungan" data-en="Coverage Gap">Selisih Perlindungan</div>
                        <div style="font-weight:700;font-size:15px;margin-top:4px">{{ \App\Support\Money::compact($assessment['metrics']['coverage_gap']) }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if ($assessment)
            {{-- ── Alasan & saran ────────────────────────────── --}}
            <div class="grid grid-2 reveal reveal-2" style="align-items:start;margin-top:16px">
                <div class="card">
                    <div class="section-heading">
                        <div>
                            <h2 data-id="Faktor yang Menaikkan Skor" data-en="Factors Raising the Score">Faktor yang Menaikkan Skor</h2>
                            <p data-id="Alasan transparan di balik angka skor." data-en="The transparent reasons behind the score.">Alasan transparan di balik angka skor.</p>
                        </div>
                    </div>

                    @forelse ($assessment['reasons'] as $reason)
                        <div style="display:flex;gap:10px;padding:9px 0;border-bottom:1px solid var(--line);font-size:12.5px;color:#4f5c72">
                            <span style="color:var(--blue);font-weight:800">•</span>
                            <span data-id="{{ $reason['id'] }}" data-en="{{ $reason['en'] }}">{{ $reason['id'] }}</span>
                        </div>
                    @empty
                        <div class="empty"><span data-id="Tidak ada faktor risiko menonjol." data-en="No prominent risk factors.">Tidak ada faktor risiko menonjol.</span></div>
                    @endforelse
                </div>

                <div class="card">
                    <div class="section-heading">
                        <div>
                            <h2 data-id="Saran Tindakan" data-en="Suggested Actions">Saran Tindakan</h2>
                            <p data-id="Langkah praktis yang bisa Anda lakukan." data-en="Practical steps you can take.">Langkah praktis yang bisa Anda lakukan.</p>
                        </div>
                    </div>

                    @forelse ($assessment['suggestions'] as $suggestion)
                        <div style="display:flex;gap:10px;padding:9px 0;border-bottom:1px solid var(--line);font-size:12.5px;color:#4f5c72">
                            <span style="color:var(--green);font-weight:800">✓</span>
                            <span data-id="{{ $suggestion['id'] }}" data-en="{{ $suggestion['en'] }}">{{ $suggestion['id'] }}</span>
                        </div>
                    @empty
                        <div class="empty"><span data-id="Belum ada saran tambahan." data-en="No additional suggestions.">Belum ada saran tambahan.</span></div>
                    @endforelse
                </div>
            </div>
        @endif

        @if ($assessment)
            {{-- ── Kebutuhan per kategori ────────────────────── --}}
            <div class="card reveal reveal-3" style="margin-top:16px">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Kebutuhan per Kategori" data-en="Needs by Category">Kebutuhan per Kategori</h2>
                        <p data-id="Prioritas perlindungan untuk tiap jenis risiko."
                           data-en="Protection priority for each type of risk.">Prioritas perlindungan untuk tiap jenis risiko.</p>
                    </div>
                </div>

                <div class="grid grid-4">
                    @foreach ($assessment['categories'] as $key => $category)
                        <div class="stat">
                            <div class="stat-label">{{ $categoryNames[$key]['id'] ?? $key }}</div>
                            <div class="stat-note" style="margin-top:8px">
                                <span class="pill {{ $pillClass($category['level']) }}">
                                    {{ $levelLabel[$category['level']]['id'] ?? $category['level'] }}
                                </span>
                            </div>
                            <p style="font-size:11.5px;color:var(--muted);margin:9px 0 0"
                               data-id="{{ $category['note']['id'] }}" data-en="{{ $category['note']['en'] }}">{{ $category['note']['id'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ── Riwayat analisis tersimpan ────────────────────── --}}
        <div class="card reveal reveal-3" style="margin-top:16px">
            <div class="section-heading">
                <div>
                    <h2 data-id="Riwayat Analisis" data-en="Assessment History">Riwayat Analisis</h2>
                    <p data-id="Hasil yang pernah Anda simpan." data-en="Results you have saved.">Hasil yang pernah Anda simpan.</p>
                </div>
            </div>

            @if ($history->isEmpty())
                <div class="empty">
                    <span data-id="Belum ada hasil tersimpan. Klik Simpan Hasil Analisis untuk menyimpan."
                          data-en="No saved results yet. Click Save Assessment Result to store one.">Belum ada hasil tersimpan. Klik Simpan Hasil Analisis untuk menyimpan.</span>
                </div>
            @else
                <div class="table-wrap">
                    <table class="data">
                        <thead>
                            <tr>
                                <th data-id="Tanggal" data-en="Date">Tanggal</th>
                                <th data-id="Skor" data-en="Score">Skor</th>
                                <th data-id="Level" data-en="Level">Level</th>
                                <th data-id="Tanggungan" data-en="Dependents">Tanggungan</th>
                                <th class="num" data-id="Dana Darurat" data-en="Emergency Fund">Dana Darurat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($history as $row)
                                <tr>
                                    <td>{{ $row->created_at?->format('d M Y H:i') }}</td>
                                    <td>{{ $row->score }}/100</td>
                                    <td><span class="pill {{ $pillClass($row->level) }}">{{ $levelLabel[$row->level]['id'] ?? $row->level }}</span></td>
                                    <td>{{ data_get($row->snapshot, 'dependents', '—') }}</td>
                                    <td class="num">{{ number_format((float) data_get($row->metrics, 'emergency_months', 0), 1) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endunless
@endsection
