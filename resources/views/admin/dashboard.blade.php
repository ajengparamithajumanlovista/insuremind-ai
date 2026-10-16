@extends('layouts.admin')

@section('title', 'Ringkasan')

@section('content')
    <h1 class="page-title reveal" data-id="Ringkasan Operasional" data-en="Operational Overview">Ringkasan Operasional</h1>
    <p class="page-subtitle reveal reveal-1"
       data-id="Kondisi akun, katalog, dokumen, dan antrean AI Quality Review dalam satu tampilan."
       data-en="Account health, catalog, documents, and the AI Quality Review queue in one view.">Kondisi akun, katalog, dokumen, dan antrean AI Quality Review dalam satu tampilan.</p>

    {{-- ── Main statistic cards ──────────────────────────────── --}}
    <div class="grid grid-4 dash-section reveal reveal-1">
        <div class="stat">
            <div class="stat-label" data-id="Total Pengguna" data-en="Total Users">Total Pengguna</div>
            <div class="stat-value">{{ number_format($stats['users_total'], 0, ',', '.') }}</div>
            <div class="stat-note">
                {{ $stats['users_active'] }} <span data-id="aktif" data-en="active">aktif</span>
                ·
                {{ $stats['users_suspended'] }} <span data-id="ditangguhkan" data-en="suspended">ditangguhkan</span>
            </div>
        </div>

        <div class="stat">
            <div class="stat-label" data-id="Produk Aktif" data-en="Active Products">Produk Aktif</div>
            <div class="stat-value">{{ number_format($stats['products_active'], 0, ',', '.') }}</div>
            <div class="stat-note">
                <span data-id="dari" data-en="of">dari</span>
                {{ number_format($stats['products_total'], 0, ',', '.') }}
                <span data-id="produk" data-en="products">produk</span>
            </div>
        </div>

        <div class="stat">
            <div class="stat-label" data-id="Dokumen Polis" data-en="Policy Documents">Dokumen Polis</div>
            <div class="stat-value">{{ number_format($stats['documents_total'], 0, ',', '.') }}</div>
            <div class="stat-note">
                <span class="pill pill-rose">{{ $stats['documents_failed'] }} <span data-id="gagal" data-en="failed">gagal</span></span>
                <span class="pill pill-amber">{{ $stats['documents_review'] }} <span data-id="perlu tinjauan" data-en="needs review">perlu tinjauan</span></span>
            </div>
        </div>

        <div class="stat">
            <div class="stat-label" data-id="Antrean Review" data-en="Review Queue">Antrean Review</div>
            <div class="stat-value">{{ number_format($stats['review_open'], 0, ',', '.') }}</div>
            <div class="stat-note">
                {{ $stats['review_needs_fix'] }}
                <span data-id="perlu perbaikan" data-en="need fixing">perlu perbaikan</span>
            </div>
    </div>


        </div>
    {{-- ── Role, queue, and compliance summary ──────────────── --}}
    <div class="grid grid-3 dash-section reveal reveal-2">
        <div class="card">
            <div class="section-heading">
                <div>
                    <h2 data-id="Sebaran Peran" data-en="Role Breakdown">Sebaran Peran</h2>
                    <p data-id="Jumlah akun per peran" data-en="Accounts per role">Jumlah akun per peran</p>
                </div>
            </div>

            @php
                $roleLabels = [
                    'admin' => ['id' => 'Admin', 'en' => 'Admin', 'tone' => 'purple'],
                    'reviewer' => ['id' => 'Reviewer', 'en' => 'Reviewer', 'tone' => 'blue'],
                    'user' => ['id' => 'Pengguna Biasa', 'en' => 'Regular User', 'tone' => 'grey'],
                ];
            @endphp

            <div class="kv">
                @foreach ($roleLabels as $role => $meta)
                    <div class="kv-row kv-row-split">
                        <dt><span class="pill pill-{{ $meta['tone'] }}" data-id="{{ $meta['id'] }}" data-en="{{ $meta['en'] }}">{{ $meta['id'] }}</span></dt>
                        <dd class="kv-strong">{{ number_format((int) ($roleBreakdown[$role] ?? 0), 0, ',', '.') }}</dd>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <div class="section-heading">
                <div>
                    <h2 data-id="Kasus Review" data-en="Review Cases">Kasus Review</h2>
                    <p data-id="Ringkasan antrean AI Quality Review" data-en="AI Quality Review queue summary">Ringkasan antrean AI Quality Review</p>
                </div>
                <a href="{{ route('admin.review.index') }}" class="subtle-link" data-id="Buka antrean" data-en="Open queue">Buka antrean</a>
            </div>

            <div class="kv">
                <div class="kv-row kv-row-split">
                    <dt data-id="Terbuka" data-en="Open">Terbuka</dt>
                    <dd class="kv-strong">{{ $stats['review_open'] }}</dd>
                </div>
                <div class="kv-row kv-row-split">
                    <dt data-id="Perlu perbaikan" data-en="Needs fixing">Perlu perbaikan</dt>
                    <dd class="kv-strong">{{ $stats['review_needs_fix'] }}</dd>
                </div>
                <div class="kv-row kv-row-split">
                    <dt data-id="Selesai" data-en="Finished">Selesai</dt>
                    <dd class="kv-strong">{{ $stats['review_done'] }}</dd>
                </div>
            </div>
        </div>

        <div class="card card-accent">
            <div class="section-heading">
                <div>
                    <h2 data-id="Kepatuhan & Audit" data-en="Compliance & Audit">Kepatuhan &amp; Audit</h2>
                    <p data-id="Setiap tindakan admin tercatat" data-en="Every admin action is recorded">Setiap tindakan admin tercatat</p>
                </div>
                <a href="{{ route('admin.audit.index') }}" class="subtle-link" data-id="Lihat audit" data-en="View audit">Lihat audit</a>
            </div>
            <p class="section-note"
               data-id="Setiap perubahan peran, status akun, katalog, dan keputusan tinjauan ditulis ke Audit Log (append-only) dan tidak dapat diubah dari antarmuka."
               data-en="Every role, account status, catalog, and review decision change is written to the append-only Audit Log and cannot be altered from the interface.">Setiap perubahan peran, status akun, katalog, dan keputusan tinjauan ditulis ke Audit Log (append-only) dan tidak dapat diubah dari antarmuka.</p>
        </div>
    </div>

    {{-- ── Recent activity ───────────────────────────────────── --}}
    <div class="grid grid-2 reveal reveal-3">
        <div class="card">
            <div class="section-heading">
                <div>
                    <h2 data-id="Pengguna Terbaru" data-en="Newest Users">Pengguna Terbaru</h2>
                    <p data-id="Akun yang baru dibuat" data-en="Recently created accounts">Akun yang baru dibuat</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="subtle-link" data-id="Kelola" data-en="Manage">Kelola</a>
            </div>

            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th data-id="Nama" data-en="Name">Nama</th>
                            <th data-id="Peran" data-en="Role">Peran</th>
                            <th data-id="Status" data-en="Status">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestUsers as $user)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.show', $user) }}"><b>{{ $user->name }}</b></a>
                                    <div class="muted">{{ $user->email }}</div>
                                </td>
                                <td><span class="pill pill-{{ $user->role === 'admin' ? 'purple' : ($user->role === 'reviewer' ? 'blue' : 'grey') }}">{{ $user->roleLabel()['id'] }}</span></td>
                                <td><span class="pill pill-{{ $user->statusTone() }}">{{ $user->statusLabel()['id'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="muted" data-id="Belum ada pengguna." data-en="No users yet.">Belum ada pengguna.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="section-heading">
                <div>
                    <h2 data-id="Antrean AI Quality Review" data-en="AI Quality Review Queue">Antrean AI Quality Review</h2>
                    <p data-id="Kasus yang menunggu penanganan" data-en="Cases awaiting handling">Kasus yang menunggu penanganan</p>
                </div>
                <a href="{{ route('admin.review.index') }}" class="subtle-link" data-id="Lihat semua" data-en="View all">Lihat semua</a>
            </div>

            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th data-id="Kasus" data-en="Case">Kasus</th>
                            <th data-id="Alasan" data-en="Reason">Alasan</th>
                            <th data-id="Status" data-en="Status">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($openReviewCases as $case)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.review.show', $case) }}"><b>#{{ $case->id }}</b></a>
                                    <div class="muted">{{ $case->user?->name }}</div>
                                </td>
                                <td><span class="pill pill-grey">{{ str_replace('_', ' ', $case->issue_type) }}</span></td>
                                <td><span class="pill pill-{{ $case->statusTone() }}">{{ str_replace('_', ' ', $case->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="muted" data-id="Tidak ada kasus terbuka." data-en="No open cases.">Tidak ada kasus terbuka.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
