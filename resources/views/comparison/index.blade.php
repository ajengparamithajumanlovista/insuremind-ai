@extends('layouts.app')

@section('title', 'Perbandingan Produk')

@section('content')
    @php
        $categoryNames = [
            'health' => ['id' => 'Kesehatan', 'en' => 'Health'],
            'life' => ['id' => 'Jiwa', 'en' => 'Life'],
            'work' => ['id' => 'Risiko Kerja', 'en' => 'Work Risk'],
            'education' => ['id' => 'Pendidikan', 'en' => 'Education'],
        ];
        $highlightLabels = [
            'cheapest' => ['id' => 'Premi termurah', 'en' => 'Lowest premium'],
            'highest_coverage' => ['id' => 'Pertanggungan tertinggi', 'en' => 'Highest coverage'],
            'best_value' => ['id' => 'Nilai terbaik', 'en' => 'Best value'],
            'shortest_wait' => ['id' => 'Masa tunggu terpendek', 'en' => 'Shortest waiting period'],
        ];
        $highlights = $comparison['highlights'];
        $highlightByIndex = [];
        foreach ($highlights as $key => $index) {
            if ($index !== null) {
                $highlightByIndex[$index] = $highlightLabels[$key] ?? ['id' => $key, 'en' => $key];
            }
        }
    @endphp

    <h1 class="page-title reveal" data-id="Perbandingan Produk" data-en="Product Comparison">Perbandingan Produk</h1>
    <p class="page-subtitle reveal reveal-1"
       data-id="Bandingkan hingga 3 produk berdampingan: premi, pertanggungan, masa tunggu, dan nilai."
       data-en="Compare up to 3 products side by side: premium, coverage, waiting period, and value.">Bandingkan hingga 3 produk berdampingan: premi, pertanggungan, masa tunggu, dan nilai.</p>

    {{-- ── Filter ─────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('comparison.index') }}" class="card reveal reveal-1" style="margin-top:16px">
        <div class="grid grid-3" style="align-items:end">
            <div class="field">
                <label for="category" data-id="Kategori" data-en="Category">Kategori</label>
                <select id="category" name="category">
                    <option value="" data-id="Semua kategori" data-en="All categories">Semua kategori</option>
                    @foreach ($categoryNames as $key => $label)
                        <option value="{{ $key }}" @selected($category === $key)>{{ $label['id'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="q" data-id="Cari produk / perusahaan" data-en="Search product / provider">Cari produk / perusahaan</label>
                <input type="text" id="q" name="q" value="{{ $search }}"
                       data-id-placeholder="Contoh: kesehatan atau SehatKu"
                       data-en-placeholder="e.g. health or SehatKu" placeholder="Contoh: kesehatan atau SehatKu">
            </div>

            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <button type="submit" class="btn btn-primary"
                        data-id="Terapkan Filter" data-en="Apply Filter">Terapkan Filter</button>
                <a href="{{ route('comparison.index') }}" class="btn btn-ghost"
                   data-id="Reset" data-en="Reset">Reset</a>
            </div>
        </div>
    </form>

    @if ($selected->isEmpty())
        <div class="empty reveal reveal-2" style="margin-top:16px">
            <strong data-id="Belum ada produk untuk dibandingkan" data-en="No products to compare yet">Belum ada produk untuk dibandingkan</strong>
            <span data-id="Tambahkan produk pada seeder atau ubah filter pencarian."
                  data-en="Add products via the seeder or adjust the search filter.">Tambahkan produk pada seeder atau ubah filter pencarian.</span>
        </div>
    @else
        {{-- ── Tabel perbandingan ────────────────────────── --}}
        <div class="card reveal reveal-2" style="margin-top:16px">
            <div class="section-heading">
                <div>
                    <h2 data-id="Perbandingan Berdampingan" data-en="Side-by-Side Comparison">Perbandingan Berdampingan</h2>
                    <p data-id="Nilai terbaik setiap metrik ditandai."
                       data-en="The best value in each metric is highlighted.">Nilai terbaik setiap metrik ditandai.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th data-id="Metrik" data-en="Metric">Metrik</th>
                            @foreach ($selected as $index => $product)
                                <th>
                                    {{ $product->name }}
                                    <div style="font-weight:600;text-transform:none;letter-spacing:0;font-size:10.5px;color:var(--muted);margin-top:3px">
                                        {{ $product->provider }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-id="Kategori" data-en="Category">Kategori</td>
                            @foreach ($selected as $product)
                                <td>
                                    <span class="pill pill-blue">{{ $categoryNames[$product->category]['id'] ?? $product->category }}</span>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td data-id="Premi Bulanan" data-en="Monthly Premium">Premi Bulanan</td>
                            @foreach ($selected as $index => $product)
                                <td class="num {{ ($highlights['cheapest'] ?? null) === $index ? 'best' : '' }}">
                                    {{ \App\Support\Money::idr($product->monthly_premium) }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td data-id="Uang Pertanggungan" data-en="Coverage Limit">Uang Pertanggungan</td>
                            @foreach ($selected as $index => $product)
                                <td class="num {{ ($highlights['highest_coverage'] ?? null) === $index ? 'best' : '' }}">
                                    {{ \App\Support\Money::idr($product->coverage_limit) }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td data-id="Masa Tunggu (hari)" data-en="Waiting Period (days)">Masa Tunggu (hari)</td>
                            @foreach ($selected as $index => $product)
                                <td class="num {{ ($highlights['shortest_wait'] ?? null) === $index ? 'best' : '' }}">
                                    {{ $product->waiting_period !== null ? $product->waiting_period : '—' }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td data-id="Rasio Nilai" data-en="Value Ratio">Rasio Nilai</td>
                            @foreach ($selected as $index => $product)
                                <td class="num {{ ($highlights['best_value'] ?? null) === $index ? 'best' : '' }}">
                                    {{ number_format($product->valueRatio(), 1) }}×
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Kesesuaian dengan profil ─────────────────── --}}
        <div class="grid grid-3 reveal reveal-3" style="margin-top:16px">
            @foreach ($selected as $index => $product)
                @php
                    $fit = $suitability[$product->id] ?? ['score' => 50, 'label' => ['id' => 'Netral', 'en' => 'Neutral'], 'reasons' => []];
                    $fitTone = $fit['score'] >= 70 ? 'pill-green' : ($fit['score'] >= 45 ? 'pill-amber' : 'pill-rose');
                @endphp
                <div class="card">
                    <div class="section-heading" style="align-items:start">
                        <div>
                            <h2 style="font-size:13.5px">{{ $product->name }}</h2>
                            <p>{{ $product->provider }}</p>
                        </div>
                        <span class="pill {{ $fitTone }}">{{ $fit['score'] }}/100</span>
                    </div>

                    @foreach ($highlightByIndex[$index] ?? [] as $hl)
                        <p style="margin:0 0 8px"><span class="pill pill-blue" data-id="{{ $hl['id'] }}" data-en="{{ $hl['en'] }}">{{ $hl['id'] }}</span></p>
                    @endforeach

                    <p style="font-size:12px;font-weight:650;margin:6px 0 6px" data-id="{{ $fit['label']['id'] }}" data-en="{{ $fit['label']['en'] }}">
                        {{ $fit['label']['id'] }}
                    </p>

                    <ul style="margin:0;padding-left:16px;font-size:11.5px;color:#4f5c72;display:grid;gap:6px">
                        @forelse (array_slice($fit['reasons'], 0, 3) as $reason)
                            <li data-id="{{ $reason['id'] }}" data-en="{{ $reason['en'] }}">{{ $reason['id'] }}</li>
                        @empty
                            <li data-id="Belum ada catatan kesesuaian." data-en="No suitability notes yet.">Belum ada catatan kesesuaian.</li>
                        @endforelse
                    </ul>

                    @if ($product->is_sample)
                        <p style="margin:10px 0 0;font-size:10.5px;color:var(--muted)"
                           data-id="Data contoh untuk keperluan demo." data-en="Sample data for demo purposes.">Data contoh untuk keperluan demo.</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Daftar produk tersedia ────────────────────────────── --}}
    <div class="card reveal reveal-3" style="margin-top:16px">
        <div class="section-heading">
            <div>
                <h2 data-id="Produk Tersedia" data-en="Available Products">Produk Tersedia</h2>
                <p data-id="Pilih produk untuk menyusun perbandingan."
                   data-en="Pick products to build your comparison.">Pilih produk untuk menyusun perbandingan.</p>
            </div>
            <span class="subtle-link">{{ $products->count() }} <span data-id="produk" data-en="products">produk</span></span>
        </div>

        @if ($products->isEmpty())
            <div class="empty">
                <span data-id="Tidak ada produk yang cocok dengan filter."
                      data-en="No products match the filter.">Tidak ada produk yang cocok dengan filter.</span>
            </div>
        @else
            <div class="grid grid-3">
                @foreach ($products as $product)
                    <a href="{{ route('comparison.index', ['category' => $category, 'q' => $search, 'ids' => array_merge($selected->pluck('id')->all(), [$product->id])]) }}"
                       class="stat" style="display:block">
                        <div class="stat-label">{{ $categoryNames[$product->category]['id'] ?? $product->category }}</div>
                        <div style="font-weight:700;font-size:13.5px;margin-top:5px">{{ $product->name }}</div>
                        <div class="stat-note">{{ $product->provider }}</div>
                        <div style="margin-top:9px;font-size:11.5px;color:#4f5c72">
                            {{ \App\Support\Money::compact($product->monthly_premium) }}<span data-id="/bln" data-en="/mo">/bln</span>
                            · <span data-id="UP" data-en="cover">UP</span> {{ \App\Support\Money::compact($product->coverage_limit) }}
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
