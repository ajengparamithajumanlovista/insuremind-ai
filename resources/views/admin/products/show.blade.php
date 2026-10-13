@extends('layouts.admin')

@section('title', 'Detail Produk')

@section('content')
    @php
        $description = $product->description ?? [];
        $benefitsText = implode("\n", $product->benefits ?? []);
        $exclusionsText = implode("\n", $product->exclusions ?? []);
        $badge = $product->sourceBadge();
    @endphp

    <div class="section-heading reveal">
        <div>
            <h1 class="page-title" style="margin-bottom:5px">{{ $product->name }}</h1>
            <p class="page-subtitle" style="margin-bottom:0">
                {{ $product->provider }} ·
                <span class="pill pill-{{ $badge['tone'] }}">{{ $badge['label']['id'] }}</span>
                <span class="pill pill-{{ $product->is_active ? 'green' : 'grey' }}">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost" data-id="Kembali" data-en="Back">Kembali</a>
    </div>

    <div class="grid grid-2 reveal reveal-1" style="margin-bottom:16px">
        <div class="card">
            <div class="section-heading">
                <div>
                    <h2 data-id="Perbarui Produk" data-en="Update Product">Perbarui Produk</h2>
                    <p data-id="Perubahan dicatat ke riwayat produk dan Audit Log" data-en="Changes are recorded in product history and the Audit Log">Perubahan dicatat ke riwayat produk dan Audit Log</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.products.update', $product) }}" class="grid">
                @csrf
                @method('PUT')

                <div class="grid grid-2">
                    <div class="field">
                        <label for="provider" data-id="Penyedia" data-en="Provider">Penyedia</label>
                        <input type="text" id="provider" name="provider" value="{{ old('provider', $product->provider) }}" required>
                    </div>
                    <div class="field">
                        <label for="name" data-id="Nama Produk" data-en="Product Name">Nama Produk</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                    </div>
                </div>

                <div class="grid grid-3">
                    <div class="field">
                        <label for="category" data-id="Kategori" data-en="Category">Kategori</label>
                        <select id="category" name="category">
                            @foreach ($product::CATEGORIES as $category)
                                <option value="{{ $category }}" @selected(old('category', $product->category) === $category)>
                                    {{ ['health' => 'Kesehatan', 'life' => 'Jiwa', 'work' => 'Kerja', 'education' => 'Pendidikan'][$category] ?? ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="monthly_premium" data-id="Premi Bulanan (Rp)" data-en="Monthly Premium (Rp)">Premi Bulanan (Rp)</label>
                        <input type="number" step="1000" min="0" id="monthly_premium" name="monthly_premium"
                               value="{{ old('monthly_premium', (int) $product->monthly_premium) }}" required>
                    </div>
                    <div class="field">
                        <label for="annual_premium" data-id="Premi Tahunan (Rp)" data-en="Annual Premium (Rp)">Premi Tahunan (Rp)</label>
                        <input type="number" step="1000" min="0" id="annual_premium" name="annual_premium"
                               value="{{ old('annual_premium', $product->annual_premium ? (int) $product->annual_premium : '') }}">
                    </div>
                </div>

                <div class="field">
                    <label for="coverage_limit" data-id="Batas Perlindungan (Rp)" data-en="Coverage Limit (Rp)">Batas Perlindungan (Rp)</label>
                    <input type="number" step="1000000" min="0" id="coverage_limit" name="coverage_limit"
                           value="{{ old('coverage_limit', (int) $product->coverage_limit) }}" required>
                </div>

                <div class="grid grid-2">
                    <div class="field">
                        <label for="description_id" data-id="Deskripsi (ID)" data-en="Description (ID)">Deskripsi (ID)</label>
                        <textarea id="description_id" name="description_id" rows="3">{{ old('description_id', $description['id'] ?? '') }}</textarea>
                    </div>
                    <div class="field">
                        <label for="description_en" data-id="Deskripsi (EN)" data-en="Description (EN)">Deskripsi (EN)</label>
                        <textarea id="description_en" name="description_en" rows="3">{{ old('description_en', $description['en'] ?? '') }}</textarea>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="field">
                        <label for="benefits" data-id="Manfaat (satu per baris)" data-en="Benefits (one per line)">Manfaat (satu per baris)</label>
                        <textarea id="benefits" name="benefits" rows="4">{{ old('benefits', $benefitsText) }}</textarea>
                    </div>
                    <div class="field">
                        <label for="exclusions" data-id="Pengecualian (satu per baris)" data-en="Exclusions (one per line)">Pengecualian (satu per baris)</label>
                        <textarea id="exclusions" name="exclusions" rows="4">{{ old('exclusions', $exclusionsText) }}</textarea>
                    </div>
                </div>

                <div class="field">
                    <label for="source_url" data-id="Tautan Sumber Resmi" data-en="Official Source URL">Tautan Sumber Resmi</label>
                    <input type="url" id="source_url" name="source_url" value="{{ old('source_url', $product->source_url) }}" placeholder="https://">
                </div>

                <div class="field">
                    <label for="reason" data-id="Alasan Perubahan" data-en="Reason for Change">Alasan Perubahan</label>
                    <input type="text" id="reason" name="reason" value="{{ old('reason') }}"
                           data-id-placeholder="Mis. penyesuaian premi kuartal ini" data-en-placeholder="e.g. premium adjustment for this quarter">
                    <span class="hint" data-id="Alasan membantu penelusuran audit." data-en="A reason helps audit traceability.">Alasan membantu penelusuran audit.</span>
                </div>

                <div style="display:flex;gap:18px;flex-wrap:wrap">
                    <label class="check">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))>
                        <span data-id="Tampilkan pada katalog" data-en="Show in catalog">Tampilkan pada katalog</span>
                    </label>
                    <label class="check">
                        <input type="checkbox" name="is_sample" value="1" @checked(old('is_sample', $product->is_sample))>
                        <span data-id="Tandai sebagai data contoh" data-en="Mark as sample data">Tandai sebagai data contoh</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" data-id="Simpan Perubahan" data-en="Save Changes">Simpan Perubahan</button>
            </form>
        </div>

        <div class="grid">
            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Ringkasan" data-en="Summary">Ringkasan</h2>
                    </div>
                </div>

                <div class="kv">
                    <div class="kv-row">
                        <dt data-id="Rasio Nilai" data-en="Value Ratio">Rasio Nilai</dt>
                        <dd>{{ $product->valueRatio() }}x</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Usia Masuk" data-en="Entry Age">Usia Masuk</dt>
                        <dd>{{ $product->entry_age_min ?? '—' }} – {{ $product->entry_age_max ?? '—' }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Sumber Data" data-en="Data Source">Sumber Data</dt>
                        <dd>{{ $product->data_source ?? '—' }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Tautan Sumber" data-en="Source URL">Tautan Sumber</dt>
                        <dd>
                            @if ($product->source_url)
                                <a href="{{ $product->source_url }}" target="_blank" rel="noopener">{{ $product->source_url }}</a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Riwayat Perubahan" data-en="Change History">Riwayat Perubahan</h2>
                        <p data-id="Perubahan terbaru pada produk ini" data-en="Recent changes to this product">Perubahan terbaru pada produk ini</p>
                    </div>
                </div>

                <div class="timeline">
                    @forelse ($histories as $history)
                        <div class="timeline-item">
                            <span class="timeline-dot blue"></span>
                            <div class="timeline-body">
                                <b>{{ str_replace('_', ' ', $history->field) }}</b>
                                <span>{{ $history->created_at?->format('d M Y H:i') }} · {{ $history->user?->name ?? 'Sistem' }}</span>
                                <p>
                                    <span class="diff-cell old" style="display:inline-block"><small>Sebelum</small>{{ $history->old_value ?? '—' }}</span>
                                    <span class="diff-cell new" style="display:inline-block;margin-left:6px"><small>Sesudah</small>{{ $history->new_value ?? '—' }}</span>
                                </p>
                                @if ($history->reason)
                                    <span>{{ $history->reason }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty">
                            <span data-id="Belum ada perubahan tercatat." data-en="No changes recorded yet.">Belum ada perubahan tercatat.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
