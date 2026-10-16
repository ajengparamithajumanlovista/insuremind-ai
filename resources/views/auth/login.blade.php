<!DOCTYPE html>
<html lang="id" id="htmlRoot">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f8fc">
    <title>Masuk | InsureMind AI</title>
    <meta name="description"
        content="Masuk atau daftar ke InsureMind AI — copilot asuransi berbasis AI untuk memahami polis dan mempersiapkan klaim.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Design tokens (shared with landing page) ─────────── */
        :root {
            --ink: #17243b;
            --muted: #77839a;
            --soft: #8a95a8;
            --line: #e7ebf2;
            --bg: #f6f8fc;
            --white: #ffffff;
            --blue: #315ee8;
            --blue-dark: #244bd4;
            --navy-1: #172e53;
            --navy-2: #24477d;
            --navy-3: #3561a3;
            --radius: 18px;
            --shadow-sm: 0 4px 16px rgba(23, 46, 83, .06);
            --shadow-md: 0 18px 48px rgba(23, 46, 83, .12);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
            line-height: 1.55;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .wrap {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
        }

        /* ── Brand panel ──────────────────────────────────────── */
        .brand-panel {
            position: relative;
            overflow: hidden;
            padding: 56px 60px;
            color: #fff;
            background:
                radial-gradient(circle at 15% 85%, #37c5bd28, transparent 32%),
                radial-gradient(circle at 92% 12%, #8e79ff38, transparent 34%),
                linear-gradient(145deg, #172e53 0%, #293f7e 54%, #176f79 120%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            opacity: .12;
            background-image: radial-gradient(#fff 1px, transparent 1px);
            background-size: 24px 24px;
            mask-image: linear-gradient(135deg, #000, transparent 68%);
            pointer-events: none;
        }

        .brand-panel::after {
            content: "";
            position: absolute;
            width: 620px;
            height: 620px;
            right: -260px;
            top: -180px;
            border-radius: 50%;
            background: radial-gradient(circle at center, rgba(72, 120, 245, .3), transparent 62%);
            pointer-events: none;
        }

        .brand-top {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            letter-spacing: -.01em;
            font-size: 18px;
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(145deg, #4778f5, var(--blue-dark));
            box-shadow: 0 10px 24px rgba(36, 75, 212, .45);
        }

        .brand-copy {
            position: relative;
            z-index: 1;
            max-width: 460px;
            padding: 38px 0;
        }

        .brand-copy .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 600;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .22);
            margin-bottom: 22px;
            backdrop-filter: blur(8px);
        }

        .brand-copy h1 {
            font-size: clamp(34px, 4vw, 50px);
            line-height: 1.16;
            letter-spacing: -.04em;
            margin: 0 0 20px;
            font-weight: 700;
            text-wrap: balance;
        }

        .brand-copy p {
            margin: 0;
            font-size: 16px;
            color: rgba(255, 255, 255, .78);
            line-height: 1.75;
            max-width: 430px;
        }

        .brand-points {
            position: relative;
            z-index: 1;
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 14px;
        }

        .brand-points li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14.5px;
            color: rgba(255, 255, 255, .9);
        }

        .brand-points li span:last-child { line-height: 1.55; }

        .brand-points .tick {
            flex: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .28);
            font-size: 12px;
            margin-top: 1px;
        }

        /* ── Form panel ───────────────────────────────────────── */
        .form-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 82px 40px 48px;
            background:
                radial-gradient(ellipse at 100% 0%, #e9edff 0, transparent 32%),
                radial-gradient(ellipse at 0% 100%, #eaf8f6 0, transparent 30%),
                #f7f9fc;
        }

        .lang {
            display: inline-flex;
            gap: 3px;
            padding: 4px;
            border: 1px solid #e4e9f2;
            border-radius: 12px;
            background: #ffffffd9;
            box-shadow: 0 5px 18px #172e530b;
        }

        .lang button {
            min-width: 38px;
            border: 0;
            border-radius: 8px;
            padding: 7px 9px;
            background: transparent;
            color: #748198;
            font: inherit;
            font-size: 11px;
            font-weight: 750;
            cursor: pointer;
            transition: color .18s ease, background .18s ease, box-shadow .18s ease;
        }

        .lang button.active {
            color: #315ee8;
            background: #eef3ff;
            box-shadow: inset 0 0 0 1px #dfe7fb;
        }

        .lang button:focus-visible { outline: 3px solid #8b63d966; outline-offset: 2px; }

        .form-lang { position: absolute; top: 26px; right: 32px; }

        .card {
            width: 100%;
            max-width: 430px;
            background: var(--white);
            border: 1px solid #e6ebf3;
            border-radius: 20px;
            box-shadow: 0 24px 65px rgba(23, 46, 83, .12), 0 3px 12px rgba(23, 46, 83, .04);
            padding: 32px 34px 30px;
        }

        .card h2 {
            margin: 0 0 6px;
            font-size: 25px;
            letter-spacing: -.02em;
        }

        .card .sub {
            margin: 0 0 24px;
            color: var(--muted);
            font-size: 14.5px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: 20px;
            transition: color .18s ease;
        }

        .back-link:hover {
            color: var(--blue);
        }

        /* Tabs */
        .tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: #f3f5fa;
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 24px;
        }

        .tab {
            border: 0;
            background: transparent;
            padding: 10px 12px;
            border-radius: 9px;
            font: inherit;
            font-size: 14px;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
            transition: all .18s ease;
        }

        .tab.active {
            background: var(--white);
            color: var(--ink);
            box-shadow: var(--shadow-sm);
        }

        /* Social buttons */
        .socials {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 11px 14px;
            border: 1px solid var(--line);
            border-radius: 11px;
            background: var(--white);
            font: inherit;
            font-size: 14px;
            font-weight: 600;
            color: var(--ink);
            cursor: pointer;
            transition: all .18s ease;
        }

        .social-btn:hover {
            border-color: #cdd6e6;
            background: #fafbfe;
            transform: translateY(-1px);
        }

        .social-btn svg {
            width: 18px;
            height: 18px;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            color: var(--soft);
            font-size: 12.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin: 0 0 20px;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--line);
        }

        /* Fields */
        .field {
            margin-bottom: 16px;
        }

        .field label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .field input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid var(--line);
            border-radius: 11px;
            font: inherit;
            font-size: 14.5px;
            color: var(--ink);
            background: #f9fafd;
            transition: all .18s ease;
        }

        .field input:focus {
            outline: none;
            border-color: var(--blue);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(49, 94, 232, .12);
        }

        .field .err {
            color: #d6455b;
            font-size: 12.5px;
            margin-top: 6px;
        }

        .row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 4px 0 22px;
            font-size: 13.5px;
        }

        .check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            cursor: pointer;
            user-select: none;
        }

        .check input {
            width: 16px;
            height: 16px;
            accent-color: var(--blue);
        }

        .link {
            color: var(--blue);
            font-weight: 600;
        }

        .btn-primary {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 13px 18px;
            font: inherit;
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            background: linear-gradient(110deg, #416ff0, #6255d8 62%, #248e9a);
            box-shadow: 0 12px 26px rgba(36, 75, 212, .32);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 32px rgba(36, 75, 212, .4);
        }

        .foot-note {
            text-align: center;
            font-size: 13.5px;
            color: var(--muted);
            margin: 20px 0 0;
        }

        .alert {
            border-radius: 11px;
            padding: 11px 14px;
            font-size: 13.5px;
            margin-bottom: 18px;
        }

        .alert-error {
            background: #fdf0f2;
            border: 1px solid #f7ccd4;
            color: #c03246;
        }

        .alert-success {
            background: #edf8f1;
            border: 1px solid #c5e9d3;
            color: #1f7a45;
        }

        .hidden {
            display: none !important;
        }

        .brand-points .tick {
            flex: none;
            width: 24px;
            height: 24px;
            color: #c8fff0;
            background: #ffffff18;
            border-color: #ffffff35;
        }

        @media (max-width: 980px) {
            .wrap {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                min-height: 360px;
                padding: 32px;
                gap: 12px;
            }

            .brand-copy {
                padding: 30px 0 22px;
            }

            .brand-copy h1 {
                font-size: 36px;
            }

            .brand-points {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 12px;
            }

            .brand-points li { font-size: 12px; }
            .form-panel { padding-top: 82px; }
        }

        @media (max-width: 520px) {
            .brand-panel { min-height: 0; padding: 24px 20px 28px; }
            .brand-copy { padding: 30px 0 6px; }
            .brand-copy .eyebrow { margin-bottom: 15px; font-size: 11px; }
            .brand-copy h1 { font-size: 31px; }
            .brand-copy p { font-size: 14px; }
            .brand-points { display: none; }
            .form-panel {
                align-items: flex-start;
                padding: 70px 16px 32px;
            }

            .card {
                padding: 24px 20px;
                border-radius: 17px;
            }

            .socials {
                grid-template-columns: 1fr;
            }

            .form-lang { top: 18px; right: 16px; }
            .back-link { margin-bottom: 16px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }

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
            border: 1px solid rgba(23, 36, 59, .1);
            border-radius: 999px;
            padding: 3px 10px;
            backdrop-filter: blur(4px);
            pointer-events: none;
            user-select: none;
        }
    </style>
</head>

<body>
    <div class="wrap">
        {{-- ── Left: brand panel ──────────────────────────────── --}}
        <aside class="brand-panel">
            <div class="brand-top">
                <a href="{{ route('landing') }}" class="brand-mark" data-id-aria="Beranda InsureMind AI" data-en-aria="InsureMind AI home">
                    <span class="brand-logo">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 2.5 4.5 5.6v6.1c0 4.6 3.1 8.5 7.5 9.8 4.4-1.3 7.5-5.2 7.5-9.8V5.6L12 2.5Z"
                                stroke="#fff" stroke-width="1.6" fill="rgba(255,255,255,.14)" />
                            <path d="m8.8 11.8 2.2 2.2 4.2-4.4" stroke="#fff" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    InsureMind AI
                </a>
            </div>

            <div class="brand-copy">
                <span class="eyebrow" data-id="Kopilot Asuransi Berbasis AI" data-en="AI-Powered Insurance Copilot">Kopilot Asuransi Berbasis AI</span>
                <h1 data-id="Pahami Polis Anda.<br>Siapkan Klaim Anda." data-en="Understand Your Policy.<br>Prepare Your Claim.">Pahami Polis Anda.<br>Siapkan Klaim Anda.</h1>
                <p data-id="Masuk untuk menganalisis polis, menilai kesiapan klaim, dan mengambil keputusan yang lebih terinformasi."
                   data-en="Sign in to analyze your policy, assess claim readiness, and make better-informed decisions.">Masuk untuk menganalisis polis, menilai kesiapan klaim, dan mengambil keputusan yang lebih terinformasi.</p>
            </div>

            <ul class="brand-points">
                <li><span class="tick">✓</span><span data-id="Penjelasan polis yang dapat ditelusuri dan transparan." data-en="Traceable, transparent policy explanations.">Penjelasan polis yang dapat ditelusuri dan transparan.</span></li>
                <li><span class="tick">✓</span><span data-id="Skor kesiapan klaim dengan faktor yang jelas." data-en="Claim readiness scores with clear factors.">Skor kesiapan klaim dengan faktor yang jelas.</span></li>
                <li><span class="tick">✓</span><span data-id="Selalu dalam pengawasan manusia." data-en="Always under human supervision.">Selalu dalam pengawasan manusia.</span></li>
            </ul>
        </aside>

        {{-- ── Right: auth form ───────────────────────────────── --}}
        <main class="form-panel">
            <div class="lang form-lang" role="group" aria-label="Pilih bahasa"
                 data-id-aria="Pilih bahasa" data-en-aria="Choose language">
                <button type="button" id="btnLangID" class="active" aria-pressed="true"
                        data-id-title="Bahasa Indonesia" data-en-title="Indonesian">ID</button>
                <button type="button" id="btnLangEN" aria-pressed="false"
                        data-id-title="Bahasa Inggris" data-en-title="English">EN</button>
            </div>
            <div class="card">
                <a href="{{ route('landing') }}" class="back-link" data-id-aria="Kembali ke beranda" data-en-aria="Back to home">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span data-id="Kembali ke beranda" data-en="Back to home">Kembali ke beranda</span>
                </a>

                <h2 id="cardTitle" data-id-login="Selamat datang kembali" data-en-login="Welcome back"
                    data-id-signup="Buat akun Anda" data-en-signup="Create your account">Selamat datang kembali</h2>
                <p class="sub" id="cardSub" data-id-login="Masuk ke akun InsureMind Anda."
                   data-en-login="Sign in to your InsureMind account."
                   data-id-signup="Daftar untuk mulai menggunakan InsureMind AI."
                   data-en-signup="Sign up to get started with InsureMind AI.">Masuk ke akun InsureMind Anda.</p>

                @if ($errors->any())
                    <div class="alert alert-error">{{ $errors->first() }}</div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                {{-- Tabs --}}
                <div class="tabs" role="tablist" aria-label="Jenis akses" data-id-aria="Jenis akses" data-en-aria="Account access">
                    <button type="button" role="tab" aria-selected="true" class="tab active" id="tabLogin" onclick="showTab('login')"
                            data-id="Masuk" data-en="Sign in">Masuk</button>
                    <button type="button" role="tab" aria-selected="false" class="tab" id="tabSignup" onclick="showTab('signup')"
                            data-id="Daftar" data-en="Sign up">Daftar</button>
                </div>

                {{-- Social auth --}}
                <div class="socials">
                    <button type="button" class="social-btn" onclick="socialNotice('Google')"
                            data-id-aria="Lanjutkan dengan Google" data-en-aria="Continue with Google">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#4285F4"
                                d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5a5.6 5.6 0 0 1-2.4 3.6v3h3.9c2.3-2.1 3.5-5.2 3.5-8.8Z" />
                            <path fill="#34A853"
                                d="M12 24c3.2 0 5.9-1.1 7.9-2.9l-3.9-3c-1.1.7-2.4 1.1-4 1.1-3.1 0-5.7-2.1-6.6-4.9H1.4v3.1A12 12 0 0 0 12 24Z" />
                            <path fill="#FBBC05" d="M5.4 14.3a7.2 7.2 0 0 1 0-4.6V6.6H1.4a12 12 0 0 0 0 10.8l4-3.1Z" />
                            <path fill="#EA4335"
                                d="M12 4.8c1.8 0 3.3.6 4.6 1.8l3.4-3.4A12 12 0 0 0 1.4 6.6l4 3.1C6.3 6.9 8.9 4.8 12 4.8Z" />
                        </svg>
                        <span data-id="Google" data-en="Google">Google</span>
                    </button>
                    <button type="button" class="social-btn" onclick="socialNotice('Apple')"
                            data-id-aria="Lanjutkan dengan Apple" data-en-aria="Continue with Apple">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#000"
                                d="M16.4 12.7c0-2.4 1.9-3.5 2-3.6-1.1-1.6-2.8-1.8-3.4-1.9-1.5-.1-2.8.9-3.5.9-.7 0-1.9-.9-3.1-.8-1.6 0-3 .9-3.8 2.4-1.6 2.8-.4 6.9 1.2 9.2.8 1.1 1.7 2.3 3 2.3 1.2 0 1.6-.8 3.1-.8 1.4 0 1.8.8 3.1.8 1.3 0 2.1-1.1 2.9-2.3.9-1.3 1.3-2.6 1.3-2.7-.1 0-2.5-1-2.5-3.7Zm-2.2-7.1c.6-.8 1.1-1.9 1-3-.9 0-2 .6-2.6 1.4-.6.7-1.1 1.8-1 2.9 1 .1 2-.5 2.6-1.3Z" />
                        </svg>
                        <span data-id="Apple" data-en="Apple">Apple</span>
                    </button>
                </div>

                <div class="divider" data-id="atau lanjutkan dengan email" data-en="or continue with email">atau lanjutkan dengan email</div>

                {{-- Login form --}}
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <input type="hidden" name="form" value="login">

                    <div class="field">
                        <label for="login_email" data-id="Email" data-en="Email">Email</label>
                        <input id="login_email" type="email" name="email" value="{{ old('email') }}"
                            placeholder="nama@email.com" data-id-placeholder="nama@email.com" data-en-placeholder="name@email.com"
                            autocomplete="email" required autofocus>
                        @error('email')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="login_password" data-id="Kata Sandi" data-en="Password">Kata Sandi</label>
                        <input id="login_password" type="password" name="password" placeholder="••••••••"
                            data-id-placeholder="Masukkan kata sandi" data-en-placeholder="Enter your password"
                            autocomplete="current-password" required>
                        @error('password')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row-between">
                        <label class="check">
                            <input type="checkbox" name="remember" value="1">
                            <span data-id="Ingat saya" data-en="Remember me">Ingat saya</span>
                        </label>
                        <a href="#" class="link" onclick="socialNotice('password reset'); return false;"
                           data-id="Lupa kata sandi?" data-en="Forgot password?">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" class="btn-primary" data-id="Masuk" data-en="Sign in">Masuk</button>
                </form>

                {{-- Sign up form --}}
                <form method="POST" action="{{ route('register') }}" id="signupForm" class="hidden">
                    @csrf
                    <input type="hidden" name="form" value="signup">

                    <div class="field">
                        <label for="signup_name" data-id="Nama Lengkap" data-en="Full name">Nama Lengkap</label>
                        <input id="signup_name" type="text" name="name" value="{{ old('name') }}"
                            placeholder="Nama Anda" data-id-placeholder="Nama Anda" data-en-placeholder="Your name"
                            autocomplete="name" required>
                        @error('name')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="signup_email" data-id="Email" data-en="Email">Email</label>
                        <input id="signup_email" type="email" name="email" value="{{ old('email') }}"
                            placeholder="nama@email.com" data-id-placeholder="nama@email.com" data-en-placeholder="name@email.com"
                            autocomplete="email" required>
                        @error('email')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="signup_password" data-id="Kata Sandi" data-en="Password">Kata Sandi</label>
                        <input id="signup_password" type="password" name="password" placeholder="Minimal 8 karakter"
                            data-id-placeholder="Minimal 8 karakter" data-en-placeholder="At least 8 characters"
                            autocomplete="new-password" required>
                        @error('password')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="signup_password_confirmation" data-id="Konfirmasi Kata Sandi" data-en="Confirm password">Konfirmasi Kata Sandi</label>
                        <input id="signup_password_confirmation" type="password" name="password_confirmation"
                            placeholder="Ulangi kata sandi" data-id-placeholder="Ulangi kata sandi" data-en-placeholder="Repeat your password"
                            autocomplete="new-password" required>
                    </div>

                    <div class="row-between">
                        <label class="check">
                            <input type="checkbox" name="terms" value="1" required>
                            <span data-id="Saya setuju dengan syarat &amp; ketentuan" data-en="I agree to the terms and conditions">Saya setuju dengan syarat &amp; ketentuan</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary" data-id="Buat Akun" data-en="Create account">Buat Akun</button>
                </form>

                <p class="foot-note" id="footNote">
                    <span data-id="Belum punya akun?" data-en="Don't have an account?">Belum punya akun?</span>
                    <a href="#" class="link" onclick="showTab('signup'); return false;"
                       data-id="Daftar sekarang" data-en="Sign up">Daftar sekarang</a>
                </p>
            </div>
        </main>
    </div>

    <script>
        let currentLang = localStorage.getItem('im_lang') || 'id';

        function applyLang(lang) {
            currentLang = lang === 'en' ? 'en' : 'id';

            document.querySelectorAll('[data-id], [data-en]').forEach(el => {
                const text = el.getAttribute(`data-${currentLang}`);
                if (text !== null && text !== '') {
                    el.innerHTML = text;
                }
            });

            document.querySelectorAll('[data-id-placeholder], [data-en-placeholder]').forEach(el => {
                const placeholder = el.getAttribute(`data-${currentLang}-placeholder`);
                if (placeholder !== null) el.setAttribute('placeholder', placeholder);
            });

            document.querySelectorAll('[data-id-title], [data-en-title]').forEach(el => {
                const title = el.getAttribute(`data-${currentLang}-title`);
                if (title !== null) el.setAttribute('title', title);
            });

            document.querySelectorAll('[data-id-aria], [data-en-aria]').forEach(el => {
                const label = el.getAttribute(`data-${currentLang}-aria`);
                if (label !== null) el.setAttribute('aria-label', label);
            });

            document.getElementById('htmlRoot').setAttribute('lang', currentLang);
            document.title = currentLang === 'id' ? 'Masuk | InsureMind AI' : 'Sign in | InsureMind AI';
            document.querySelector('meta[name="description"]').setAttribute(
                'content',
                currentLang === 'id'
                    ? 'Masuk atau daftar ke InsureMind AI — copilot asuransi berbasis AI untuk memahami polis dan mempersiapkan klaim.'
                    : 'Sign in or register for InsureMind AI — an AI-powered insurance copilot to understand policies and prepare claims.'
            );
            document.getElementById('btnLangID').classList.toggle('active', currentLang === 'id');
            document.getElementById('btnLangEN').classList.toggle('active', currentLang === 'en');
            document.getElementById('btnLangID').setAttribute('aria-pressed', String(currentLang === 'id'));
            document.getElementById('btnLangEN').setAttribute('aria-pressed', String(currentLang === 'en'));

            const activeTab = document.getElementById('loginForm').classList.contains('hidden') ? 'signup' : 'login';
            updateAuthCopy(activeTab);
            localStorage.setItem('im_lang', currentLang);
        }

        function updateAuthCopy(tab) {
            const suffix = tab === 'login' ? 'login' : 'signup';
            const title = document.getElementById('cardTitle');
            const subtitle = document.getElementById('cardSub');
            title.textContent = title.getAttribute(`data-${currentLang}-${suffix}`);
            subtitle.textContent = subtitle.getAttribute(`data-${currentLang}-${suffix}`);
            document.getElementById('footNote').innerHTML = tab === 'login'
                ? (currentLang === 'id'
                    ? 'Belum punya akun? <a href="#" class="link" onclick="showTab(\'signup\'); return false;">Daftar sekarang</a>'
                    : 'Don\'t have an account? <a href="#" class="link" onclick="showTab(\'signup\'); return false;">Sign up</a>')
                : (currentLang === 'id'
                    ? 'Sudah punya akun? <a href="#" class="link" onclick="showTab(\'login\'); return false;">Masuk di sini</a>'
                    : 'Already have an account? <a href="#" class="link" onclick="showTab(\'login\'); return false;">Sign in</a>');
        }

        function showTab(tab) {
            const isLogin = tab === 'login';
            document.getElementById('tabLogin').classList.toggle('active', isLogin);
            document.getElementById('tabSignup').classList.toggle('active', !isLogin);
            document.getElementById('tabLogin').setAttribute('aria-selected', String(isLogin));
            document.getElementById('tabSignup').setAttribute('aria-selected', String(!isLogin));
            document.getElementById('loginForm').classList.toggle('hidden', !isLogin);
            document.getElementById('signupForm').classList.toggle('hidden', isLogin);
            updateAuthCopy(tab);
        }

        function socialNotice(provider) {
            if (provider === 'password reset') {
                alert(currentLang === 'id'
                    ? 'Reset kata sandi belum dikonfigurasi. Silakan hubungi administrator.'
                    : 'Password reset is not configured yet. Please contact the administrator.');
                return;
            }

            alert(currentLang === 'id'
                ? 'Login dengan ' + provider + ' belum dikonfigurasi. Silakan gunakan email dan kata sandi.'
                : 'Sign in with ' + provider + ' is not configured yet. Please use your email and password.');
        }

        document.getElementById('btnLangID').addEventListener('click', () => applyLang('id'));
        document.getElementById('btnLangEN').addEventListener('click', () => applyLang('en'));
        applyLang(currentLang);

        @if ($errors->any() && old('form') === 'signup')
            showTab('signup');
        @endif
    </script>

    <span class="version-badge" aria-hidden="true">Beta v1.7</span>
</body>

</html>
