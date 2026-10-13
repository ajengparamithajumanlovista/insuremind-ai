<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi akses area admin pada backend.
 *
 * Pemeriksaan izin WAJIB dilakukan di server; menyembunyikan tombol pada
 * frontend tidak cukup untuk mengamankan suatu tindakan (brief bagian 7F).
 *
 * Pengguna yang belum masuk diarahkan ke halaman login, sedangkan pengguna
 * yang sudah masuk tetapi tidak berwenang menerima 403.
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
