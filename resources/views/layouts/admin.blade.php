<!DOCTYPE html>
<html lang="id" id="htmlRoot">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f8fc">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>InsureMind AI Admin | @yield('title', 'Ringkasan')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @include('admin.partials.styles')
        @yield('styles')

        /* ── Version badge (bottom right) ───────────────────── */
        .version-badge {
            position: fixed;
            right: 14px;
            bottom: 12px;
            z-index: 60;
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: .2px;
            color: #98a3b6;
            background: #ffffffcc;
            border: 1px solid #e7ebf2;
            border-radius: 999px;
            padding: 3px 10px;
            backdrop-filter: blur(4px);
            pointer-events: none;
            user-select: none;
        }
    </style>
</head>

<body>
    @include('admin.partials.sidebar')

    <main class="main">
        @include('admin.partials.topbar')

        @include('admin.partials.flash')

        @yield('content')
    </main>

    <span class="version-badge" aria-hidden="true">Beta v1.7</span>

    <script>
        // ── Sidebar (mobile) ──────────────────────────────────────
        document.getElementById('menuButton')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('open');
        });

        // ── Language switcher (same behavior as the user layout) ──────
        let currentLang = 'id';
        const t = key => key;

        function applyLang(lang) {
            currentLang = lang;

            document.querySelectorAll('[data-id], [data-en]').forEach(el => {
                const text = lang === 'id' ? el.getAttribute('data-id') : el.getAttribute('data-en');
                if (text !== null && text !== '') {
                    el.innerHTML = text;
                }
            });

            document.querySelectorAll('[data-id-aria], [data-en-aria]').forEach(el => {
                const label = lang === 'id' ? el.getAttribute('data-id-aria') : el.getAttribute('data-en-aria');
                if (label !== null) el.setAttribute('aria-label', label);
            });

            document.querySelectorAll('[data-id-title], [data-en-title]').forEach(el => {
                const label = lang === 'id' ? el.getAttribute('data-id-title') : el.getAttribute('data-en-title');
                if (label !== null) el.setAttribute('title', label);
            });

            document.querySelectorAll('[data-id-placeholder], [data-en-placeholder]').forEach(el => {
                const text = lang === 'id' ? el.getAttribute('data-id-placeholder') : el.getAttribute('data-en-placeholder');
                if (text !== null) el.setAttribute('placeholder', text);
            });

            document.getElementById('htmlRoot').setAttribute('lang', lang);
            document.getElementById('btnLangID')?.classList.toggle('active', lang === 'id');
            document.getElementById('btnLangEN')?.classList.toggle('active', lang === 'en');

            document.dispatchEvent(new CustomEvent('im:lang', { detail: { lang } }));
            localStorage.setItem('im_lang', lang);
        }

        window.imLang = () => currentLang;
        window.imString = t;

        (function () {
            applyLang(localStorage.getItem('im_lang') || 'id');
        })();

        document.getElementById('btnLangID')?.addEventListener('click', () => applyLang('id'));
        document.getElementById('btnLangEN')?.addEventListener('click', () => applyLang('en'));
    </script>

    @stack('scripts')
</body>

</html>
