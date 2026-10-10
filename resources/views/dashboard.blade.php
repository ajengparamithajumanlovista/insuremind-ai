<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f8fc">
    <title>InsureMind AI | Dashboard</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Design tokens ───────────────────────────────────── */
        :root {
            --ink: #17243b;
            --muted: #77839a;
            --line: #e7ebf2;
            --blue: #315ee8;
            --pale: #f6f8fc;
        }

        /* ── Reset ───────────────────────────────────────────── */
        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            background: var(--pale);
            color: var(--ink);
            font: 14px/1.5 Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        button, a { font: inherit; }

        a {
            color: inherit;
            text-decoration: none;
        }

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
            z-index: 5;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 2px 11px 31px;
        }

        .brand-mark {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: linear-gradient(145deg, #4778f5, #244bd4);
            display: grid;
            place-items: center;
            color: white;
            box-shadow: 0 6px 14px #315ee833;
        }

        .brand-name {
            font-size: 17px;
            font-weight: 750;
            letter-spacing: -.5px;
        }

        .brand-name span { color: var(--blue); }

        .nav-label {
            font-size: 10px;
            color: #a0a9b8;
            font-weight: 750;
            letter-spacing: 1.2px;
            padding: 0 12px 10px;
        }

        .nav {
            display: grid;
            gap: 5px;
        }

        .nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 10px;
            color: #68758b;
            font-size: 13px;
            font-weight: 550;
        }

        .nav a:hover,
        .nav a.active {
            background: #eef3ff;
            color: #2855dd;
        }

        .nav-icon {
            width: 18px;
            text-align: center;
            font-size: 16px;
        }

        .side-note {
            margin-top: auto;
            background: #f5f7fb;
            border: 1px solid #edf0f5;
            border-radius: 12px;
            padding: 13px;
            color: #738097;
            font-size: 11px;
        }

        .side-note strong {
            display: block;
            color: #35435a;
            font-size: 12px;
            margin-bottom: 4px;
        }

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
        }

        .profile b {
            display: block;
            font-size: 12px;
        }

        .profile small {
            font-size: 10px;
            color: var(--muted);
        }

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
        }

        .crumb {
            font-size: 12px;
            color: var(--muted);
        }

        .crumb b { color: #344159; }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .icon-button,
        .menu-button {
            width: 36px;
            height: 36px;
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 10px;
            color: #627087;
            display: grid;
            place-items: center;
        }

        .menu-button { display: none; }

        .page-title {
            font-size: 25px;
            letter-spacing: -.7px;
            margin: 0 0 5px;
        }

        .page-subtitle {
            color: var(--muted);
            margin: 0 0 23px;
            font-size: 13px;
        }

        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            margin: 0 0 12px;
        }

        .section-heading h2 {
            margin: 0;
            font-size: 15px;
            letter-spacing: -.2px;
        }

        .section-heading p {
            margin: 3px 0 0;
            font-size: 11px;
            color: var(--muted);
        }

        .subtle-link {
            font-size: 11px;
            color: #71809a;
        }

        /* ── Welcome banner ──────────────────────────────────── */
        .welcome {
            border-radius: 16px;
            background: linear-gradient(110deg, #172e53, #24477d 62%, #3561a3);
            color: white;
            padding: 25px 29px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            overflow: hidden;
            position: relative;
            margin-bottom: 23px;
        }

        .welcome::after {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border: 1px solid #ffffff18;
            border-radius: 50%;
            right: 9%;
            top: -155px;
            box-shadow: 0 0 0 36px #ffffff08, 0 0 0 72px #ffffff06;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .eyebrow {
            font-size: 10px;
            letter-spacing: 1.3px;
            text-transform: uppercase;
            color: #b8cbf6;
            font-weight: 700;
        }

        .welcome h2 {
            font-size: 21px;
            letter-spacing: -.4px;
            margin: 7px 0;
        }

        .welcome p {
            margin: 0;
            color: #d0dcf2;
            font-size: 12px;
        }

        .welcome-badge {
            position: relative;
            z-index: 1;
            border: 1px solid #ffffff30;
            background: #ffffff12;
            border-radius: 10px;
            padding: 10px 13px;
            font-size: 11px;
            color: #e5edfc;
            white-space: nowrap;
        }

        /* ── Upload card ─────────────────────────────────────── */
        .upload-card,
        .documents-card,
        .preview-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
        }

        .upload-card {
            padding: 19px;
            margin-bottom: 25px;
        }

        .upload-box {
            min-height: 116px;
            border: 1.5px dashed #cbd5e7;
            border-radius: 11px;
            background: #fbfcff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 18px;
            cursor: pointer;
            transition: .2s;
        }

        .upload-box:hover,
        .upload-box.dragover {
            border-color: #5578ef;
            background: #f4f7ff;
        }

        .upload-symbol {
            width: 37px;
            height: 37px;
            border-radius: 12px;
            background: #edf2ff;
            color: #4167e4;
            display: grid;
            place-items: center;
            margin: 0 auto 8px;
            font-size: 19px;
        }

        .upload-box strong {
            display: block;
            font-size: 12px;
        }

        .upload-box p {
            color: var(--muted);
            font-size: 10px;
            margin: 4px 0 0;
        }

        .upload-box input { display: none; }

        .upload-note {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #8a95a8;
            font-size: 10px;
            margin: 9px 2px 0;
        }

        .picked-file {
            display: none;
            margin: 10px 0 0;
            padding: 9px 11px;
            border-radius: 8px;
            background: #f3f6fc;
            color: #52617b;
            font-size: 11px;
        }

        /* ── Documents card ──────────────────────────────────── */
        .documents-card {
            padding: 19px;
            margin-bottom: 24px;
        }

        .empty-state {
            text-align: center;
            padding: 27px 15px 23px;
        }

        .empty-art {
            width: 64px;
            height: 64px;
            background: #f0f4ff;
            border-radius: 19px;
            display: grid;
            place-items: center;
            margin: 0 auto 12px;
            color: #5878dd;
        }

        .empty-state h3 {
            margin: 0;
            font-size: 13px;
        }

        .empty-state p {
            font-size: 11px;
            color: var(--muted);
            max-width: 315px;
            margin: 5px auto 0;
        }

        /* ── Lower grid ──────────────────────────────────────── */
        .lower-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .preview-card {
            padding: 18px;
            min-height: 191px;
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .card-icon {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            background: #eef3ff;
            color: #4568dc;
            display: grid;
            place-items: center;
        }

        .card-title strong { font-size: 12px; }

        .status {
            margin-left: auto;
            border-radius: 20px;
            background: #f1f3f7;
            color: #8490a4;
            padding: 4px 8px;
            font-size: 9px;
            font-weight: 650;
        }

        .preview-empty {
            margin-top: 16px;
            border-top: 1px solid #f0f2f6;
            padding-top: 13px;
            color: #8a95a8;
            font-size: 11px;
            line-height: 1.6;
        }

        .placeholder-lines {
            display: grid;
            gap: 7px;
            margin-top: 11px;
        }

        .placeholder-lines i {
            display: block;
            height: 6px;
            border-radius: 8px;
            background: #f0f2f6;
        }

        .placeholder-lines i:nth-child(2) { width: 82%; }
        .placeholder-lines i:nth-child(3) { width: 60%; }

        /* ── Chat section ────────────────────────────────────── */
        .chat-disabled {
            display: flex;
            align-items: center;
            gap: 11px;
            border: 1px solid #edf0f5;
            background: #fafbfe;
            padding: 12px;
            border-radius: 10px;
            margin-top: 15px;
        }

        .chat-orb {
            width: 30px;
            height: 30px;
            flex: none;
            border-radius: 10px;
            background: #eff2fb;
            display: grid;
            place-items: center;
            color: #7b8bb1;
        }

        .chat-disabled p {
            font-size: 10px;
            color: #8490a3;
            margin: 0;
        }

        .chat-disabled strong {
            color: #59677d;
            display: block;
            margin-bottom: 2px;
            font-size: 11px;
        }

        .chat-input {
            margin-top: 10px;
            display: flex;
            border: 1px solid #e8ebf1;
            border-radius: 9px;
            padding: 5px;
            background: #fafbfc;
            opacity: .62;
        }

        .chat-input span {
            font-size: 10px;
            color: #9ba5b4;
            padding: 7px 8px;
            flex: 1;
        }

        .chat-input button {
            border: 0;
            background: #e9edf5;
            color: #9aa5b6;
            border-radius: 7px;
            padding: 0 10px;
        }

        /* ── Footer ──────────────────────────────────────────── */
        .footer-note {
            text-align: center;
            font-size: 10px;
            color: #98a2b2;
            margin-top: 25px;
        }

        .mobile-overlay { display: none; }

        /* ── Responsive ──────────────────────────────────────── */
        @media (max-width: 1050px) {
            .sidebar { width: 220px; }
            .main {
                margin-left: 220px;
                padding-left: 26px;
                padding-right: 26px;
            }
        }

        @media (max-width: 760px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform .22s ease;
                width: 250px;
                box-shadow: 12px 0 30px #15254112;
            }

            .sidebar.open { transform: translateX(0); }

            .mobile-overlay.show {
                display: block;
                position: fixed;
                inset: 0;
                background: #14213b66;
                z-index: 4;
            }

            .main {
                margin-left: 0;
                padding: 0 18px 35px;
            }

            .topbar {
                height: 60px;
                margin-bottom: 23px;
            }

            .menu-button { display: grid; }

            .top-actions { gap: 8px; }

            .welcome {
                padding: 21px;
                align-items: flex-start;
            }

            .welcome-badge { display: none; }

            .welcome h2 { font-size: 19px; }

            .page-title { font-size: 22px; }

            .lower-grid { grid-template-columns: 1fr; }

            .section-heading { align-items: start; }

            .upload-card,
            .documents-card { padding: 15px; }
        }

        @media (max-width: 390px) {
            .main {
                padding-left: 13px;
                padding-right: 13px;
            }

            .upload-box { min-height: 108px; }

            .crumb { font-size: 11px; }
        }
    </style>
</head>

<body>
    <div class="layout">

        {{-- ── Sidebar ───────────────────────────────────── --}}
        <aside class="sidebar" id="sidebar">

            {{-- Brand --}}
            <a href="{{ route('dashboard') }}" class="brand" aria-label="InsureMind AI beranda">
                <span class="brand-mark">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 2.8 20 6v5.2c0 5.1-3.4 8.5-8 10-4.6-1.5-8-4.9-8-10V6l8-3.2Z"
                              stroke="currentColor" stroke-width="1.8"/>
                        <path d="m8.5 12 2.2 2.2 4.8-5" stroke="currentColor" stroke-width="1.8"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="brand-name">InsureMind <span>AI</span></span>
            </a>

            {{-- Navigation --}}
            <div class="nav-label">RUANG KERJA</div>
            <nav class="nav" aria-label="Navigasi utama">
                <a class="active" href="#dashboard">
                    <span class="nav-icon">&#x25eb;</span> Dashboard
                </a>
                <a href="#dokumen">
                    <span class="nav-icon">&#x25a4;</span> Dokumen polis
                </a>
                <a href="#ringkasan">
                    <span class="nav-icon">&#x25f7;</span> Ringkasan AI
                </a>
                <a href="#chat">
                    <span class="nav-icon">&#x2733;</span> Chat AI
                </a>
            </nav>

            {{-- Side note --}}
            <div class="side-note">
                <strong>Ruang yang aman</strong>
                Dokumen Anda akan dikelola dengan privasi sebagai prioritas.
            </div>

            {{-- Profile --}}
            <div class="profile">
                <div class="avatar">IM</div>
                <div>
                    <b>Pengguna</b>
                    <small>Ruang pribadi</small>
                </div>
            </div>

        </aside>

        {{-- Mobile overlay --}}
        <div class="mobile-overlay" id="overlay"></div>

        {{-- ── Main content ──────────────────────────────── --}}
        <main class="main" id="dashboard">

            {{-- Top bar --}}
            <header class="topbar">
                <div style="display:flex; align-items:center; gap:12px">
                    <button class="menu-button" id="menuButton"
                            aria-label="Buka navigasi" aria-expanded="false">
                        &#x2630;
                    </button>
                    <div class="crumb">
                        Ruang kerja <span aria-hidden="true">/</span> <b>Dashboard</b>
                    </div>
                </div>
                <div class="top-actions">
                    <span class="subtle-link">AI Finance Insurance</span>
                    <button class="icon-button" aria-label="Notifikasi" title="Notifikasi">&#x2667;</button>
                </div>
            </header>

            {{-- Page heading --}}
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Kelola dan pahami perlindungan asuransi Anda dalam satu tempat.</p>

            {{-- Welcome banner --}}
            <section class="welcome" aria-labelledby="welcome-title">
                <div class="welcome-content">
                    <div class="eyebrow">INSUREMIND AI &middot; RUANG KERJA ANDA</div>
                    <h2 id="welcome-title">Selamat datang di InsureMind AI</h2>
                    <p>Mulai dengan menambahkan dokumen polis untuk menjelajahi perlindungan Anda.</p>
                </div>
                <div class="welcome-badge">&#x2726; &nbsp; Asisten polis Anda</div>
            </section>

            {{-- Upload section --}}
            <section id="unggah" aria-labelledby="upload-title">
                <div class="section-heading">
                    <div>
                        <h2 id="upload-title">Tambahkan dokumen polis</h2>
                        <p>Mulai dari file polis asuransi dalam format PDF.</p>
                    </div>
                </div>
                <div class="upload-card">
                    <label class="upload-box" id="dropArea" for="policyFile">
                        <span>
                            <span class="upload-symbol">&#x2191;</span>
                            <strong>Pilih file PDF atau seret ke sini</strong>
                            <p>PDF &middot; Maksimum 20 MB</p>
                        </span>
                        <input id="policyFile" type="file" accept="application/pdf,.pdf">
                    </label>
                    <div class="picked-file" id="pickedFile" aria-live="polite"></div>
                    <div class="upload-note">
                        <span aria-hidden="true">&#x24d8;</span>
                        Area unggah ini merupakan pratinjau. Pengiriman dan penyimpanan dokumen belum tersedia.
                    </div>
                </div>
            </section>

            {{-- Documents section --}}
            <section id="dokumen" aria-labelledby="documents-title">
                <div class="section-heading">
                    <div>
                        <h2 id="documents-title">Dokumen polis</h2>
                        <p>Daftar dokumen yang Anda tambahkan.</p>
                    </div>
                    <span class="subtle-link">0 dokumen</span>
                </div>
                <div class="documents-card">
                    <div class="empty-state">
                        <div class="empty-art">
                            <svg width="31" height="31" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                                <path d="M9 4.5h9l6 6V26a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V6.5a2 2 0 0 1 2-2Z"
                                      stroke="currentColor" stroke-width="1.6"/>
                                <path d="M18 5v6h6M11 17h10M11 21h7" stroke="currentColor" stroke-width="1.6"
                                      stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3>Belum ada dokumen</h3>
                        <p>Dokumen polis yang ditambahkan akan tampil di sini. Unggah dan pemrosesan belum diaktifkan.</p>
                    </div>
                </div>
            </section>

            {{-- Lower grid: Summary + Chat --}}
            <div class="lower-grid">

                {{-- Policy summary --}}
                <section class="preview-card" id="ringkasan" aria-labelledby="summary-title">
                    <div class="card-title">
                        <span class="card-icon">&#x25f7;</span>
                        <strong id="summary-title">Ringkasan polis</strong>
                        <span class="status">Belum tersedia</span>
                    </div>
                    <div class="preview-empty">
                        Ringkasan akan ditampilkan setelah dokumen tersedia dan pemrosesan polis dikembangkan.
                        <div class="placeholder-lines" aria-hidden="true">
                            <i></i>
                            <i></i>
                            <i></i>
                        </div>
                    </div>
                </section>

                {{-- AI Chat --}}
                <section class="preview-card" id="chat" aria-labelledby="chat-title">
                    <div class="card-title">
                        <span class="card-icon">&#x2733;</span>
                        <strong id="chat-title">Chat AI</strong>
                        <span class="status">Belum aktif</span>
                    </div>
                    <div class="chat-disabled">
                        <span class="chat-orb">&#x2733;</span>
                        <p>
                            <strong>Asisten polis belum tersedia</strong>
                            Chat akan hadir setelah backend AI dan dokumen siap.
                        </p>
                    </div>
                    <div class="chat-input" aria-disabled="true">
                        <span>Pilih dokumen untuk mulai bertanya...</span>
                        <button type="button" disabled aria-label="Kirim">&#x2191;</button>
                    </div>
                </section>

            </div>

            <div class="footer-note">InsureMind AI &middot; Bantu pahami polis Anda dengan lebih mudah.</div>

        </main>
    </div>

    <script>
        // ── Sidebar toggle (mobile) ───────────────────────────────────────
        const sidebar    = document.getElementById('sidebar');
        const menuButton = document.getElementById('menuButton');
        const overlay    = document.getElementById('overlay');

        const closeMenu = () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            menuButton.setAttribute('aria-expanded', 'false');
        };

        menuButton.addEventListener('click', () => {
            const open = sidebar.classList.toggle('open');
            overlay.classList.toggle('show', open);
            menuButton.setAttribute('aria-expanded', String(open));
        });

        overlay.addEventListener('click', closeMenu);

        sidebar.querySelectorAll('.nav a').forEach(link => {
            link.addEventListener('click', closeMenu);
        });

        // ── File upload ───────────────────────────────────────────────────
        const input      = document.getElementById('policyFile');
        const dropArea   = document.getElementById('dropArea');
        const pickedFile = document.getElementById('pickedFile');

        const showSelection = file => {
            if (!file) return;
            pickedFile.style.display = 'block';
            pickedFile.textContent = file.name.toLowerCase().endsWith('.pdf')
                ? 'Dipilih: ' + file.name + ' — belum diunggah.'
                : 'Pilih file dengan format PDF.';
        };

        input.addEventListener('change', () => showSelection(input.files[0]));

        ['dragenter', 'dragover'].forEach(name => {
            dropArea.addEventListener(name, event => {
                event.preventDefault();
                dropArea.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(name => {
            dropArea.addEventListener(name, event => {
                event.preventDefault();
                dropArea.classList.remove('dragover');
            });
        });

        dropArea.addEventListener('drop', event => {
            showSelection(event.dataTransfer.files[0]);
        });
    </script>
</body>

</html>
