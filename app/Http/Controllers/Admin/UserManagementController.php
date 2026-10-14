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
 * Manajemen Pengguna (khusus admin).
 *
 * Menonaktifkan akun TIDAK menghapus data: `status` diubah sehingga
 * pengguna kehilangan akses masuk, tetapi riwayat aktivitasnya tetap utuh.
 * Setiap perubahan peran/status dicatat ke Audit Log.
 */
class UserManagementController extends Controller
{
    public function __construct(
        private readonly AuditLogger $audit,
    ) {}

    /**
     * Daftar pengguna dengan pencarian dan filter.
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
     * Detail akun beserta jejak aktivitas terkait.
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
     * Perbarui peran dan status akun.
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

        // Administrator tidak boleh menurunkan perannya sendiri agar tidak
        // ada sistem yang kehilangan admin terakhirnya secara tidak sengaja.
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
     * Hapus akun pengguna secara permanen.
     *
     * Berbeda dari menonaktifkan akun (yang hanya mencabut akses masuk),
     * tindakan ini menghapus baris user beserta seluruh data terkait yang
     * memakai foreign key `cascadeOnDelete` (profil, polis, percakapan, dsb).
     * Tabel yang memakai `nullOnDelete` (mis. Audit Log) tetap menyimpan
     * riwayatnya, sehingga jejak audit tidak hilang.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Administrator tidak boleh menghapus akunnya sendiri agar sesi yang
        // sedang aktif tidak langsung kehilangan pemiliknya.
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.|You cannot delete your own account.');
        }

        // Cegah sistem kehilangan admin terakhirnya.
        if ($user->isAdmin() && User::query()->where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Admin terakhir tidak dapat dihapus.|The last administrator cannot be deleted.');
        }

        $email = $user->email;
        $name = $user->name;

        // Catat audit SEBELUM penghapusan agar name/email subjek tetap tersimpan
        // (kolom user_id pada kolom audit memakai nullOnDelete sehingga baris
        // log tetap ada walau akunnya sudah dihapus).
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
     * Aktifkan kembali akun yang dinonaktifkan.
     */
    public function activate(Request $request, User $user): RedirectResponse
    {
        return $this->setStatus($request, $user, 'active', 'Mengaktifkan kembali akun.');
    }

    /**
     * Tangguhkan akun (akses masuk dicabut, data tetap tersimpan).
     */
    public function suspend(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menangguhkan akun Anda sendiri.|You cannot suspend your own account.');
        }

        return $this->setStatus($request, $user, 'suspended', 'Menangguhkan akun.');
    }

    /**
     * Terapkan perubahan status dengan pencatatan audit.
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
