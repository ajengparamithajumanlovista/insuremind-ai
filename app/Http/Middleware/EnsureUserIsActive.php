<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menolak pengguna yang akunnya tidak lagi aktif.
 *
 * Menonaktifkan akun TIDAK menghapus data maupun riwayat aktivitasnya
 * (brief bagian 7D); pengguna hanya kehilangan akses masuk.
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
