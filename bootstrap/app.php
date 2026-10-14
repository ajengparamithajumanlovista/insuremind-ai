<?php

use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsReviewer;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias otorisasi. Pemeriksaan izin tetap dilakukan di backend.
        $middleware->alias([
            'active' => EnsureUserIsActive::class,
            'admin' => EnsureUserIsAdmin::class,
            'reviewer' => EnsureUserIsReviewer::class,
        ]);

        // Railway (dan platform PaaS sejenis) mengakhiri TLS di reverse proxy
        // miliknya, lalu meneruskan request sebagai HTTP biasa. Tanpa
        // konfigurasi ini Laravel mengira situs berjalan di atas HTTP, sehingga
        // URL yang dihasilkan memakai skema http:// dan cookie sesi "secure"
        // tidak pernah dikirim. Akibatnya permintaan POST (kirim pesan,
        // simpan akun, dsb.) gagal, sesi tampak "mati", atau browser
        // menampilkan "resend form / lanjut kirim atau tidak".
        // Mempercayai proxy membuat Laravel mengenali HTTPS melalui header
        // X-Forwarded-* dan sesi tetap konsisten.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
