<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use App\Support\AuditCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class LoginController extends Controller
{
    /**
     * Where to redirect users after authentication.
     */
    protected string $redirectTo = '/dashboard';

    public function __construct(
        private readonly AuditLogger $audit,
    ) {}

    /**
     * Show the login / sign-up screen.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect($this->homeFor(Auth::user()));
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * The account's role (admin vs user) is detected automatically from the
     * database, so a single login form serves both audiences.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('The provided credentials do not match our records.'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->homeFor($request->user()));
    }

    /**
     * Determine the destination page after sign-in based on the account role.
     *
     * Admins and reviewers are directed to the admin area, while regular users
     * are directed to their personal dashboard.
     */
    private function homeFor(?User $user): string
    {
        return match ($user?->role) {
            'admin' => '/admin',
            'reviewer' => '/admin/tinjauan',
            default => $this->redirectTo,
        };
    }

    /**
     * Register a new user account and sign them in.
     *
     * Both a successful and a failed registration are recorded in the Audit
     * Log (`auth.register`). Only non-sensitive metadata is stored: the
     * password and its confirmation are never included.
     */
    public function register(Request $request): RedirectResponse
    {
        $auditPayload = [
            'category' => AuditCategory::AUTHENTICATION,
            'action' => 'auth.register',
            'description' => 'Pengguna baru mendaftar',
            'actorRole' => 'user',
        ];

        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->audit->log($auditPayload + [
                'actor' => null,
                'result' => 'failed',
                'reason' => $this->registrationFailureReason($e),
                'after' => [
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                ],
            ]);

            throw $e;
        }

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'user',
            ]);
        } catch (Throwable $e) {
            $this->audit->log($auditPayload + [
                'actor' => null,
                'result' => 'failed',
                'reason' => 'Akun gagal dibuat.',
                'after' => ['name' => $data['name'], 'email' => $data['email']],
            ]);

            throw $e;
        }

        $this->audit->log($auditPayload + [
            'actor' => $user,
            'result' => 'success',
            'subject' => $user,
            'after' => ['email' => $user->email, 'role' => $user->role],
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect($this->redirectTo);
    }

    /**
     * Short, non-sensitive reason for a rejected registration.
     *
     * Only the names of the fields that failed validation are recorded, never
     * the submitted values or the raw exception message.
     */
    private function registrationFailureReason(ValidationException $e): string
    {
        $fields = array_keys($e->errors());

        return $fields === []
            ? 'Validasi pendaftaran gagal.'
            : 'Validasi pendaftaran gagal: '.implode(', ', $fields).'.';
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
