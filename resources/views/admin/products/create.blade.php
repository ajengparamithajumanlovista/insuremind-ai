@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
    <div class="section-heading reveal">
        <div>
            <h1 class="page-title" style="margin-bottom:5px"
                data-id="Tambah Produk" data-en="Add Product">Tambah Produk</h1>
            <p class="page-subtitle" style="margin-bottom:0"
               data-id="Produk baru langsung masuk katalog dan tercatat di Audit Log."
               data-en="The new product enters the catalog and is recorded in the Audit Log.">Produk baru langsung masuk katalog dan tercatat di Audit Log.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost" data-id="Kembali" data-en="Back">Kembali</a>
    </div>

    <div class="grid grid-2 reveal reveal-1" style="margin-bottom:16px">
        <div class="card">
            <div class="section-heading">
                <div>
                    <h2 data-id="Data Produk" data-en="Product Details">Data Produk</h2>
                    <p data-id="Isi informasi utama produk asuransi" data-en="Fill in the main insurance product information">Isi informasi utama produk asuransi</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.products.store') }}" class="grid">
                @csrf

                <div class="grid grid-2">
                    <div class="field">
                        <label for="provider" data-id="Penyedia" data-en="Provider">Penyedia</label>
                        <input type="text" id="provider" name="provider" value="{{ old('provider') }}" required>
                    </div>
                    <div class="field">
                        <label for="name" data-id="Nama Produk" data-en="Product Name">Nama Produk</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="grid grid-3">
                    <div class="field">
                        <label for="category" data-id="Kategori" data-en="Category">Kategori</label>
                        <select id="category" name="category">
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(old('category') === $category)>
                                    {{ ['health' => 'Kesehatan', 'life' => 'Jiwa', 'work' => 'Kerja', 'education' => 'Pendidikan'][$category] ?? ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="monthly_premium" data-id="Premi Bulanan (Rp)" data-en="Monthly Premium (Rp)">Premi Bulanan (Rp)</label>
                        <input type="number" step="1000" min="0" id="monthly_premium" name="monthly_premium"
                               value="{{ old('monthly_premium') }}" required>
                    </div>
                    <div class="field">
                        <label for="annual_premium" data-id="Premi Tahunan (Rp)" data-en="Annual Premium (Rp)">Premi Tahunan (Rp)</label>
                        <input type="number" step="1000" min="0" id="annual_premium" name="annual_premium"
                               value="{{ old('annual_premium') }}">
                    </div>
                </div>

                <div class="field">
                    <label for="coverage_limit" data-id="Batas Perlindungan (Rp)" data-en="Coverage Limit (Rp)">Batas Perlindungan (Rp)</label>
                    <input type="number" step="1000000" min="0" id="coverage_limit" name="coverage_limit"
                           value="{{ old('coverage_limit') }}" required>
                </div>

                <div class="grid grid-2">
                    <div class="field">
                        <label for="description_id" data-id="Deskripsi (ID)" data-en="Description (ID)">Deskripsi (ID)</label>
                        <textarea id="description_id" name="description_id" rows="3">{{ old('description_id') }}</textarea>
                    </div>
                    <div class="field">
                        <label for="description_en" data-id="Deskripsi (EN)" data-en="Description (EN)">Deskripsi (EN)</label>
                        <textarea id="description_en" name="description_en" rows="3">{{ old('description_en') }}</textarea>
                    </div>
                </div>

                <div class="field">
                    <label for="source_url" data-id="Tautan Sumber Resmi" data-en="Official Source URL">Tautan Sumber Resmi</label>
                    <input type="url" id="source_url" name="source_url" value="{{ old('source_url') }}" placeholder="https://">
                </div>

                <div class="field">
                    <label for="reason" data-id="Catatan Penambahan" data-en="Addition Note">Catatan Penambahan</label>
                    <input type="text" id="reason" name="reason" value="{{ old('reason') }}"
                           data-id-placeholder="Mis. produk baru dari mitra resmi" data-en-placeholder="e.g. new product from an official partner">
                    <span class="hint" data-id="Catatan membantu penelusuran audit." data-en="A note helps audit traceability.">Catatan membantu penelusuran audit.</span>
                </div>

                <div style="display:flex;gap:18px;flex-wrap:wrap">
                    <label class="check">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                        <span data-id="Tampilkan pada katalog" data-en="Show in catalog">Tampilkan pada katalog</span>
                    </label>
                    <label class="check">
                        <input type="checkbox" name="is_sample" value="1" @checked(old('is_sample'))>
                        <span data-id="Tandai sebagai data contoh" data-en="Mark as sample data">Tandai sebagai data contoh</span>
                    </label>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap">
                    <button type="submit" class="btn btn-primary" data-id="Simpan Produk" data-en="Save Product">Simpan Produk</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-ghost" data-id="Batal" data-en="Cancel">Batal</a>
                </div>
            </form>
        </div>

        <div class="grid">
            <div class="card card-accent">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Panduan" data-en="Guidelines">Panduan</h2>
                    </div>
                </div>

                <div class="kv">
                    <div class="kv-row">
                        <dt data-id="Slug" data-en="Slug">Slug</dt>
                        <dd data-id="Dibuat otomatis dari penyedia + nama" data-en="Generated from provider + name">Dibuat otomatis dari penyedia + nama</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Sumber Data" data-en="Data Source">Sumber Data</dt>
                        <dd data-id="Centang data contoh bila belum diverifikasi" data-en="Check sample data if unverified">Centang data contoh bila belum diverifikasi</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Riwayat" data-en="History">Riwayat</dt>
                        <dd data-id="Penambahan tercatat pada Audit Log" data-en="Additions are recorded in the Audit Log">Penambahan tercatat pada Audit Log</dd>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
