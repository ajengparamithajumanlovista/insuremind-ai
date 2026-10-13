<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PolicyDocument;
use App\Services\AuditLogger;
use App\Support\AuditCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Dokumen Polis (khusus admin).
 *
 * Menampilkan jejak pemrosesan tiap dokumen (upload → validasi → ekstraksi →
 * analisis) apa adanya. Status `failed` tidak pernah ditampilkan sebagai
 * sukses hanya karena dokumen dibuka kembali (brief bagian 4E).
 */
class DocumentController extends Controller
{
    public function __construct(
        private readonly AuditLogger $audit,
    ) {}

    /**
     * Daftar dokumen dengan filter status.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        $documents = PolicyDocument::query()
            ->with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('original_name', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->when(in_array($status, PolicyDocument::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.documents.index', [
            'documents' => $documents,
            'filters' => ['q' => $search, 'status' => $status],
            'statuses' => PolicyDocument::STATUSES,
            'summary' => [
                'total' => PolicyDocument::query()->count(),
                'processing' => PolicyDocument::query()->where('status', 'processing')->count(),
                'review' => PolicyDocument::query()->where('status', 'review')->count(),
                'failed' => PolicyDocument::query()->where('status', 'failed')->count(),
            ],
        ]);
    }

    /**
     * Detail dokumen beserta tahapan pemrosesan dan hasil analisisnya.
     */
    public function show(PolicyDocument $document): View
    {
        return view('admin.documents.show', [
            'document' => $document->load('user'),
            'events' => $document->events()->get(),
            'reviewCases' => $document->reviewCases()->latest('id')->get(),
        ]);
    }

    /**
     * Kirim berkas dokumen ke admin (download terkendali).
     *
     * Pengunduhan dicatat ke Audit Log karena menyangkut berkas pribadi
     * pengguna.
     */
    public function download(Request $request, PolicyDocument $document): StreamedResponse|RedirectResponse
    {
        // Akses berkas pribadi dicatat lebih dulu, termasuk saat berkas tidak
        // ditemukan, agar percobaan pengunduhan tetap dapat ditelusuri.
        $this->audit->log([
            'category' => AuditCategory::POLICY_DOCUMENT,
            'action' => 'document.downloaded',
            'description' => "Mengunduh dokumen {$document->original_name}.",
            'subject' => $document,
        ]);

        $disk = Storage::disk($document->disk);

        if (! $disk->exists($document->path)) {
            return back()->with('error', 'Berkas tidak ditemukan pada penyimpanan.|File not found on storage.');
        }

        return $disk->download($document->path, $document->original_name);
    }
}
