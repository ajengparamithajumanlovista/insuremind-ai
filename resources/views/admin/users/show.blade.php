@extends('layouts.admin')

@section('title', 'Detail Pengguna')

@section('content')
    <div class="section-heading reveal">
        <div>
            <h1 class="page-title" style="margin-bottom:5px">{{ $user->name }}</h1>
            <p class="page-subtitle" style="margin-bottom:0">{{ $user->email }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost" data-id="Kembali" data-en="Back">Kembali</a>
    </div>

    <div class="grid grid-2 reveal reveal-1" style="margin-bottom:16px">
        {{-- ── Formulir pengelolaan ────────────────────────────── --}}
        <div class="card">
            <div class="section-heading">
                <div>
                    <h2 data-id="Data Akun" data-en="Account Data">Data Akun</h2>
                    <p data-id="Perubahan peran dan status tercatat ke Audit Log" data-en="Role and status changes are written to the Audit Log">Perubahan peran dan status tercatat ke Audit Log</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid">
                @csrf
                @method('PUT')

                <div class="field">
                    <label for="name" data-id="Nama" data-en="Name">Nama</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="grid grid-2">
                    <div class="field">
                        <label for="role" data-id="Peran" data-en="Role">Peran</label>
                        <select id="role" name="role" @disabled($user->is(auth()->user()))>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                        @if ($user->is(auth()->user()))
                            <span class="hint" data-id="Peran akun sendiri tidak dapat diubah." data-en="Your own role cannot be changed.">Peran akun sendiri tidak dapat diubah.</span>
                        @endif
                    </div>

                    <div class="field">
                        <label for="status" data-id="Status" data-en="Status">Status</label>
                        <select id="status" name="status">
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(old('status', $user->status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="phone" data-id="Telepon" data-en="Phone">Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>

                <div class="field">
                    <label for="admin_note" data-id="Catatan Internal" data-en="Internal Note">Catatan Internal</label>
                    <textarea id="admin_note" name="admin_note" rows="3"
                              data-id-placeholder="Catatan ini tidak ditampilkan kepada pemilik akun."
                              data-en-placeholder="This note is never shown to the account owner.">{{ old('admin_note', $user->admin_note) }}</textarea>
                    <span class="hint" data-id="Hanya terlihat oleh admin." data-en="Visible to admins only.">Hanya terlihat oleh admin.</span>
                </div>

                <button type="submit" class="btn btn-primary" data-id="Simpan Perubahan" data-en="Save Changes">Simpan Perubahan</button>
            </form>
        </div>

        {{-- ── Summary & history ───────────────────────────────── --}}
        <div class="grid">
            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Ringkasan Aktivitas" data-en="Activity Summary">Ringkasan Aktivitas</h2>
                    </div>
                </div>

                <div class="kv">
                    <div class="kv-row">
                        <dt data-id="Status" data-en="Status">Status</dt>
                        <dd><span class="pill pill-{{ $user->statusTone() }}">{{ $user->statusLabel()['id'] }}</span></dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Peran" data-en="Role">Peran</dt>
                        <dd>{{ $user->roleLabel()['id'] }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Polis" data-en="Policies">Polis</dt>
                        <dd>{{ $user->policies_count }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Analisis Risiko" data-en="Risk Assessments">Analisis Risiko</dt>
                        <dd>{{ $user->risk_assessments_count }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Pesan Chat" data-en="Chat Messages">Pesan Chat</dt>
                        <dd>{{ $user->chat_messages_count }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Kasus Review" data-en="Review Cases">Kasus Review</dt>
                        <dd>{{ $user->review_cases_count }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Terdaftar" data-en="Registered">Terdaftar</dt>
                        <dd>{{ $user->created_at?->format('d M Y H:i') }}</dd>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Audit Terkait" data-en="Related Audit">Audit Terkait</h2>
                        <p data-id="Perubahan terbaru atas akun ini" data-en="Recent changes to this account">Perubahan terbaru atas akun ini</p>
                    </div>
                </div>

                <div class="timeline">
                    @forelse ($auditLogs as $log)
                        <div class="timeline-item">
                            <span class="timeline-dot {{ $log->resultTone() }}"></span>
                            <div class="timeline-body">
                                <b>{{ $log->description }}</b>
                                <span>{{ $log->created_at?->format('d M Y H:i') }} · {{ $log->actor_name ?? 'Sistem' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty">
                            <span data-id="Belum ada catatan audit untuk akun ini." data-en="No audit entries for this account yet.">Belum ada catatan audit untuk akun ini.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ── Danger zone: permanently delete account ─────── --}}
            @if (! $user->is(auth()->user()))
                <div class="card" style="border-color:#f3cdd7">
                    <div class="section-heading">
                        <div>
                            <h2 data-id="Hapus Akun" data-en="Delete Account">Hapus Akun</h2>
                            <p data-id="Tindakan ini permanen dan tidak dapat dibatalkan."
                               data-en="This action is permanent and cannot be undone.">Tindakan ini permanen dan tidak dapat dibatalkan.</p>
                        </div>
                    </div>

                    <p class="hint" style="margin-bottom:12px"
                       data-id="Menghapus akun akan membuang profil, polis, percakapan, dan data terkait lainnya. Catatan Audit Log tetap tersimpan."
                       data-en="Deleting the account removes the profile, policies, conversations, and other related data. The Audit Log entries are kept.">Menghapus akun akan membuang profil, polis, percakapan, dan data terkait lainnya. Catatan Audit Log tetap tersimpan.</p>

                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                          onsubmit="return confirm('{{ $user->email }} akan dihapus permanen beserta seluruh datanya. Lanjutkan?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                                data-id="Hapus Akun Ini" data-en="Delete This Account">Hapus Akun Ini</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
