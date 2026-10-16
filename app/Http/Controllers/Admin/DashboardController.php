<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiReviewCase;
use App\Models\AuditLog;
use App\Models\InsuranceProduct;
use App\Models\PolicyDocument;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin operations summary.
 *
 * Only admins can access this page (see the route middleware).
 * Reviewers are sent directly to the AI Quality Review queue.
 */
class DashboardController extends Controller
{
    /**
     * Summary of account status, catalog, documents, and review queue.
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Reviewers cannot access the admin summary; direct them to their queue.
        if (! $request->user()->can('access-admin-dashboard')) {
            return redirect()->route('admin.review.index');
        }

        return view('admin.dashboard', [
            'stats' => [
                'users_total' => User::query()->count(),
                'users_active' => User::query()->where('status', 'active')->count(),
                'users_admin' => User::query()->where('role', 'admin')->count(),
                'users_suspended' => User::query()->where('status', 'suspended')->count(),
                'products_total' => InsuranceProduct::query()->count(),
                'products_active' => InsuranceProduct::query()->where('is_active', true)->count(),
                'documents_total' => PolicyDocument::query()->count(),
                'documents_failed' => PolicyDocument::query()->where('status', 'failed')->count(),
                'documents_review' => PolicyDocument::query()->where('status', 'review')->count(),
                'review_open' => AiReviewCase::query()->open()->count(),
                'review_needs_fix' => AiReviewCase::query()->where('status', 'needs_fix')->count(),
                'review_done' => AiReviewCase::query()->finished()->count(),
            ],
            'latestUsers' => User::query()->latest('id')->take(5)->get(),
            'latestDocuments' => PolicyDocument::query()->with('user')->latest('id')->take(5)->get(),
            'openReviewCases' => AiReviewCase::query()->open()->with('user')->latest('id')->take(5)->get(),
            'recentAudit' => AuditLog::query()->with('user')->latest('id')->take(6)->get(),
            'roleBreakdown' => User::query()
                ->selectRaw('role, count(*) as total')
                ->groupBy('role')
                ->pluck('total', 'role'),
        ]);
    }
}
