<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rejects users whose accounts are no longer active.
 *
 * Deactivating an account does NOT delete its data or activity history
 * (brief section 7D); the user only loses sign-in access.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && ! $user->isActive()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Akun Anda dinonaktifkan. Hubungi administrator InsureMind AI.',
                ]);
        }

        return $next($request);
    }
}
