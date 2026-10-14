@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
    <h1 class="page-title reveal" data-id="Manajemen Pengguna" data-en="User Management">Manajemen Pengguna</h1>
    <p class="page-subtitle reveal reveal-1"
       data-id="Kelola peran dan status akun. Menonaktifkan akun tidak menghapus riwayatnya."
       data-en="Manage account roles and statuses. Deactivating an account does not erase its history.">Kelola peran dan status akun. Menonaktifkan akun tidak menghapus riwayatnya.</p>

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
            <div class="stat-label" data-id="Ditangguhkan" data-en="Suspended">Ditangguhkan</div>
            <div class="stat-value">{{ number_format($summary['suspended'], 0, ',', '.') }}</div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="filters reveal reveal-2">
        <div class="field" style="flex:1">
            <label for="q" data-id="Pencarian" data-en="Search">Pencarian</label>
            <input type="search" id="q" name="q" value="{{ $filters['q'] }}"
                   placeholder="Nama, email, atau telepon"
                   data-id-placeholder="Nama, email, atau telepon" data-en-placeholder="Name, email, or phone">
        </div>

        <div class="field">
            <label for="role" data-id="Peran" data-en="Role">Peran</label>
            <select id="role" name="role">
                <option value="" data-id="Semua peran" data-en="All roles">Semua peran</option>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected($filters['role'] === $role)>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
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
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost" data-id="Reset" data-en="Reset">Reset</a>
    </form>

    <div class="table-wrap reveal reveal-2">
        <table class="data">
            <thead>
                <tr>
                    <th data-id="Pengguna" data-en="User">Pengguna</th>
                    <th data-id="Peran" data-en="Role">Peran</th>
                    <th data-id="Status" data-en="Status">Status</th>
                    <th data-id="Aktivitas Terakhir" data-en="Last Activity">Aktivitas Terakhir</th>
                    <th class="num" data-id="Polis / Kasus" data-en="Policies / Cases">Polis / Kasus</th>
                    <th data-id="Aksi" data-en="Actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <a href="{{ route('admin.users.show', $user) }}"><b>{{ $user->name }}</b></a>
                            <div class="muted">{{ $user->email }}</div>
                            @if ($user->phone)
                                <div class="muted">{{ $user->phone }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="pill pill-{{ $user->role === 'admin' ? 'purple' : ($user->role === 'reviewer' ? 'blue' : 'grey') }}">
                                {{ $user->roleLabel()['id'] }}
                            </span>
                        </td>
                        <td>
                            <span class="pill pill-{{ $user->statusTone() }}">{{ $user->statusLabel()['id'] }}</span>
                        </td>
                        <td class="muted">
                            {{ $user->last_activity_at?->format('d M Y H:i') ?? '—' }}
                        </td>
                        <td class="num">{{ $user->policies_count }} / {{ $user->review_cases_count }}</td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-ghost btn-sm"
                                   data-id="Kelola" data-en="Manage">Kelola</a>

                                @if (! $user->is(auth()->user()))
                                    @if ($user->status === 'active')
                                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    data-id="Tangguhkan" data-en="Suspend">Tangguhkan</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-ghost btn-sm"
                                                    data-id="Aktifkan" data-en="Activate">Aktifkan</button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('{{ $user->email }} akan dihapus permanen beserta seluruh datanya. Lanjutkan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                data-id="Hapus" data-en="Delete">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty">
                                <strong data-id="Tidak ada pengguna yang cocok" data-en="No matching users">Tidak ada pengguna yang cocok</strong>
                                <span data-id="Ubah kata kunci atau filter pencarian."
                                      data-en="Adjust your search keyword or filters.">Ubah kata kunci atau filter pencarian.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pager reveal reveal-3">{{ $users->links() }}</div>
@endsection
