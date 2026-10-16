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
 * Policy Documents (admin only).
 *
 * Displays each document's processing trail (upload → validation → extraction
 * → analysis) as-is. A `failed` status is never shown as successful just
 * because a document is reopened (brief section 4E).
 */
class DocumentController extends Controller
{
    public function __construct(
        private readonly AuditLogger $audit,
    ) {}

    /**
     * List documents with status filters.
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
     * Document details, processing stages, and analysis results.
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
     * Send a document file to an admin (controlled download).
     *
     * Downloads are recorded in the Audit Log because they involve a user's
     * private files.
     */
    public function download(Request $request, PolicyDocument $document): StreamedResponse|RedirectResponse
    {
        // Access to private files is recorded first, even when the file is not
        // found, so download attempts can still be traced.
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
