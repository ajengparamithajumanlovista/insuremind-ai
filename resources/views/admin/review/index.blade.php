@extends('layouts.admin')

@section('title', 'AI Quality Review')

@section('content')
    <h1 class="page-title reveal" data-id="AI Quality Review" data-en="AI Quality Review">AI Quality Review</h1>
    <p class="page-subtitle reveal reveal-1"
       data-id="Periksa jawaban yang perlu ditinjau manusia. Setiap keputusan tercatat lengkap dengan alasannya."
       data-en="Inspect answers that need human review. Every decision is recorded with its reason.">Periksa jawaban yang perlu ditinjau manusia. Setiap keputusan tercatat lengkap dengan alasannya.</p>

    <div class="grid grid-4 reveal reveal-1" style="margin-bottom:16px">
        <div class="stat">
            <div class="stat-label" data-id="Terbuka" data-en="Open">Terbuka</div>
            <div class="stat-value">{{ number_format($summary['open'], 0, ',', '.') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Menunggu" data-en="Pending">Menunggu</div>
            <div class="stat-value">{{ number_format($summary['pending'], 0, ',', '.') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Perlu Perbaikan" data-en="Needs Fix">Perlu Perbaikan</div>
            <div class="stat-value">{{ number_format($summary['needs_fix'], 0, ',', '.') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" data-id="Dieskalasi" data-en="Escalated">Dieskalasi</div>
            <div class="stat-value">{{ number_format($summary['escalated'], 0, ',', '.') }}</div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.review.index') }}" class="filters reveal reveal-2">
        <div class="field">
            <label for="status" data-id="Status" data-en="Status">Status</label>
            <select id="status" name="status">
                <option value="open" @selected($filters['status'] === 'open') data-id="Terbuka (semua)" data-en="Open (all)">Terbuka (semua)</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="issue" data-id="Alasan" data-en="Reason">Alasan</label>
            <select id="issue" name="issue">
                <option value="" data-id="Semua alasan" data-en="All reasons">Semua alasan</option>
                @foreach ($issueTypes as $issue)
                    <option value="{{ $issue }}" @selected($filters['issue'] === $issue)>{{ ucfirst(str_replace('_', ' ', $issue)) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary" data-id="Terapkan" data-en="Apply">Terapkan</button>
        <a href="{{ route('admin.review.index') }}" class="btn btn-ghost" data-id="Reset" data-en="Reset">Reset</a>
    </form>

    <div class="table-wrap reveal reveal-2">
        <table class="data">
            <thead>
                <tr>
                    <th data-id="Kasus" data-en="Case">Kasus</th>
                    <th data-id="Pengguna" data-en="User">Pengguna</th>
                    <th data-id="Alasan" data-en="Reason">Alasan</th>
                    <th data-id="Referensi" data-en="Reference">Referensi</th>
                    <th data-id="Status" data-en="Status">Status</th>
                    <th data-id="Reviewer" data-en="Reviewer">Reviewer</th>
                    <th data-id="Aksi" data-en="Actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cases as $case)
                    <tr>
                        <td>
                            <a href="{{ route('admin.review.show', $case) }}"><b>#{{ $case->id }}</b></a>
                            <div class="muted">{{ $case->created_at?->format('d M Y H:i') }}</div>
                        </td>
                        <td>
                            <b>{{ $case->user?->name }}</b>
                            <div class="muted">{{ $case->category ?? '—' }}</div>
                        </td>
                        <td>
                            <span class="pill pill-grey">{{ str_replace('_', ' ', $case->issue_type) }}</span>
                            @if ($case->negative_feedback_count > 0)
                                <div class="muted">{{ $case->negative_feedback_count }} feedback negatif</div>
                            @endif
                        </td>
                        <td>
                            <span class="pill pill-{{ $case->has_reference ? 'green' : 'rose' }}">
                                {{ $case->has_reference ? 'Ada' : 'Tidak ada' }}
                            </span>
                        </td>
                        <td><span class="pill pill-{{ $case->statusTone() }}">{{ ucfirst(str_replace('_', ' ', $case->status)) }}</span></td>
                        <td class="muted">{{ $case->reviewer?->name ?? '—' }}</td>
                        <td>
                            <a href="{{ route('admin.review.show', $case) }}" class="btn btn-ghost btn-sm"
                               data-id="Tinjau" data-en="Review">Tinjau</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty">
                                <strong data-id="Tidak ada kasus pada filter ini" data-en="No cases for this filter">Tidak ada kasus pada filter ini</strong>
                                <span data-id="Antrean bersih. Kerja bagus!" data-en="The queue is clear. Good job!">Antrean bersih. Kerja bagus!</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pager reveal reveal-3">{{ $cases->links() }}</div>
@endsection
