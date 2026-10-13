@extends('layouts.admin')

@section('title', 'Dokumen Polis')

@section('content')
    <h1 class="page-title reveal" data-id="Dokumen Polis" data-en="Policy Documents">Dokumen Polis</h1>
    <p class="page-subtitle reveal reveal-1"
       data-id="Pantau jejak pemrosesan dokumen. Status gagal tidak pernah ditampilkan sebagai sukses."
       data-en="Track document processing history. A failed status is never shown as successful.">Pantau jejak pemrosesan dokumen. Status gagal tidak pernah ditampilkan sebagai sukses.</p>

    <div class="grid grid-4 reveal reveal-1" style="margin-bottom:16px">
        <div class="stat">
            <div class="stat-label" data-id="Total" data-en="Total">Total</div>
            <div class="stat-value">{{ number_format($summary['total'], 0, ',', '.') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Diproses" data-en="Processing">Diproses</div>
            <div class="stat-value">{{ number_format($summary['processing'], 0, ',', '.') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Perlu Tinjauan" data-en="Needs Review">Perlu Tinjauan</div>
            <div class="stat-value">{{ number_format($summary['review'], 0, ',', '.') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Gagal" data-en="Failed">Gagal</div>
            <div class="stat-value">{{ number_format($summary['failed'], 0, ',', '.') }}</div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.documents.index') }}" class="filters reveal reveal-2">
        <div class="field" style="flex:1">
            <label for="q" data-id="Pencarian" data-en="Search">Pencarian</label>
            <input type="search" id="q" name="q" value="{{ $filters['q'] }}"
                   data-id-placeholder="Nama berkas atau pengunggah" data-en-placeholder="File name or uploader">
        </div>

        <div class="field">
            <label for="status" data-id="Status" data-en="Status">Status</label>
            <select id="status" name="status">
                <option value="" data-id="Semua status" data-en="All statuses">Semua status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary" data-id="Terapkan" data-en="Apply">Terapkan</button>
        <a href="{{ route('admin.documents.index') }}" class="btn btn-ghost" data-id="Reset" data-en="Reset">Reset</a>
    </form>

    <div class="table-wrap reveal reveal-2">
        <table class="data">
            <thead>
                <tr>
                    <th data-id="Berkas" data-en="File">Berkas</th>
                    <th data-id="Pengguna" data-en="User">Pengguna</th>
                    <th data-id="Status" data-en="Status">Status</th>
                    <th data-id="Ekstraksi / Analisis" data-en="Extraction / Analysis">Ekstraksi / Analisis</th>
                    <th data-id="Diunggah" data-en="Uploaded">Diunggah</th>
                    <th data-id="Aksi" data-en="Actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($documents as $document)
                    <tr>
                        <td>
                            <a href="{{ route('admin.documents.show', $document) }}"><b>{{ $document->original_name }}</b></a>
                            <div class="muted">{{ number_format($document->size_bytes / 1024, 0) }} KB · {{ $document->mime_type }}</div>
                        </td>
                        <td>
                            <b>{{ $document->user?->name }}</b>
                            <div class="muted">{{ $document->user?->email }}</div>
                        </td>
                        <td><span class="pill pill-{{ $document->statusTone() }}">{{ ucfirst($document->status) }}</span></td>
                        <td class="muted">{{ ucfirst($document->extraction_status) }} / {{ ucfirst($document->analysis_status) }}</td>
                        <td class="muted">{{ $document->created_at?->format('d M Y H:i') }}</td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('admin.documents.show', $document) }}" class="btn btn-ghost btn-sm"
                                   data-id="Detail" data-en="Detail">Detail</a>
                                <a href="{{ route('admin.documents.download', $document) }}" class="btn btn-ghost btn-sm"
                                   data-id="Unduh" data-en="Download">Unduh</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty">
                                <strong data-id="Belum ada dokumen" data-en="No documents yet">Belum ada dokumen</strong>
                                <span data-id="Dokumen yang diunggah pengguna akan muncul di sini."
                                      data-en="Documents uploaded by users will appear here.">Dokumen yang diunggah pengguna akan muncul di sini.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pager reveal reveal-3">{{ $documents->links() }}</div>
@endsection
