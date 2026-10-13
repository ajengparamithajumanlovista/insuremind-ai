@extends('layouts.admin')

@section('title', 'Tinjau Kasus #'.$case->id)

@section('content')
    <div class="section-heading reveal">
        <div>
            <h1 class="page-title" style="margin-bottom:5px">Kasus #{{ $case->id }}</h1>
            <p class="page-subtitle" style="margin-bottom:0">
                <span class="pill pill-{{ $case->statusTone() }}">{{ ucfirst(str_replace('_', ' ', $case->status)) }}</span>
                <span class="pill pill-grey">{{ str_replace('_', ' ', $case->issue_type) }}</span>
            </p>
        </div>
        <a href="{{ route('admin.review.index') }}" class="btn btn-ghost" data-id="Kembali" data-en="Back">Kembali</a>
    </div>

    <div class="grid grid-2 reveal reveal-1">
        <div class="grid">
            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Pertanyaan" data-en="Question">Pertanyaan</h2>
                    </div>
                </div>
                <p style="font-size:13px;color:#35435a;margin:0">{{ $case->question ?? '—' }}</p>
            </div>

            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Jawaban Asisten" data-en="Assistant Answer">Jawaban Asisten</h2>
                        <p data-id="Snapshot jawaban saat kasus dibuat" data-en="Answer snapshot when the case was created">Snapshot jawaban saat kasus dibuat</p>
                    </div>
                </div>
                <p style="font-size:13px;color:#35435a;margin:0;white-space:pre-line">{{ $case->answer ?? '—' }}</p>
            </div>

            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Referensi Ditemukan" data-en="Found References">Referensi Ditemukan</h2>
                    </div>
                </div>

                @php $references = $case->references ?? []; @endphp

                @if (! empty($references))
                    <div class="kv">
                        @foreach ($references as $ref)
                            <div class="kv-row">
                                <dt data-id="Sumber" data-en="Source">Sumber</dt>
                                <dd>{{ is_array($ref) ? json_encode($ref, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $ref }}</dd>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty">
                        <span data-id="Tidak ada referensi yang dapat diverifikasi." data-en="No verifiable references.">Tidak ada referensi yang dapat diverifikasi.</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Konteks Kasus" data-en="Case Context">Konteks Kasus</h2>
                    </div>
                </div>

                <div class="kv">
                    <div class="kv-row">
                        <dt data-id="Pengguna" data-en="User">Pengguna</dt>
                        <dd>{{ $case->user?->name ?? '—' }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Kategori" data-en="Category">Kategori</dt>
                        <dd>{{ $case->category ?? '—' }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Dibuat" data-en="Created">Dibuat</dt>
                        <dd>{{ $case->created_at?->format('d M Y H:i') }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Reviewer" data-en="Reviewer">Reviewer</dt>
                        <dd>{{ $case->reviewer?->name ?? '—' }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Dokumen Terkait" data-en="Related Document">Dokumen Terkait</dt>
                        <dd>
                            @if ($case->document)
                                <a href="{{ route('admin.documents.show', $case->document) }}">{{ $case->document->original_name }}</a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                </div>
            </div>

            {{-- ── Panel keputusan ─────────────────────────────── --}}
            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Keputusan Tinjauan" data-en="Review Decision">Keputusan Tinjauan</h2>
                        <p data-id="Keputusan tercatat pada riwayat dan Audit Log" data-en="Decisions are recorded in history and the Audit Log">Keputusan tercatat pada riwayat dan Audit Log</p>
                    </div>
                </div>

                @if ($case->status === 'pending')
                    <form method="POST" action="{{ route('admin.review.claim', $case) }}" style="margin-bottom:16px">
                        @csrf
                        <button type="submit" class="btn btn-ghost" data-id="Mulai Tinjau" data-en="Start Review">Mulai Tinjau</button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.review.decide', $case) }}" class="grid">
                    @csrf

                    <div class="field">
                        <label for="decision" data-id="Keputusan" data-en="Decision">Keputusan</label>
                        <select id="decision" name="decision" required>
                            @foreach ($decisions as $decision)
                                <option value="{{ $decision }}" @selected($case->review_decision === $decision)>
                                    {{ ['valid' => 'Valid', 'needs_fix' => 'Perlu Perbaikan', 'escalated' => 'Eskalasi', 'done' => 'Selesai'][$decision] ?? ucfirst($decision) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="note" data-id="Catatan Wajib" data-en="Required Note">Catatan Wajib</label>
                        <textarea id="note" name="note" rows="4" required
                                  data-id-placeholder="Jelaskan alasan keputusan ini."
                                  data-en-placeholder="Explain the reason for this decision.">{{ old('note', $case->review_note) }}</textarea>
                        <span class="hint" data-id="Catatan membantu penelusuran audit." data-en="The note helps audit traceability.">Catatan membantu penelusuran audit.</span>
                    </div>

                    <button type="submit" class="btn btn-primary" data-id="Simpan Keputusan" data-en="Save Decision">Simpan Keputusan</button>
                </form>

                <form method="POST" action="{{ route('admin.review.note', $case) }}" class="grid" style="margin-top:18px;border-top:1px solid var(--line);padding-top:16px">
                    @csrf

                    <div class="field">
                        <label for="note-only" data-id="Catatan Tambahan" data-en="Additional Note">Catatan Tambahan</label>
                        <textarea id="note-only" name="note" rows="2" required
                                  data-id-placeholder="Tambahkan catatan tanpa mengubah status."
                                  data-en-placeholder="Add a note without changing the status."></textarea>
                    </div>

                    <button type="submit" class="btn btn-ghost" data-id="Tambah Catatan" data-en="Add Note">Tambah Catatan</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Riwayat tinjauan ─────────────────────────────────── --}}
    <div class="card reveal reveal-2" style="margin-top:16px">
        <div class="section-heading">
            <div>
                <h2 data-id="Riwayat Tinjauan" data-en="Review History">Riwayat Tinjauan</h2>
                <p data-id="Setiap keputusan reviewer tercatat (append-only)" data-en="Every reviewer decision is recorded (append-only)">Setiap keputusan reviewer tercatat (append-only)</p>
            </div>
        </div>

        <div class="timeline">
            @forelse ($actions as $action)
                <div class="timeline-item">
                    <span class="timeline-dot {{ in_array($action->to_status, ['valid', 'done'], true) ? 'green' : ($action->to_status === 'needs_fix' ? 'rose' : 'blue') }}"></span>
                    <div class="timeline-body">
                        <b>{{ str_replace('_', ' ', $action->action) }} → {{ str_replace('_', ' ', $action->to_status ?? $action->from_status) }}</b>
                        <span>{{ $action->created_at?->format('d M Y H:i') }} · {{ $action->user?->name ?? 'Sistem' }}</span>
                        @if ($action->note)
                            <p>{{ $action->note }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty">
                    <span data-id="Belum ada tindakan tinjauan." data-en="No review actions yet.">Belum ada tindakan tinjauan.</span>
                </div>
            @endforelse
        </div>
    </div>
@endsection
