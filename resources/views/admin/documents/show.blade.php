@extends('layouts.admin')

@section('title', 'Detail Dokumen')

@section('content')
    <div class="section-heading reveal">
        <div>
            <h1 class="page-title" style="margin-bottom:5px">{{ $document->original_name }}</h1>
            <p class="page-subtitle" style="margin-bottom:0">
                {{ $document->user?->name }} · {{ $document->user?->email }}
            </p>
        </div>
        <div class="row-actions">
            <a href="{{ route('admin.documents.download', $document) }}" class="btn btn-ghost"
               data-id="Unduh" data-en="Download">Unduh</a>
            <a href="{{ route('admin.documents.index') }}" class="btn btn-ghost"
               data-id="Kembali" data-en="Back">Kembali</a>
        </div>
    </div>

    <div class="grid grid-2 reveal reveal-1">
        <div class="grid">
            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Ringkasan Dokumen" data-en="Document Summary">Ringkasan Dokumen</h2>
                    </div>
                </div>

                <div class="kv">
                    <div class="kv-row">
                        <dt data-id="Status" data-en="Status">Status</dt>
                        <dd><span class="pill pill-{{ $document->statusTone() }}">{{ ucfirst($document->status) }}</span></dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Kategori" data-en="Category">Kategori</dt>
                        <dd>{{ $document->category ? ucfirst(str_replace('_', ' ', $document->category)) : '—' }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Ekstraksi" data-en="Extraction">Ekstraksi</dt>
                        <dd>{{ ucfirst($document->extraction_status) }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Analisis" data-en="Analysis">Analisis</dt>
                        <dd>{{ ucfirst($document->analysis_status) }}</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Ukuran" data-en="Size">Ukuran</dt>
                        <dd>{{ number_format($document->size_bytes / 1024, 0) }} KB</dd>
                    </div>
                    <div class="kv-row">
                        <dt data-id="Selesai Diproses" data-en="Processed At">Selesai Diproses</dt>
                        <dd>{{ $document->processed_at?->format('d M Y H:i') ?? '—' }}</dd>
                    </div>
                </div>

                @if ($document->failure_reason)
                    <div class="flash flash-error" style="margin:16px 0 0">
                        <b data-id="Alasan gagal:" data-en="Failure reason:">Alasan gagal:</b>
                        {{ $document->failure_reason }}
                    </div>
                @endif
            </div>

            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Hasil Analisis" data-en="Analysis Result">Hasil Analisis</h2>
                    </div>
                </div>

                @php $summary = $document->summary ?? []; @endphp

                @if (! empty($summary))
                    <div class="kv">
                        @foreach ($summary as $key => $value)
                            <div class="kv-row">
                                <dt>{{ ucfirst(str_replace('_', ' ', (string) $key)) }}</dt>
                                <dd>{{ is_array($value) ? implode(', ', $value) : $value }}</dd>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty">
                        <span data-id="Belum ada ringkasan analisis." data-en="No analysis summary yet.">Belum ada ringkasan analisis.</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Riwayat Pemrosesan" data-en="Processing History">Riwayat Pemrosesan</h2>
                        <p data-id="Urutan tahapan yang tercatat" data-en="Recorded stage sequence">Urutan tahapan yang tercatat</p>
                    </div>
                </div>

                <div class="timeline">
                    @forelse ($events as $event)
                        <div class="timeline-item">
                            <span class="timeline-dot {{ $event->tone() }}"></span>
                            <div class="timeline-body">
                                <b>{{ ucfirst(str_replace('_', ' ', $event->stage)) }} — {{ ucfirst($event->status) }}</b>
                                <span>{{ $event->created_at?->format('d M Y H:i') }}</span>
                                @if ($event->message)
                                    <p>{{ $event->message }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty">
                            <span data-id="Belum ada tahapan tercatat." data-en="No stages recorded yet.">Belum ada tahapan tercatat.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="section-heading">
                    <div>
                        <h2 data-id="Kasus Tinjauan Terkait" data-en="Related Review Cases">Kasus Tinjauan Terkait</h2>
                    </div>
                </div>

                <div class="timeline">
                    @forelse ($reviewCases as $case)
                        <div class="timeline-item">
                            <span class="timeline-dot {{ $case->statusTone() }}"></span>
                            <div class="timeline-body">
                                <b><a href="{{ route('admin.review.show', $case) }}">#{{ $case->id }} {{ str_replace('_', ' ', $case->issue_type) }}</a></b>
                                <span>{{ $case->created_at?->format('d M Y H:i') }} · {{ ucfirst($case->status) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty">
                            <span data-id="Tidak ada kasus tinjauan untuk dokumen ini." data-en="No review cases for this document.">Tidak ada kasus tinjauan untuk dokumen ini.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
