<!DOCTYPE html>
<html lang="id" id="htmlRoot">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f8fc">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>InsureMind AI | @yield('title', 'Dashboard')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Design tokens (selaras dengan landing page) ───────── */
        :root {
            --ink: #17243b;
            --muted: #77839a;
            --line: #e7ebf2;
            --blue: #315ee8;
            --pale: #f6f8fc;
            --green: #17a673;
            --amber: #d98b12;
            --rose: #d8446a;
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            background: var(--pale);
            color: var(--ink);
            font: 14px/1.5 Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        button, a, input, select, textarea { font: inherit; }

        a { color: inherit; text-decoration: none; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: none; }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .reveal { animation: fadeInUp .5s ease both; }
        .reveal-1 { animation-delay: .05s; }
        .reveal-2 { animation-delay: .1s; }
        .reveal-3 { animation-delay: .15s; }

        /* ── Sidebar ─────────────────────────────────────────── */
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 246px;
            background: #fff;
            border-right: 1px solid var(--line);
            padding: 25px 16px;
            display: flex;
            flex-direction: column;
            z-index: 20;
            overflow-y: auto;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 2px 11px 31px;
        }

        .brand-mark {
            width: 34px;
            height: 34px;
            border-radius: 11px;
            background: linear-gradient(145deg, #4778f5, #244bd4);
            display: grid;
            place-items: center;
            color: white;
            box-shadow: 0 6px 14px #315ee833;
        }

        .brand-name { font-size: 17px; font-weight: 750; letter-spacing: -.5px; }
        .brand-name span { color: var(--blue); }

        .nav-label {
            font-size: 10px;
            color: #a0a9b8;
            font-weight: 750;
            letter-spacing: 1.2px;
            padding: 0 12px 10px;
        }

        .nav { display: grid; gap: 5px; }

        .nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 10px;
            color: #68758b;
            font-size: 13px;
            font-weight: 550;
            transition: background .18s ease, color .18s ease;
        }

        .nav a:hover, .nav a.active { background: #eef3ff; color: #2855dd; }

        .nav-icon { width: 18px; text-align: center; font-size: 16px; }

        .side-note {
            margin-top: auto;
            background: #f5f7fb;
            border: 1px solid #edf0f5;
            border-radius: 12px;
            padding: 13px;
            color: #738097;
            font-size: 11px;
        }

        .side-note strong { display: block; color: #35435a; font-size: 12px; margin-bottom: 4px; }

        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 17px 8px 0;
            margin-top: 15px;
            border-top: 1px solid var(--line);
        }

        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: #e9edff;
            color: #425ac2;
            font-weight: 700;
            flex: none;
        }

        .profile b { display: block; font-size: 12px; }
        .profile small { font-size: 10px; color: var(--muted); }

        .logout-button {
            width: 100%;
            border: 1px solid var(--line);
            background: #fff;
            color: #627087;
            font-size: 12px;
            font-weight: 600;
            padding: 9px 12px;
            border-radius: 10px;
            cursor: pointer;
            transition: all .18s ease;
            margin-top: 10px;
        }

        .logout-button:hover { border-color: #cdd6e6; color: #c03246; background: #fdf0f2; }

        /* ── Main layout ─────────────────────────────────────── */
        .main {
            margin-left: 246px;
            padding: 0 38px 48px;
            max-width: 1600px;
        }

        .topbar {
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--line);
            margin-bottom: 29px;
            gap: 12px;
        }

        .crumb { font-size: 12px; color: var(--muted); }
        .crumb b { color: #344159; }

        .top-actions { display: flex; align-items: center; gap: 12px; }

        .lang {
            display: flex;
            align-items: center;
            gap: 6px;
            border: 1px solid var(--line);
            border-radius: 9px;
            padding: 5px;
            background: #fff;
        }

        .lang button {
            border: 0;
            background: transparent;
            border-radius: 6px;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 650;
            color: #8490a4;
            cursor: pointer;
        }

        .lang button.active { background: #eef3ff; color: #2855dd; }

        .menu-button {
            width: 36px;
            height: 36px;
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 10px;
            color: #627087;
            display: none;
            place-items: center;
            cursor: pointer;
        }

        .page-title { font-size: 25px; letter-spacing: -.7px; margin: 0 0 5px; }
        .page-subtitle { color: var(--muted); margin: 0 0 23px; font-size: 13px; }

        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            margin: 0 0 12px;
            gap: 12px;
        }

        .section-heading h2 { margin: 0; font-size: 15px; letter-spacing: -.2px; }
        .section-heading p { margin: 3px 0 0; font-size: 11px; color: var(--muted); }
        .subtle-link { font-size: 11px; color: #71809a; }

        /* ── Flash messages ──────────────────────────────────── */
        .flash {
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 12.5px;
            margin-bottom: 18px;
            border: 1px solid;
            animation: fadeIn .3s ease both;
        }

        .flash-success { background: #eefaf4; border-color: #c7eedd; color: #14785a; }
        .flash-error { background: #fdf1f4; border-color: #f5cfd9; color: #b23350; }

        /* ── Cards ───────────────────────────────────────────── */
        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 15px;
            padding: 21px;
        }

        .grid { display: grid; gap: 16px; }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

        .stat {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 17px 18px;
        }

        .stat-label { font-size: 10.5px; color: #8b96a9; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; }
        .stat-value { font-size: 21px; font-weight: 750; letter-spacing: -.6px; margin-top: 6px; }
        .stat-note { font-size: 11px; color: var(--muted); margin-top: 4px; }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 10.5px;
            font-weight: 700;
        }

        .pill-green { background: #eefaf4; color: #14785a; }
        .pill-amber { background: #fdf6e8; color: #a76a08; }
        .pill-rose { background: #fdf1f4; color: #b23350; }
        .pill-blue { background: #eef3ff; color: #2a53c4; }
        .pill-grey { background: #f2f4f8; color: #6b7789; }

        /* ── Buttons ─────────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 10px;
            padding: 10px 17px;
            font-size: 12.5px;
            font-weight: 650;
            border: 1px solid transparent;
            cursor: pointer;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .btn-primary {
            background: linear-gradient(145deg, #4778f5, #2a4fd6);
            color: #fff;
            box-shadow: 0 8px 18px #315ee82e;
        }

        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 22px #315ee840; }

        .btn-ghost { background: #fff; border-color: var(--line); color: #4d5b73; }
        .btn-ghost:hover { border-color: #cdd6e6; }
        .btn-danger { background: #fdf1f4; color: #b23350; border-color: #f5cfd9; }
        .btn-danger:hover { background: #fbe4ea; }
        .btn-sm { padding: 7px 12px; font-size: 11.5px; }

        /* ── Forms ───────────────────────────────────────────── */
        .field { display: grid; gap: 6px; }
        .field label { font-size: 11.5px; font-weight: 650; color: #4f5c72; }

        .field input, .field select, .field textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            background: #fff;
            color: var(--ink);
            font-size: 13px;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        .field input:focus, .field select:focus, .field textarea:focus {
            outline: none;
            border-color: #7a9bf5;
            box-shadow: 0 0 0 3px #315ee816;
        }

        .field .hint { font-size: 10.5px; color: var(--muted); }
        .field .error { font-size: 11px; color: #b23350; }

        .check {
            display: flex;
            align-items: center;
            gap: 9px;
            font-size: 12.5px;
            color: #4f5c72;
        }

        .check input { width: 16px; height: 16px; accent-color: var(--blue); }

        /* ── Tables ──────────────────────────────────────────── */
        .table-wrap { overflow-x: auto; border: 1px solid var(--line); border-radius: 14px; background: #fff; }

        table.data { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        table.data th, table.data td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--line); vertical-align: top; }
        table.data th { background: #f7f9fd; font-size: 10.5px; color: #79859a; text-transform: uppercase; letter-spacing: .6px; font-weight: 750; }
        table.data tr:last-child td { border-bottom: 0; }
        table.data td.num { text-align: right; font-variant-numeric: tabular-nums; }
        table.data tr.best { background: #f4fbf8; }

        /* ── Progress / meters ───────────────────────────────── */
        .meter { height: 8px; border-radius: 999px; background: #eef1f7; overflow: hidden; }
        .meter > span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #4778f5, #315ee8); transition: width .6s ease; }
        .meter.green > span { background: linear-gradient(90deg, #34c48f, #17a673); }
        .meter.amber > span { background: linear-gradient(90deg, #f0b23f, #d98b12); }
        .meter.rose > span { background: linear-gradient(90deg, #f0718f, #d8446a); }

        /* ── Empty state ─────────────────────────────────────── */
        .empty {
            border: 1px dashed #d9e0ec;
            border-radius: 14px;
            background: #fbfcfe;
            padding: 30px 22px;
            text-align: center;
            color: var(--muted);
            font-size: 12.5px;
        }

        .empty strong { display: block; color: #3a475c; font-size: 13.5px; margin-bottom: 5px; }

        /* ── Responsive ──────────────────────────────────────── */
        @media (max-width: 1080px) {
            .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 860px) {
            .sidebar { transform: translateX(-100%); transition: transform .25s ease; }
            .sidebar.open { transform: none; }
            .main { margin-left: 0; padding: 0 18px 40px; }
            .menu-button { display: grid; }
            .grid-2, .grid-3, .grid-4 { grid-template-columns: minmax(0, 1fr); }
        }

        @yield('styles')
    </style>
</head>

<body>
    @php
        $navItems = [
            ['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => '▦', 'id' => 'Dashboard', 'en' => 'Dashboard'],
            ['route' => 'profile.edit', 'pattern' => 'profile.*', 'icon' => '◎', 'id' => 'Profil Keuangan', 'en' => 'Financial Profile'],
            ['route' => 'risk.index', 'pattern' => 'risk.*', 'icon' => '⚠', 'id' => 'Analisis Risiko', 'en' => 'Risk Assessment'],
            ['route' => 'simulator.index', 'pattern' => 'simulator.*', 'icon' => '◔', 'id' => 'Simulator Risiko', 'en' => 'Risk Simulator'],
            ['route' => 'comparison.index', 'pattern' => 'comparison.*', 'icon' => '⇄', 'id' => 'Perbandingan', 'en' => 'Comparison'],
            ['route' => 'categories.show', 'pattern' => 'categories.*', 'icon' => '◫', 'id' => 'Kategori Produk', 'en' => 'Categories', 'params' => ['category' => 'health']],
            ['route' => 'policies.index', 'pattern' => 'policies.*', 'icon' => '⌸', 'id' => 'Polis & Pengingat', 'en' => 'Policies & Reminders'],
            ['route' => 'chat.index', 'pattern' => 'chat.*', 'icon' => '✆', 'id' => 'Asisten AI', 'en' => 'AI Assistant'],
        ];
    @endphp

    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="brand">
            <span class="brand-mark">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 2.8 20 6v5.2c0 5.1-3.4 8.5-8 10-4.6-1.5-8-4.9-8-10V6l8-3.2Z"
                          stroke="currentColor" stroke-width="1.8"/>
                    <path d="m8.5 12 2.2 2.2 4.8-5" stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="brand-name">Insure<span>Mind</span></span>
        </a>

        <p class="nav-label" data-id="MENU UTAMA" data-en="MAIN MENU">MENU UTAMA</p>

        <nav class="nav">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                   class="{{ request()->routeIs($item['pattern']) ? 'active' : '' }}">
                    <span class="nav-icon">{{ $item['icon'] }}</span>
                    <span data-id="{{ $item['id'] }}" data-en="{{ $item['en'] }}">{{ $item['id'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="side-note">
            <strong data-id="Catatan penting" data-en="Important note">Catatan penting</strong>
            <span data-id="Hasil analisis bersifat edukatif dan bukan nasihat keuangan resmi."
                  data-en="Analysis results are educational and not official financial advice.">Hasil analisis bersifat edukatif dan bukan nasihat keuangan resmi.</span>
        </div>

        <div class="profile">
            <span class="avatar">{{ strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
            <span>
                <b>{{ auth()->user()->name ?? 'Guest' }}</b>
                <small>{{ auth()->user()->email ?? '' }}</small>
            </span>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-button" data-id="Keluar" data-en="Sign out">Keluar</button>
        </form>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="crumb">
                <span data-id="InsureMind" data-en="InsureMind">InsureMind</span> /
                <b>@yield('title', 'Dashboard')</b>
            </div>

            <div class="top-actions">
                <button type="button" class="menu-button" id="menuButton" aria-label="Menu">☰</button>

                <div class="lang">
                    <button type="button" id="btnLangID" class="active"
                            data-id-title="Bahasa Indonesia" data-en-title="Indonesian">ID</button>
                    <button type="button" id="btnLangEN" data-id-title="English" data-en-title="English">EN</button>
                </div>
            </div>
        </header>

        @if (session('status'))
            <div class="flash flash-success">{{ explode('|', session('status'))[0] }}</div>
        @endif

        @if (session('error'))
            <div class="flash flash-error">{{ explode('|', session('error'))[0] }}</div>
        @endif

        @if ($errors->any())
            <div class="flash flash-error">
                <b data-id="Periksa kembali isian Anda:" data-en="Please review your input:">Periksa kembali isian Anda:</b>
                <ul style="margin:6px 0 0;padding-left:18px">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        // ── Sidebar (mobile) ──────────────────────────────────────
        document.getElementById('menuButton')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('open');
        });

        // ── Language switcher (shared behaviour with landing page) ─
        const IM_STRINGS = {
            id: {
                thinking: 'Asisten sedang menyusun jawaban…',
                error: 'Maaf, terjadi kendala teknis. Silakan coba lagi.',
                sending: 'Mengirim…',
                copied: 'Tersalin',
            },
            en: {
                thinking: 'The assistant is preparing a reply…',
                error: 'Sorry, a technical issue occurred. Please try again.',
                sending: 'Sending…',
                copied: 'Copied',
            },
        };

        let currentLang = 'id';
        const t = key => (IM_STRINGS[currentLang] && IM_STRINGS[currentLang][key]) || key;

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
