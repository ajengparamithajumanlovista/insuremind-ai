@extends('layouts.admin')

@section('title', 'Detail Audit')

@section('content')
    <div class="section-heading reveal">
        <div>
            <h1 class="page-title" style="margin-bottom:5px">#{{ $log->id }} {{ $log->action }}</h1>
            <p class="page-subtitle" style="margin-bottom:0">
                <span class="pill pill-{{ $log->resultTone() }}">{{ ucfirst($log->result) }}</span>
                <span class="pill pill-grey">{{ $log->categoryLabel()['id'] }}</span>
            </p>
        </div>
        <a href="{{ route('admin.audit.index') }}" class="btn btn-ghost" data-id="Kembali" data-en="Back">Kembali</a>
    </div>

    <div class="grid grid-2 reveal reveal-1">
        <div class="card">
            <div class="section-heading">
                <div>
                    <h2 data-id="Detail Peristiwa" data-en="Event Detail">Detail Peristiwa</h2>
                </div>
            </div>

            <div class="kv">
                <div class="kv-row">
                    <dt data-id="Event ID" data-en="Event ID">Event ID</dt>
                    <dd style="font-family:ui-monospace,monospace;font-size:11.5px">{{ $log->event_id }}</dd>
                </div>
                <div class="kv-row">
                    <dt data-id="Waktu" data-en="Time">Waktu</dt>
                    <dd>{{ $log->created_at?->format('d M Y H:i:s') }}</dd>
                </div>
                <div class="kv-row">
                    <dt data-id="Pelaku" data-en="Actor">Pelaku</dt>
                    <dd>{{ $log->actor_name ?? 'Sistem' }} ({{ $log->actor_role }})</dd>
                </div>
                <div class="kv-row">
                    <dt data-id="Deskripsi" data-en="Description">Deskripsi</dt>
                    <dd>{{ $log->description }}</dd>
                </div>
                <div class="kv-row">
                    <dt data-id="Objek" data-en="Subject">Objek</dt>
                    <dd>
                        {{ $log->subject_label ?? '—' }}
                        @if ($log->subject_type)
                            <div class="muted">{{ $log->subject_type }} #{{ $log->subject_id }}</div>
                        @endif
                    </dd>
                </div>
                <div class="kv-row">
                    <dt data-id="Alamat IP" data-en="IP Address">Alamat IP</dt>
                    <dd>{{ $log->ip_address ?? '—' }}</dd>
                </div>
                <div class="kv-row">
                    <dt data-id="Request ID" data-en="Request ID">Request ID</dt>
                    <dd>{{ $log->request_id ?? '—' }}</dd>
                </div>
                <div class="kv-row">
                    <dt data-id="User Agent" data-en="User Agent">User Agent</dt>
                    <dd style="font-size:11.5px;color:var(--muted)">{{ $log->user_agent ?? '—' }}</dd>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Nilai Sebelum / Sesudah" data-en="Before / After Values">Nilai Sebelum / Sesudah</h2>
                        <p data-id="Kata sandi dan token telah disunting" data-en="Passwords and tokens have been redacted">Kata sandi dan token telah disunting</p>
                    </div>
                </div>

                @if (! empty($log->before) || ! empty($log->after))
                    <div class="diff">
                        @foreach (collect(array_keys($log->after ?? []))->merge(array_keys($log->before ?? []))->unique() as $field)
                            <div class="diff-row">
                                <div class="diff-cell old">
                                    <small>{{ $field }}</small>
                                    {{ data_get($log->before, $field) ?? '—' }}
                                </div>
                                <div class="diff-cell new">
                                    <small>{{ $field }}</small>
                                    {{ data_get($log->after, $field) ?? '—' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty">
                        <span data-id="Tidak ada perubahan nilai yang dicatat." data-en="No value changes recorded.">Tidak ada perubahan nilai yang dicatat.</span>
                    </div>
                @endif

                @if ($log->reason)
                    <div class="side-note" style="margin-top:16px">
                        <strong data-id="Alasan" data-en="Reason">Alasan</strong>
                        <span>{{ $log->reason }}</span>
                    </div>
                @endif
            </div>

            @if ($corrections->isNotEmpty())
                <div class="card">
                    <div class="section-heading">
                        <div>
                            <h2 data-id="Koreksi Terkait" data-en="Related Corrections">Koreksi Terkait</h2>
                            <p data-id="Baris baru yang mengoreksi catatan ini" data-en="Newer rows correcting this entry">Baris baru yang mengoreksi catatan ini</p>
                        </div>
                    </div>

                    <div class="timeline">
                        @foreach ($corrections as $correction)
                            <div class="timeline-item">
                                <span class="timeline-dot blue"></span>
                                <div class="timeline-body">
                                    <b>{{ $correction->description }}</b>
                                    <span>{{ $correction->created_at?->format('d M Y H:i') }} · {{ $correction->actor_name ?? 'Sistem' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
