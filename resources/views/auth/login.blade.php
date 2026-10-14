<!DOCTYPE html>
<html lang="id">

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
            background: linear-gradient(160deg, var(--navy-1) 0%, var(--navy-2) 52%, var(--navy-3) 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-panel::after {
            content: "";
            position: absolute;
            width: 620px;
            height: 620px;
            right: -260px;
            top: -180px;
            border-radius: 50%;
            background: radial-gradient(circle at center, rgba(72, 120, 245, .38), transparent 62%);
            pointer-events: none;
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
            margin-bottom: 26px;
        }

        .brand-copy h1 {
            font-size: 40px;
            line-height: 1.16;
            letter-spacing: -.02em;
            margin: 0 0 18px;
            font-weight: 700;
        }

        .brand-copy p {
            margin: 0;
            font-size: 16px;
            color: rgba(255, 255, 255, .78);
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
        }

        .card {
            width: 100%;
            max-width: 430px;
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            padding: 34px 34px 30px;
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
            background: #f1f4fa;
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
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 11px;
            font: inherit;
            font-size: 14.5px;
            color: var(--ink);
            background: #fbfcfe;
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
            background: linear-gradient(145deg, #4b7bf7, var(--blue-dark));
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

        @media (max-width: 980px) {
            .wrap {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                padding: 40px 32px;
                gap: 28px;
            }

            .brand-copy h1 {
                font-size: 30px;
            }

            .brand-points {
                display: none;
            }
        }

        @media (max-width: 520px) {
            .form-panel {
                padding: 28px 18px;
            }

            .card {
                padding: 26px 22px;
            }

            .socials {
                grid-template-columns: 1fr;
            }
        }

        /* ── Badge versi (kanan bawah) ───────────────────────── */
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
            <a href="{{ route('landing') }}" class="brand-mark">
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

            <div class="brand-copy">
                <span class="eyebrow">AI-Powered Insurance Copilot</span>
                <h1>Understand Your Policy.<br>Prepare Your Claim.</h1>
                <p>Masuk untuk menganalisis polis, menilai kesiapan klaim, dan mengambil keputusan yang lebih
                    terinformasi.</p>
            </div>

            <ul class="brand-points">
                <li><span class="tick">✓</span> Penjelasan polis yang dapat ditelusuri &amp; transparan.</li>
                <li><span class="tick">✓</span> Skor kesiapan klaim dengan faktor yang jelas.</li>
                <li><span class="tick">✓</span> Selalu dalam pengawasan manusia (Human-in-the-Loop).</li>
            </ul>
        </aside>

        {{-- ── Right: auth form ───────────────────────────────── --}}
        <main class="form-panel">
            <div class="card">
                <a href="{{ route('landing') }}" class="back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Kembali ke beranda
                </a>

                <h2 id="cardTitle">Selamat datang kembali</h2>
                <p class="sub" id="cardSub">Masuk ke akun InsureMind Anda.</p>

                @if ($errors->any())
                    <div class="alert alert-error">{{ $errors->first() }}</div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                {{-- Tabs --}}
                <div class="tabs" role="tablist">
                    <button type="button" class="tab active" id="tabLogin" onclick="showTab('login')">Login</button>
                    <button type="button" class="tab" id="tabSignup" onclick="showTab('signup')">Sign Up</button>
                </div>

                {{-- Social auth --}}
                <div class="socials">
                    <button type="button" class="social-btn" onclick="socialNotice('Google')">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#4285F4"
                                d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5a5.6 5.6 0 0 1-2.4 3.6v3h3.9c2.3-2.1 3.5-5.2 3.5-8.8Z" />
                            <path fill="#34A853"
                                d="M12 24c3.2 0 5.9-1.1 7.9-2.9l-3.9-3c-1.1.7-2.4 1.1-4 1.1-3.1 0-5.7-2.1-6.6-4.9H1.4v3.1A12 12 0 0 0 12 24Z" />
                            <path fill="#FBBC05" d="M5.4 14.3a7.2 7.2 0 0 1 0-4.6V6.6H1.4a12 12 0 0 0 0 10.8l4-3.1Z" />
                            <path fill="#EA4335"
                                d="M12 4.8c1.8 0 3.3.6 4.6 1.8l3.4-3.4A12 12 0 0 0 1.4 6.6l4 3.1C6.3 6.9 8.9 4.8 12 4.8Z" />
                        </svg>
                        Google
                    </button>
                    <button type="button" class="social-btn" onclick="socialNotice('Apple')">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#000"
                                d="M16.4 12.7c0-2.4 1.9-3.5 2-3.6-1.1-1.6-2.8-1.8-3.4-1.9-1.5-.1-2.8.9-3.5.9-.7 0-1.9-.9-3.1-.8-1.6 0-3 .9-3.8 2.4-1.6 2.8-.4 6.9 1.2 9.2.8 1.1 1.7 2.3 3 2.3 1.2 0 1.6-.8 3.1-.8 1.4 0 1.8.8 3.1.8 1.3 0 2.1-1.1 2.9-2.3.9-1.3 1.3-2.6 1.3-2.7-.1 0-2.5-1-2.5-3.7Zm-2.2-7.1c.6-.8 1.1-1.9 1-3-.9 0-2 .6-2.6 1.4-.6.7-1.1 1.8-1 2.9 1 .1 2-.5 2.6-1.3Z" />
                        </svg>
                        Apple
                    </button>
                </div>

                <div class="divider">atau lanjutkan dengan email</div>

                {{-- Login form --}}
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <input type="hidden" name="form" value="login">

                    <div class="field">
                        <label for="login_email">Email</label>
                        <input id="login_email" type="email" name="email" value="{{ old('email') }}"
                            placeholder="nama@email.com" autocomplete="email" required autofocus>
                        @error('email')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="login_password">Kata Sandi</label>
                        <input id="login_password" type="password" name="password" placeholder="••••••••"
                            autocomplete="current-password" required>
                        @error('password')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row-between">
                        <label class="check">
                            <input type="checkbox" name="remember" value="1"> Ingat saya
                        </label>
                        <a href="#" class="link" onclick="socialNotice('Reset kata sandi'); return false;">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" class="btn-primary">Masuk</button>
                </form>

                {{-- Sign up form --}}
                <form method="POST" action="{{ route('register') }}" id="signupForm" class="hidden">
                    @csrf
                    <input type="hidden" name="form" value="signup">

                    <div class="field">
                        <label for="signup_name">Nama Lengkap</label>
                        <input id="signup_name" type="text" name="name" value="{{ old('name') }}"
                            placeholder="Nama Anda" autocomplete="name" required>
                        @error('name')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="signup_email">Email</label>
                        <input id="signup_email" type="email" name="email" value="{{ old('email') }}"
                            placeholder="nama@email.com" autocomplete="email" required>
                        @error('email')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="signup_password">Kata Sandi</label>
                        <input id="signup_password" type="password" name="password" placeholder="Minimal 8 karakter"
                            autocomplete="new-password" required>
                        @error('password')
                            <div class="err">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="signup_password_confirmation">Konfirmasi Kata Sandi</label>
                        <input id="signup_password_confirmation" type="password" name="password_confirmation"
                            placeholder="Ulangi kata sandi" autocomplete="new-password" required>
                    </div>

                    <div class="row-between">
                        <label class="check">
                            <input type="checkbox" name="terms" value="1" required> Saya setuju dengan syarat &amp; ketentuan
                        </label>
                    </div>

                    <button type="submit" class="btn-primary">Buat Akun</button>
                </form>

                <p class="foot-note" id="footNote">
                    Belum punya akun? <a href="#" class="link" onclick="showTab('signup'); return false;">Daftar sekarang</a>
                </p>
            </div>
        </main>
    </div>

    <script>
        function showTab(tab) {
            const isLogin = tab === 'login';
            document.getElementById('tabLogin').classList.toggle('active', isLogin);
            document.getElementById('tabSignup').classList.toggle('active', !isLogin);
            document.getElementById('loginForm').classList.toggle('hidden', !isLogin);
            document.getElementById('signupForm').classList.toggle('hidden', isLogin);
            document.getElementById('cardTitle').textContent = isLogin ? 'Selamat datang kembali' : 'Buat akun Anda';
            document.getElementById('cardSub').textContent = isLogin ?
                'Masuk ke akun InsureMind Anda.' :
                'Daftar untuk mulai menggunakan InsureMind AI.';
            document.getElementById('footNote').innerHTML = isLogin ?
                'Belum punya akun? <a href="#" class="link" onclick="showTab(\'signup\'); return false;">Daftar sekarang</a>' :
                'Sudah punya akun? <a href="#" class="link" onclick="showTab(\'login\'); return false;">Masuk di sini</a>';
        }

        function socialNotice(provider) {
            alert('Login dengan ' + provider + ' belum dikonfigurasi. Silakan gunakan email & kata sandi.');
        }

        @if ($errors->any() && old('form') === 'signup')
            showTab('signup');
        @endif
    </script>

    <span class="version-badge" aria-hidden="true">Beta v1.1</span>
</body>

</html>
