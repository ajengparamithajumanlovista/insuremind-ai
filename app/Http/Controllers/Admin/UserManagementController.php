<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use App\Support\AuditCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * User management (admin only).
 *
 * Deactivating an account does NOT delete its data: `status` is changed so the
 * user loses sign-in access, but their activity history remains intact.
 * Every role/status change is recorded in the Audit Log.
 */
class UserManagementController extends Controller
{
    public function __construct(
        private readonly AuditLogger $audit,
    ) {}

    /**
     * List users with search and filters.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $role = (string) $request->query('role', '');
        $status = (string) $request->query('status', '');

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when(in_array($role, User::ROLES, true), fn ($query) => $query->where('role', $role))
            ->when(in_array($status, User::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->withCount(['policies', 'reviewCases'])
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => ['q' => $search, 'role' => $role, 'status' => $status],
            'roles' => User::ROLES,
            'statuses' => User::STATUSES,
            'summary' => [
                'total' => User::query()->count(),
                'active' => User::query()->where('status', 'active')->count(),
                'inactive' => User::query()->where('status', 'inactive')->count(),
                'suspended' => User::query()->where('status', 'suspended')->count(),
            ],
        ]);
    }

    /**
     * Account details and related activity history.
     */
    public function show(User $user): View
    {
        return view('admin.users.show', [
            'user' => $user->loadCount(['policies', 'riskAssessments', 'chatMessages', 'reviewCases']),
            'auditLogs' => $user->auditLogs()->take(15)->get(),
            'policies' => $user->policies()->latest('id')->take(10)->get(),
            'roles' => User::ROLES,
            'statuses' => User::STATUSES,
        ]);
    }

    /**
     * Update account role and status.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(User::ROLES)],
            'status' => ['required', Rule::in(User::STATUSES)],
            'phone' => ['nullable', 'string', 'max:30'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        // An administrator cannot demote themselves, to avoid accidentally
        // leaving the system without an administrator.
        if ($user->is($request->user()) && $data['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri.|You cannot change your own role.');
        }

        $before = $user->only(['name', 'role', 'status', 'phone', 'admin_note']);
        $user->update($data);
        $after = $user->only(['name', 'role', 'status', 'phone', 'admin_note']);

        $diff = $this->audit->diff($before, $after);

        $this->audit->log([
            'category' => AuditCategory::USER_MANAGEMENT,
            'action' => 'user.updated',
            'description' => "Memperbarui akun {$user->email}.",
            'subject' => $user,
            'before' => $diff['before'] ?: null,
            'after' => $diff['after'] ?: null,
        ]);

        return back()->with('status', 'Perubahan akun tersimpan.|Account changes saved.');
    }

    /**
     * Permanently delete a user account.
     *
     * Unlike deactivating an account (which only revokes sign-in access), this
     * action deletes the user row and all related data using the
     * `cascadeOnDelete` foreign key (profile, policies, conversations, etc.).
     * Tables using `nullOnDelete` (e.g. Audit Log) retain their history, so the
     * audit trail is not lost.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        // An administrator cannot delete their own account, which would leave
        // the active session without an owner.
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.|You cannot delete your own account.');
        }

        // Prevent the system from losing its last administrator.
        if ($user->isAdmin() && User::query()->where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Admin terakhir tidak dapat dihapus.|The last administrator cannot be deleted.');
        }

        $email = $user->email;
        $name = $user->name;

        // Record the audit BEFORE deletion so the subject's name/email are
        // retained (the audit's user_id column uses nullOnDelete, so the log
        // row remains even after the account is deleted).
        $this->audit->log([
            'category' => AuditCategory::USER_MANAGEMENT,
            'action' => 'user.deleted',
            'description' => "Menghapus akun {$email}.",
            'subject' => ['type' => User::class, 'id' => $user->getKey(), 'label' => $name],
            'before' => ['name' => $name, 'email' => $email, 'role' => $user->role, 'status' => $user->status],
        ]);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Akun {$email} berhasil dihapus.|Account {$email} was deleted.");
    }

    /**
     * Reactivate a deactivated account.
     */
    public function activate(Request $request, User $user): RedirectResponse
    {
        return $this->setStatus($request, $user, 'active', 'Mengaktifkan kembali akun.');
    }

    /**
     * Suspend an account (sign-in access is revoked, but data is retained).
     */
    public function suspend(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menangguhkan akun Anda sendiri.|You cannot suspend your own account.');
        }

        return $this->setStatus($request, $user, 'suspended', 'Menangguhkan akun.');
    }

    /**
     * Apply the status change and record it in the audit log.
     */
    private function setStatus(Request $request, User $user, string $status, string $description): RedirectResponse
    {
        $before = $user->status;
        $user->update(['status' => $status]);

        $this->audit->log([
            'category' => AuditCategory::USER_MANAGEMENT,
            'action' => "user.{$status}",
            'description' => "{$description} ({$user->email})",
            'subject' => $user,
            'before' => ['status' => $before],
            'after' => ['status' => $status],
        ]);

        return back()->with('status', 'Status akun diperbarui.|Account status updated.');
    }
}
