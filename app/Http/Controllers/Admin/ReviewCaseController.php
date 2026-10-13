<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiReviewAction;
use App\Models\AiReviewCase;
use App\Services\AuditLogger;
use App\Support\AuditCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * AI Quality Review (admin & reviewer).
 *
 * Alur tinjauan: pending → in_review → valid | needs_fix | escalated → done.
 * Setiap keputusan dicatat pada `ai_review_actions` (append-only) dan Audit
 * Log sehingga alasan keputusan dapat ditelusuri.
 */
class ReviewCaseController extends Controller
{
    public function __construct(
        private readonly AuditLogger $audit,
    ) {}

    /**
     * Antrean kasus dengan filter status dan alasan.
     */
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'open');
        $issue = (string) $request->query('issue', '');

        $cases = AiReviewCase::query()
            ->with(['user', 'reviewer'])
            ->when($status === 'open', fn ($query) => $query->open())
            ->when(in_array($status, AiReviewCase::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->when(in_array($issue, AiReviewCase::ISSUE_TYPES, true), fn ($query) => $query->where('issue_type', $issue))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.review.index', [
            'cases' => $cases,
            'filters' => ['status' => $status, 'issue' => $issue],
            'statuses' => AiReviewCase::STATUSES,
            'issueTypes' => AiReviewCase::ISSUE_TYPES,
            'summary' => [
                'open' => AiReviewCase::query()->open()->count(),
                'pending' => AiReviewCase::query()->where('status', 'pending')->count(),
                'needs_fix' => AiReviewCase::query()->where('status', 'needs_fix')->count(),
                'escalated' => AiReviewCase::query()->where('status', 'escalated')->count(),
            ],
        ]);
    }

    /**
     * Detail kasus: pertanyaan, jawaban, referensi, dan riwayat tinjauan.
     */
    public function show(AiReviewCase $case): View
    {
        $this->authorizeCase($case);

        return view('admin.review.show', [
            'case' => $case->load(['user', 'reviewer', 'document']),
            'actions' => $case->actions()->with('user')->get(),
            'decisions' => AiReviewCase::DECISIONS,
        ]);
    }

    /**
     * Mulai meninjau kasus (pending → in_review).
     */
    public function claim(Request $request, AiReviewCase $case): RedirectResponse
    {
        $this->authorizeCase($case);

        if ($case->status !== 'pending') {
            return back()->with('error', 'Kasus ini sudah ditangani.|This case is already being handled.');
        }

        $this->transition($request, $case, 'claim', 'in_review', null);

        return back()->with('status', 'Kasus mulai ditinjau.|Case marked as in review.');
    }

    /**
     * Simpan keputusan reviewer.
     */
    public function decide(Request $request, AiReviewCase $case): RedirectResponse
    {
        $this->authorizeCase($case);

        $data = $request->validate([
            'decision' => ['required', Rule::in(AiReviewCase::DECISIONS)],
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $action = match ($data['decision']) {
            'valid' => 'valid',
            'needs_fix' => 'needs_fix',
            'escalated' => 'escalate',
            default => 'done',
        };

        $this->transition($request, $case, $action, $data['decision'], $data['note']);

        return back()->with('status', 'Keputusan tinjauan tersimpan.|Review decision saved.');
    }

    /**
     * Tambahkan catatan tanpa mengubah status.
     */
    public function note(Request $request, AiReviewCase $case): RedirectResponse
    {
        $this->authorizeCase($case);

        $data = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $this->transition($request, $case, 'note', $case->status, $data['note']);

        return back()->with('status', 'Catatan ditambahkan.|Note added.');
    }

    /**
     * Terapkan perubahan status beserta pencatatan riwayat.
     */
    private function transition(
        Request $request,
        AiReviewCase $case,
        string $action,
        string $toStatus,
        ?string $note
    ): void {
        $fromStatus = $case->status;

        $case->update([
            'status' => $toStatus,
            'reviewed_by' => $request->user()->getKey(),
            'reviewed_at' => now(),
            'review_decision' => $toStatus === 'in_review' ? $case->review_decision : $toStatus,
            'review_note' => $note ?? $case->review_note,
        ]);

        AiReviewAction::create([
            'ai_review_case_id' => $case->getKey(),
            'user_id' => $request->user()->getKey(),
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'note' => $note,
        ]);

        $this->audit->log([
            'category' => AuditCategory::AI_QUALITY_REVIEW,
            'action' => "review.{$action}",
            'description' => "Kasus #{$case->getKey()} ditandai {$toStatus}.",
            'subject' => $case,
            'before' => ['status' => $fromStatus],
            'after' => ['status' => $toStatus],
            'reason' => $note,
        ]);
    }

    /**
     * Reviewer hanya boleh menangani kasus yang ditugaskan; admin menangani
     * semua kasus.
     */
    private function authorizeCase(AiReviewCase $case): void
    {
        abort_unless(
            (bool) auth()->user()?->can('review-case', [$case->user]),
            403,
            'Anda tidak memiliki hak akses untuk kasus ini.'
        );
    }
}
