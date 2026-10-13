@extends('layouts.admin')

@section('title', 'Katalog Produk')

@section('content')
    <h1 class="page-title reveal" data-id="Katalog Produk" data-en="Product Catalog">Katalog Produk</h1>
    <p class="page-subtitle reveal reveal-1"
       data-id="Kelola produk asuransi. Produk nonaktif tetap menyimpan riwayatnya."
       data-en="Manage insurance products. Inactive products keep their history.">Kelola produk asuransi. Produk nonaktif tetap menyimpan riwayatnya.</p>

    <div class="grid grid-4 reveal reveal-1" style="margin-bottom:16px">
        <div class="stat">
            <div class="stat-label" data-id="Total" data-en="Total">Total</div>
            <div class="stat-value">{{ number_format($summary['total'], 0, ',', '.') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Aktif" data-en="Active">Aktif</div>
            <div class="stat-value">{{ number_format($summary['active'], 0, ',', '.') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Nonaktif" data-en="Inactive">Nonaktif</div>
            <div class="stat-value">{{ number_format($summary['inactive'], 0, ',', '.') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Data Contoh" data-en="Sample Data">Data Contoh</div>
            <div class="stat-value">{{ number_format($summary['sample'], 0, ',', '.') }}</div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.products.index') }}" class="filters reveal reveal-2">
        <div class="field" style="flex:1">
            <label for="q" data-id="Pencarian" data-en="Search">Pencarian</label>
            <input type="search" id="q" name="q" value="{{ $filters['q'] }}"
                   data-id-placeholder="Nama produk atau penyedia" data-en-placeholder="Product name or provider">
        </div>

        <div class="field">
            <label for="category" data-id="Kategori" data-en="Category">Kategori</label>
            <select id="category" name="category">
                <option value="" data-id="Semua kategori" data-en="All categories">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected($filters['category'] === $category)>
                        {{ ['health' => 'Kesehatan', 'life' => 'Jiwa', 'work' => 'Kecelakaan Kerja', 'education' => 'Pendidikan'][$category] ?? ucfirst($category) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="visibility" data-id="Status" data-en="Status">Status</label>
            <select id="visibility" name="visibility">
                <option value="" data-id="Semua" data-en="All">Semua</option>
                <option value="active" @selected($filters['visibility'] === 'active') data-id="Aktif" data-en="Active">Aktif</option>
                <option value="inactive" @selected($filters['visibility'] === 'inactive') data-id="Nonaktif" data-en="Inactive">Nonaktif</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" data-id="Terapkan" data-en="Apply">Terapkan</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost" data-id="Reset" data-en="Reset">Reset</a>
    </form>

    <div class="table-wrap reveal reveal-2">
        <table class="data">
            <thead>
                <tr>
                    <th data-id="Produk" data-en="Product">Produk</th>
                    <th data-id="Kategori" data-en="Category">Kategori</th>
                    <th class="num" data-id="Premi / Bulan" data-en="Premium / Month">Premi / Bulan</th>
                    <th class="num" data-id="Perlindungan" data-en="Coverage">Perlindungan</th>
                    <th data-id="Sumber" data-en="Source">Sumber</th>
                    <th data-id="Status" data-en="Status">Status</th>
                    <th data-id="Aksi" data-en="Actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    @php $badge = $product->sourceBadge(); @endphp
                    <tr>
                        <td>
                            <a href="{{ route('admin.products.show', $product) }}"><b>{{ $product->name }}</b></a>
                            <div class="muted">{{ $product->provider }}</div>
                        </td>
                        <td>
                            <span class="pill pill-grey">
                                {{ ['health' => 'Kesehatan', 'life' => 'Jiwa', 'work' => 'Kerja', 'education' => 'Pendidikan'][$product->category] ?? $product->category }}
                            </span>
                        </td>
                        <td class="num">{{ \App\Support\Money::idr($product->monthly_premium) }}</td>
                        <td class="num">{{ \App\Support\Money::compact($product->coverage_limit) }}</td>
                        <td><span class="pill pill-{{ $badge['tone'] }}">{{ $badge['label']['id'] }}</span></td>
                        <td>
                            <span class="pill pill-{{ $product->is_active ? 'green' : 'grey' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-ghost btn-sm"
                                   data-id="Kelola" data-en="Manage">Kelola</a>

                                <form method="POST" action="{{ route('admin.products.toggle', $product) }}">
                                    @csrf
                                    <button type="submit" class="btn {{ $product->is_active ? 'btn-danger' : 'btn-ghost' }} btn-sm"
                                            data-id="{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            data-en="{{ $product->is_active ? 'Deactivate' : 'Activate' }}">
                                        {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty">
                                <strong data-id="Tidak ada produk yang cocok" data-en="No matching products">Tidak ada produk yang cocok</strong>
                                <span data-id="Ubah kata kunci atau filter pencarian." data-en="Adjust your search keyword or filters.">Ubah kata kunci atau filter pencarian.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pager reveal reveal-3">{{ $products->links() }}</div>
@endsection
