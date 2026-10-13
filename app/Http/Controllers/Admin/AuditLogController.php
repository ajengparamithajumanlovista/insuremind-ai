<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Support\AuditCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Audit Log (khusus admin).
 *
 * Tabel ini append-only: halaman ini hanya menyediakan pembacaan dan ekspor,
 * tidak ada endpoint untuk mengubah atau menghapus baris log. Koreksi
 * dilakukan dengan menulis baris baru melalui layanan AuditLogger.
 */
class AuditLogController extends Controller
{
    /**
     * Baris log dengan filter kategori, aksi, hasil, dan rentang tanggal.
     */
    public function index(Request $request): View
    {
        $logs = $this->filtered($request)
            ->with('user')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.audit.index', [
            'logs' => $logs,
            'filters' => [
                'q' => trim((string) $request->query('q', '')),
                'category' => (string) $request->query('category', ''),
                'result' => (string) $request->query('result', ''),
                'from' => (string) $request->query('from', ''),
                'to' => (string) $request->query('to', ''),
            ],
            'categories' => AuditCategory::all(),
            'summary' => [
                'total' => AuditLog::query()->count(),
                'today' => AuditLog::query()->whereDate('created_at', now()->toDateString())->count(),
                'failed' => AuditLog::query()->where('result', 'failed')->count(),
                'actors' => AuditLog::query()->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            ],
        ]);
    }

    /**
     * Detail satu baris log, termasuk nilai before/after.
     */
    public function show(AuditLog $auditLog): View
    {
        return view('admin.audit.show', [
            'log' => $auditLog->load('user'),
            'corrections' => AuditLog::query()->where('corrects_log_id', $auditLog->getKey())->latest('id')->get(),
        ]);
    }

    /**
     * Ekspor hasil filter sebagai berkas CSV.
     *
     * Ekspor dibatasi agar berkas tidak membengkakkan memori; gunakan filter
     * untuk mempersempit rentang bila diperlukan.
     */
    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filtered($request)->latest('id')->limit(5000)->get();

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'event_id', 'created_at', 'actor_role', 'actor_name', 'category',
                'action', 'description', 'subject_type', 'subject_id', 'result', 'ip_address',
            ]);

            foreach ($rows as $log) {
                fputcsv($handle, [
                    $log->event_id,
                    $log->created_at?->toDateTimeString(),
                    $log->actor_role,
                    $log->actor_name,
                    $log->category,
                    $log->action,
                    $log->description,
                    $log->subject_type,
                    $log->subject_id,
                    $log->result,
                    $log->ip_address,
                ]);
            }

            fclose($handle);
        };

        $filename = 'audit-log-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Terapkan filter yang sama pada daftar dan ekspor.
     */
    private function filtered(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', '');
        $result = (string) $request->query('result', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        return AuditLog::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('description', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%")
                        ->orWhere('actor_name', 'like', "%{$search}%")
                        ->orWhere('subject_label', 'like', "%{$search}%");
                });
            })
            ->when(in_array($category, AuditCategory::all(), true), fn ($query) => $query->where('category', $category))
            ->when(in_array($result, ['success', 'failed'], true), fn ($query) => $query->where('result', $result))
            ->when($from !== '', fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to !== '', fn ($query) => $query->whereDate('created_at', '<=', $to));
    }
}
