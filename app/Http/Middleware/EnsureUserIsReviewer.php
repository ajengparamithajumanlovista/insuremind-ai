<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts access to users with the reviewer or admin role.
 *
 * Reviewers can only handle assigned AI Quality Review cases; access to
 * product management, accounts, and the audit log remains exclusive to admins.
 */
class EnsureUserIsReviewer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        abort_unless($user->isReviewer(), 403, 'Anda tidak memiliki hak akses untuk halaman ini.');

        return $next($request);
    }
}
