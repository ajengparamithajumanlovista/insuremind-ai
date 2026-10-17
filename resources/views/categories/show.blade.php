@extends('layouts.app')

@section('title', $meta['title']['id'])
@push('head')
    <meta data-id-page-title data-title-id="{{ $meta['title']['id'] }}" data-title-en="{{ $meta['title']['en'] }}">
@endpush

@section('content')
    @php
        $categoryNames = [
            'health' => ['id' => 'Kesehatan', 'en' => 'Health'],
            'life' => ['id' => 'Jiwa', 'en' => 'Life'],
            'work' => ['id' => 'Risiko Kerja', 'en' => 'Work Risk'],
            'education' => ['id' => 'Pendidikan', 'en' => 'Education'],
        ];
        $icons = [
            'health' => '❤',
            'life' => '🛡',
            'work' => '🧰',
            'education' => '🎓',
        ];
    @endphp

    <h1 class="page-title reveal" data-id="{{ $meta['title']['id'] }}" data-en="{{ $meta['title']['en'] }}">{{ $meta['title']['id'] }}</h1>
    <p class="page-subtitle reveal reveal-1"
       data-id="{{ $meta['description']['id'] }}" data-en="{{ $meta['description']['en'] }}">{{ $meta['description']['id'] }}</p>

    {{-- ── Other categories ──────────────────────────────────── --}}
    <div class="reveal reveal-1" style="display:flex;gap:8px;flex-wrap:wrap;margin:16px 0">
        @foreach ($categoryNames as $key => $label)
            <a href="{{ route('categories.show', $key) }}"
               class="btn {{ $category === $key ? 'btn-primary' : 'btn-ghost' }} btn-sm">
                {{ $icons[$key] ?? '◫' }} {{ $label['id'] }}
            </a>
        @endforeach
        <a href="{{ route('comparison.index', ['category' => $category]) }}" class="btn btn-ghost btn-sm"
           data-id="Bandingkan kategori ini →" data-en="Compare this category →">Bandingkan kategori ini →</a>
    </div>

    @if ($products->isEmpty())
        <div class="empty reveal reveal-2">
            <strong data-id="Belum ada produk kategori ini" data-en="No products in this category yet">Belum ada produk kategori ini</strong>
            <span data-id="Produk akan muncul setelah data contoh dimuat."
                  data-en="Products will appear once sample data is seeded.">Produk akan muncul setelah data contoh dimuat.</span>
        </div>
    @else
        <div class="grid grid-2 reveal reveal-2">
            @foreach ($products as $product)
                @php
                    $fit = $suitability[$product->id] ?? ['score' => 50, 'label' => ['id' => 'Netral', 'en' => 'Neutral'], 'reasons' => []];
                    $fitTone = $fit['score'] >= 70 ? 'pill-green' : ($fit['score'] >= 45 ? 'pill-amber' : 'pill-rose');
                @endphp
                <div class="card">
                    <div class="section-heading" style="align-items:start">
                        <div>
                            <h2 style="font-size:14px">{{ $product->name }}</h2>
                            <p>{{ $product->provider }}</p>
                        </div>
                        <span class="pill {{ $fitTone }}">{{ $fit['score'] }}/100</span>
                    </div>

                    <div class="grid grid-2" style="margin-bottom:12px">
                        <div>
                            <div class="stat-label" data-id="Premi Bulanan" data-en="Monthly Premium">Premi Bulanan</div>
                            <div style="font-weight:700;font-size:14px;margin-top:3px">{{ \App\Support\Money::idr($product->monthly_premium) }}</div>
                        </div>
                        <div>
                            <div class="stat-label" data-id="Uang Pertanggungan" data-en="Coverage Limit">Uang Pertanggungan</div>
                            <div style="font-weight:700;font-size:14px;margin-top:3px">{{ \App\Support\Money::idr($product->coverage_limit) }}</div>
                        </div>
                        <div>
                            <div class="stat-label" data-id="Masa Tunggu" data-en="Waiting Period">Masa Tunggu</div>
                            <div style="font-weight:700;font-size:14px;margin-top:3px">
                                {{ $product->waiting_period !== null ? $product->waiting_period . ' hari' : '—' }}
                            </div>
                        </div>
                        <div>
                            <div class="stat-label" data-id="Rasio Nilai" data-en="Value Ratio">Rasio Nilai</div>
                            <div style="font-weight:700;font-size:14px;margin-top:3px">{{ number_format($product->valueRatio(), 1) }}×</div>
                        </div>
                    </div>

                    <p style="font-size:12px;font-weight:650;margin:0 0 6px" data-id="{{ $fit['label']['id'] }}" data-en="{{ $fit['label']['en'] }}">
                        {{ $fit['label']['id'] }}
                    </p>

                    <ul style="margin:0 0 12px;padding-left:16px;font-size:11.5px;color:#4f5c72;display:grid;gap:6px">
                        @forelse (array_slice($fit['reasons'], 0, 2) as $reason)
                            <li data-id="{{ $reason['id'] }}" data-en="{{ $reason['en'] }}">{{ $reason['id'] }}</li>
                        @empty
                            <li data-id="Belum ada catatan kesesuaian." data-en="No suitability notes yet.">Belum ada catatan kesesuaian.</li>
                        @endforelse
                    </ul>

                    @if ($product->is_sample)
                        <p style="margin:0 0 10px;font-size:10.5px;color:var(--muted)"
                           data-id="Data contoh untuk keperluan demo." data-en="Sample data for demo purposes.">Data contoh untuk keperluan demo.</p>
                    @endif

                    <a href="{{ route('comparison.index', ['category' => $category, 'ids' => [$product->id]]) }}"
                       class="btn btn-ghost btn-sm" data-id="Bandingkan produk ini" data-en="Compare this product">Bandingkan produk ini</a>
                </div>
            @endforeach
        </div>
    @endif
@endsection
