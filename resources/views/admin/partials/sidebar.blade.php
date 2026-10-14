@php
    // Menu area admin. `gate` menentukan apakah item tampil untuk peran saat
    // ini; pemeriksaan izin sebenarnya tetap dilakukan di backend (middleware).
    $adminNav = [
        [
            'route' => 'admin.dashboard',
            'pattern' => 'admin.dashboard',
            'icon' => '▦',
            'id' => 'Ringkasan',
            'en' => 'Overview',
            'gate' => 'access-admin-dashboard',
        ],
        [
            'route' => 'admin.users.index',
            'pattern' => 'admin.users.*',
            'icon' => '☺',
            'id' => 'Manajemen Pengguna',
            'en' => 'User Management',
            'gate' => 'manage-users',
        ],
        [
            'route' => 'admin.products.index',
            'pattern' => 'admin.products.*',
            'icon' => '◫',
            'id' => 'Katalog Produk',
            'en' => 'Product Catalog',
            'gate' => 'manage-products',
        ],
        [
            'route' => 'admin.documents.index',
            'pattern' => 'admin.documents.*',
            'icon' => '⎙',
            'id' => 'Dokumen Polis',
            'en' => 'Policy Documents',
            'gate' => 'manage-products',
        ],
        [
            'route' => 'admin.review.index',
            'pattern' => 'admin.review.*',
            'icon' => '✦',
            'id' => 'AI Quality Review',
            'en' => 'AI Quality Review',
            'gate' => 'access-ai-review',
        ],
        [
            'route' => 'admin.audit.index',
            'pattern' => 'admin.audit.*',
            'icon' => '▤',
            'id' => 'Audit Log',
            'en' => 'Audit Log',
            'gate' => 'view-audit-log',
        ],
    ];

    $openReviewCount = \App\Models\AiReviewCase::open()->count();
@endphp

<aside class="sidebar" id="sidebar">
    <a href="{{ route('admin.dashboard') }}" class="brand">
        <span class="brand-mark">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 2.8 20 6v5.2c0 5.1-3.4 8.5-8 10-4.6-1.5-8-4.9-8-10V6l8-3.2Z"
                      stroke="currentColor" stroke-width="1.8"/>
                <path d="m8.5 12 2.2 2.2 4.8-5" stroke="currentColor" stroke-width="1.8"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <span class="brand-name">Insure<span>Mind</span><span class="brand-badge">Admin</span></span>
    </a>

    <p class="nav-label" data-id="OPERASIONAL" data-en="OPERATIONS">OPERASIONAL</p>

    <nav class="nav">
        @foreach ($adminNav as $item)
            @can($item['gate'])
                <a href="{{ route($item['route']) }}"
                   class="{{ request()->routeIs($item['pattern']) ? 'active' : '' }}">
                    <span class="nav-icon">{{ $item['icon'] }}</span>
                    <span data-id="{{ $item['id'] }}" data-en="{{ $item['en'] }}">{{ $item['id'] }}</span>
                    @if ($item['route'] === 'admin.review.index' && $openReviewCount > 0)
                        <span class="nav-count">{{ $openReviewCount }}</span>
                    @endif
                </a>
            @endcan
        @endforeach
    </nav>

    <p class="nav-label" data-id="AKUN PRIBADI" data-en="PERSONAL">AKUN PRIBADI</p>

    <nav class="nav">
        <a href="{{ route('dashboard') }}">
            <span class="nav-icon">◧</span>
            <span data-id="Dashboard Pengguna" data-en="User Dashboard">Dashboard Pengguna</span>
        </a>
    </nav>

    <div class="side-note">
        <strong data-id="Catatan operasional" data-en="Operational note">Catatan operasional</strong>
        <span data-id="Setiap tindakan penting pada halaman ini dicatat ke Audit Log."
              data-en="Every important action on these pages is recorded in the Audit Log.">Setiap tindakan penting pada halaman ini dicatat ke Audit Log.</span>
    </div>

    <div class="profile">
        <span class="avatar">{{ strtoupper(mb_substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
        <span>
            <b>{{ auth()->user()->name ?? 'Guest' }}</b>
            <small>{{ auth()->user()?->roleLabel()['id'] ?? '' }}</small>
        </span>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-button" data-id="Keluar" data-en="Sign out">Keluar</button>
    </form>
</aside>
