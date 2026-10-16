<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts access to the admin area on the backend.
 *
 * Permission checks MUST be performed on the server; hiding buttons on the
 * frontend is not enough to secure an action (brief section 7F).
 *
 * Users who are not signed in are redirected to the login page, while signed-in
 * users without permission receive a 403.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        abort_unless($user->isAdmin(), 403, 'Anda tidak memiliki hak akses untuk halaman ini.');

        return $next($request);
    }
}
