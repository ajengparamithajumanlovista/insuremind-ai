@extends('layouts.admin')

@section('title', 'Audit Log')

@section('content')
    <div class="section-heading reveal">
        <div>
            <h1 class="page-title" style="margin-bottom:5px" data-id="Audit Log" data-en="Audit Log">Audit Log</h1>
            <p class="page-subtitle" style="margin-bottom:0"
               data-id="Catatan aktivitas penting. Bersifat append-only: tidak dapat diubah atau dihapus dari antarmuka."
               data-en="A record of important activity. Append-only: it cannot be altered or deleted from the interface.">Catatan aktivitas penting. Bersifat append-only: tidak dapat diubah atau dihapus dari antarmuka.</p>
        </div>
        <a href="{{ route('admin.audit.export', request()->query()) }}" class="btn btn-ghost"
           data-id="Ekspor CSV" data-en="Export CSV">Ekspor CSV</a>
    </div>

    <div class="grid grid-4 reveal reveal-1" style="margin-bottom:16px">
        <div class="stat">
            <div class="stat-label" data-id="Total Entri" data-en="Total Entries">Total Entri</div>
            <div class="stat-value">{{ number_format($summary['total'], 0, ',', '.') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Hari Ini" data-en="Today">Hari Ini</div>
            <div class="stat-value">{{ number_format($summary['today'], 0, ',', '.') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Gagal" data-en="Failed">Gagal</div>
            <div class="stat-value">{{ number_format($summary['failed'], 0, ',', '.') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Pelaku Unik" data-en="Unique Actors">Pelaku Unik</div>
            <div class="stat-value">{{ number_format($summary['actors'], 0, ',', '.') }}</div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.audit.index') }}" class="filters reveal reveal-2">
        <div class="field" style="flex:1">
            <label for="q" data-id="Pencarian" data-en="Search">Pencarian</label>
            <input type="search" id="q" name="q" value="{{ $filters['q'] }}"
                   data-id-placeholder="Deskripsi, aksi, pelaku" data-en-placeholder="Description, action, actor">
        </div>

        <div class="field">
            <label for="category" data-id="Kategori" data-en="Category">Kategori</label>
            <select id="category" name="category">
                <option value="" data-id="Semua kategori" data-en="All categories">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ str_replace('_', ' ', $category) }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="result" data-id="Hasil" data-en="Result">Hasil</label>
            <select id="result" name="result">
                <option value="" data-id="Semua" data-en="All">Semua</option>
                <option value="success" @selected($filters['result'] === 'success') data-id="Berhasil" data-en="Success">Berhasil</option>
                <option value="failed" @selected($filters['result'] === 'failed') data-id="Gagal" data-en="Failed">Gagal</option>
            </select>
        </div>

        <div class="field">
            <label for="from" data-id="Dari" data-en="From">Dari</label>
            <input type="date" id="from" name="from" value="{{ $filters['from'] }}">
        </div>

        <div class="field">
            <label for="to" data-id="Sampai" data-en="To">Sampai</label>
            <input type="date" id="to" name="to" value="{{ $filters['to'] }}">
        </div>

        <button type="submit" class="btn btn-primary" data-id="Terapkan" data-en="Apply">Terapkan</button>
        <a href="{{ route('admin.audit.index') }}" class="btn btn-ghost" data-id="Reset" data-en="Reset">Reset</a>
    </form>

    <div class="table-wrap reveal reveal-2">
        <table class="data">
            <thead>
                <tr>
                    <th data-id="Waktu" data-en="Time">Waktu</th>
                    <th data-id="Pelaku" data-en="Actor">Pelaku</th>
                    <th data-id="Kategori" data-en="Category">Kategori</th>
                    <th data-id="Aktivitas" data-en="Activity">Aktivitas</th>
                    <th data-id="Hasil" data-en="Result">Hasil</th>
                    <th data-id="Aksi" data-en="Actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="muted">{{ $log->created_at?->format('d M Y H:i:s') }}</td>
                        <td>
                            <b>{{ $log->actor_name ?? 'Sistem' }}</b>
                            <div class="muted">{{ $log->actor_role }}</div>
                        </td>
                        <td><span class="pill pill-grey">{{ $log->categoryLabel()['id'] }}</span></td>
                        <td>
                            {{ $log->description }}
                            <div class="muted">{{ $log->action }}</div>
                        </td>
                        <td><span class="pill pill-{{ $log->resultTone() }}">{{ ucfirst($log->result) }}</span></td>
                        <td>
                            <a href="{{ route('admin.audit.show', $log) }}" class="btn btn-ghost btn-sm"
                               data-id="Detail" data-en="Detail">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty">
                                <strong data-id="Belum ada catatan audit" data-en="No audit entries yet">Belum ada catatan audit</strong>
                                <span data-id="Aktivitas penting akan muncul di sini." data-en="Important activity will appear here.">Aktivitas penting akan muncul di sini.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pager reveal reveal-3">{{ $logs->links() }}</div>
@endsection
