<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi akses pada pengguna yang berperan reviewer atau admin.
 *
 * Reviewer hanya menangani kasus AI Quality Review yang ditugaskan; akses
 * ke manajemen produk, akun, dan audit log tetap eksklusif admin.
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
