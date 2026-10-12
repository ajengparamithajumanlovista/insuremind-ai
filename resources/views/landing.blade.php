<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f8fc">
    <title>InsureMind AI | Understand Your Policy. Prepare Your Claim.</title>
    <meta name="description"
        content="AI-powered insurance copilot that helps you understand coverage, assess your claim readiness, and make better-informed decisions.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Design tokens ───────────────────────────────────── */
        :root {
            --ink: #17243b;
            --muted: #77839a;
            --soft: #8a95a8;
            --line: #e7ebf2;
            --blue: #315ee8;
            --blue-dark: #244bd4;
            --pale: #f6f8fc;
            --navy: #172e53;
        }

        /* ── Reset ───────────────────────────────────────────── */
        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            background: var(--pale);
            color: var(--ink);
            font: 14px/1.5 Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        img, svg { display: block; }

        a { color: inherit; text-decoration: none; }

        button { font: inherit; cursor: pointer; }

        .wrap {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .eyebrow {
            font-size: 11px;
            font-weight: 750;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: #8391ab;
        }

        .section { padding: 76px 0; }

        .section-head {
            text-align: center;
            max-width: 720px;
            margin: 0 auto 40px;
        }

        .section-head h2 {
            margin: 10px 0 0;
            font-size: 32px;
            line-height: 1.25;
            letter-spacing: -.9px;
        }

        .section-head p {
            margin: 12px 0 0;
            color: var(--muted);
            font-size: 15px;
        }

        /* ── Buttons ─────────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 10px;
            padding: 11px 18px;
            font-size: 13px;
            font-weight: 650;
            border: 1px solid transparent;
            transition: .18s;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(145deg, #4778f5, #244bd4);
            color: #fff;
            box-shadow: 0 10px 22px #315ee833;
        }

        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 14px 26px #315ee83d; }

        .btn-ghost {
            background: #fff;
            border-color: var(--line);
            color: #35435a;
        }

        .btn-ghost:hover { border-color: #c9d4ea; background: #fbfcff; }

        .btn-light { background: #fff; color: var(--ink); }

        .btn-outline-light {
            background: #ffffff14;
            border-color: #ffffff40;
            color: #fff;
        }

        .btn-sm { padding: 8px 14px; font-size: 12px; }

        /* ── Brand ───────────────────────────────────────────── */
        .brand { display: flex; align-items: center; gap: 11px; }

        .brand-mark {
            width: 34px;
            height: 34px;
            border-radius: 11px;
            background: linear-gradient(145deg, #4778f5, #244bd4);
            display: grid;
            place-items: center;
            color: #fff;
            box-shadow: 0 6px 14px #315ee833;
        }

        .brand-name {
            font-size: 17px;
            font-weight: 750;
            letter-spacing: -.5px;
        }

        .brand-name span { color: var(--blue); }

        /* ── Header ──────────────────────────────────────────── */
        .header {
            position: sticky;
            top: 0;
            z-index: 40;
            background: #ffffffe6;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--line);
        }

        .header-inner {
            height: 64px;
            position: relative;
            display: flex;
            align-items: center;
            gap: 26px;
        }

        .nav { display: flex; align-items: center; gap: 4px; margin-left: 6px; }

        .nav a {
            padding: 8px 13px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 550;
            color: #68758b;
        }

        .nav a:hover { background: #eef3ff; color: #2855dd; }

        .header-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .lang {
            display: flex;
            align-items: center;
            gap: 6px;
            border: 1px solid var(--line);
            border-radius: 9px;
            padding: 5px;
        }

        .lang button {
            border: 0;
            background: transparent;
            border-radius: 6px;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 650;
            color: #8490a4;
        }

        .lang button.active { background: #eef3ff; color: #2855dd; }

        .login-link { font-size: 13px; font-weight: 600; color: #4a576e; padding: 8px 6px; }

        .hamburger {
            display: none;
            width: 38px;
            height: 38px;
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 10px;
            color: #627087;
            margin-left: auto;
            place-items: center;
            font-size: 17px;
        }

        /* ── Hero ────────────────────────────────────────────── */
        .hero { padding: 60px 0 40px; }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 46px;
            align-items: center;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #eef3ff;
            color: #2d55c8;
            border-radius: 30px;
            padding: 7px 14px;
            font-size: 11px;
            font-weight: 650;
        }

        .pill i {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #315ee8;
            box-shadow: 0 0 0 4px #315ee822;
        }

        .hero h1 {
            margin: 18px 0 0;
            font-size: 46px;
            line-height: 1.12;
            letter-spacing: -1.6px;
        }

        .hero h1 em { font-style: normal; color: var(--blue); }

        .hero p { margin: 16px 0 0; color: var(--muted); font-size: 15.5px; max-width: 520px; }

        .hero-cta { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 26px; }

        .hero-stats {
            display: flex;
            gap: 34px;
            margin-top: 34px;
            padding-top: 24px;
            border-top: 1px solid var(--line);
        }

        .hero-stats b { display: block; font-size: 24px; letter-spacing: -.8px; }

        .hero-stats small { color: var(--soft); font-size: 11px; }

        /* ── Mock card ───────────────────────────────────────── */
        .mock {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 30px 60px #172e5317;
            overflow: hidden;
        }

        .mock-head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 17px;
            border-bottom: 1px solid var(--line);
            background: #fbfcff;
        }

        .mock-avatar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #eef3ff;
            color: #4568dc;
            display: grid;
            place-items: center;
            flex: none;
        }

        .mock-head b { display: block; font-size: 12.5px; }

        .mock-head small { color: var(--soft); font-size: 10.5px; }

        .tag {
            margin-left: auto;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .4px;
            border-radius: 7px;
            padding: 5px 9px;
            background: #eef3ff;
            color: #3f5fc4;
        }

        .mock-body { padding: 17px; }

        .mock-label {
            font-size: 10px;
            font-weight: 750;
            letter-spacing: 1px;
            color: #9aa4b6;
            text-transform: uppercase;
        }

        .score-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
        }

        .score-num { font-size: 40px; font-weight: 800; letter-spacing: -1.6px; }

        .score-num small { font-size: 16px; font-weight: 700; }

        .score-desc { color: var(--muted); font-size: 11.5px; margin-top: 2px; }

        .ring {
            position: relative;
            --val: 82;
            width: 84px;
            height: 84px;
            border-radius: 50%;
            flex: none;
            background: conic-gradient(var(--blue) calc(var(--val) * 1%), #e8edf7 0);
            display: grid;
            place-items: center;
        }

        .ring::before {
            content: "";
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #fff;
        }

        .ring span {
            position: absolute;
            font-weight: 750;
            font-size: 15px;
            color: #2b3a55;
        }

        .mock-list { margin-top: 18px; }

        .mock-li {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 11px 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            margin-bottom: 8px;
            font-size: 11.5px;
            font-weight: 550;
            color: #3d4a60;
        }

        .chip {
            margin-left: auto;
            font-size: 9px;
            font-weight: 700;
            border-radius: 20px;
            padding: 3px 9px;
            white-space: nowrap;
        }

        .chip-ok { background: #e7f7ee; color: #1f8a55; }
        .chip-warn { background: #fdf3e2; color: #b47b1c; }
        .chip-bad { background: #fdeceb; color: #c4483f; }

        .alert {
            display: flex;
            align-items: center;
            gap: 9px;
            background: #fdeceb;
            color: #a9423a;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 11.5px;
            font-weight: 600;
            margin-top: 6px;
        }

        .alert .chip { background: #ffffffc4; }

        .mock-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 15px;
            padding-top: 13px;
            border-top: 1px solid var(--line);
            font-size: 10.5px;
            color: var(--soft);
            flex-wrap: wrap;
        }

        /* ── Generic cards ───────────────────────────────────── */
        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
        }

        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }

        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }

        /* ── Friction cards ──────────────────────────────────── */
        .friction {
            background: linear-gradient(180deg, #f6f8fc 0%, #eef2fa 100%);
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
        }

        .fraud-card { padding: 21px; }

        .fraud-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #eef3ff;
            color: #4568dc;
            display: grid;
            place-items: center;
            margin-bottom: 15px;
        }

        .fraud-card h3 { margin: 0; font-size: 15px; letter-spacing: -.2px; }

        .fraud-card p { margin: 8px 0 0; font-size: 12.5px; color: var(--muted); }

        .fraud-foot {
            margin-top: 15px;
            padding-top: 13px;
            border-top: 1px solid #f0f2f7;
            font-size: 11px;
            color: #c4483f;
            font-weight: 600;
        }

        /* ── Journey ─────────────────────────────────────────── */
        .journey-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 32px;
        }

        .journey-head h2 { margin: 10px 0 0; font-size: 30px; letter-spacing: -.9px; line-height: 1.2; }

        .journey-head p { margin: 0; color: var(--muted); font-size: 13px; max-width: 320px; }

        .step-card { padding: 22px; }

        .step-num {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: #eef3ff;
            color: #2d55c8;
            display: grid;
            place-items: center;
            font-weight: 750;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .step-card h3 { margin: 0; font-size: 16px; letter-spacing: -.2px; }

        .step-card p { margin: 8px 0 0; font-size: 12.5px; color: var(--muted); }

        .check-list { margin: 15px 0 0; padding: 0; list-style: none; display: grid; gap: 8px; }

        .check-list li {
            font-size: 12px;
            color: #5b6a83;
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        .check-list li::before {
            content: "\2713";
            color: #2d55c8;
            font-weight: 800;
            font-size: 11px;
            margin-top: 1px;
        }

        /* ── Score panel ─────────────────────────────────────── */
        .score-panel {
            display: grid;
            grid-template-columns: .9fr 1.1fr;
            gap: 34px;
            align-items: center;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 24px 48px #172e530f;
        }

        .calc-matrix {
            background: #f8faff;
            border: 1px solid #eaeff8;
            border-radius: 14px;
            padding: 18px;
        }

        .calc-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .matrix-badge {
            font-size: 9px;
            font-weight: 700;
            background: #e8edff;
            color: #3f5fc4;
            border-radius: 6px;
            padding: 4px 8px;
        }

        .big-ring {
            --val: 82;
            position: relative;
            width: 132px;
            height: 132px;
            border-radius: 50%;
            margin: 0 auto;
            background: conic-gradient(var(--blue) calc(var(--val) * 1%), #e8edf7 0);
            display: grid;
            place-items: center;
        }

        .big-ring::before {
            content: "";
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #fff;
        }

        .big-ring i {
            position: absolute;
            font-style: normal;
            font-weight: 800;
            font-size: 27px;
            letter-spacing: -1px;
        }

        .big-ring i small { font-size: 11px; display: block; font-weight: 700; color: var(--muted); letter-spacing: 0; }

        .matrix-legend {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-top: 16px;
        }

        .matrix-legend div {
            border: 1px solid #eaeff8;
            border-radius: 9px;
            padding: 9px 6px;
            background: #fff;
            text-align: center;
        }

        .matrix-legend b { display: block; font-size: 13px; }

        .matrix-legend small { font-size: 9.5px; color: var(--soft); }

        .score-copy h2 { margin: 10px 0 0; font-size: 30px; letter-spacing: -.9px; line-height: 1.2; }

        .score-copy > p { margin: 12px 0 0; color: var(--muted); font-size: 13.5px; }

        .calc-list { margin: 18px 0 0; display: grid; gap: 9px; }

        .calc-row {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 11.5px;
            font-weight: 550;
            color: #3d4a60;
        }

        .calc-row small { display: block; color: var(--soft); font-weight: 500; font-size: 10.5px; }

        /* ── Citations ───────────────────────────────────────── */
        .citation-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 20px 44px #172e530f;
            max-width: 900px;
            margin: 0 auto;
        }

        .citation-head { display: flex; align-items: center; gap: 11px; flex-wrap: wrap; }

        .citation-head .card-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: #eef3ff;
            color: #4568dc;
            display: grid;
            place-items: center;
            flex: none;
        }

        .citation-head b { font-size: 13px; }

        .conf { font-size: 10px; font-weight: 700; background: #e7f7ee; color: #1f8a55; border-radius: 20px; padding: 3px 10px; }

        .citation-head .model { margin-left: auto; font-size: 10.5px; color: var(--soft); }

        .factors { margin: 18px 0 0; }

        .factor-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 10px; }

        .factor {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 11px 12px;
            font-size: 11.5px;
            color: #3d4a60;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .factor::before { content: "\2713"; color: #1f8a55; font-weight: 800; }

        .quote {
            margin-top: 16px;
            background: #f8faff;
            border: 1px solid #eaeff8;
            border-left: 3px solid #315ee8;
            border-radius: 10px;
            padding: 14px 16px;
        }

        .quote .mock-label { color: #4a68c9; letter-spacing: .6px; }

        .quote p { margin: 8px 0 0; font-size: 13px; font-style: italic; color: #344159; line-height: 1.6; }

        .quote-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #eaeff8;
            font-size: 10.5px;
            color: var(--soft);
            flex-wrap: wrap;
        }

        .quote-foot a { color: #2d55c8; font-weight: 650; }

        /* ── Human in the loop ───────────────────────────────── */
        .loop { display: grid; grid-template-columns: repeat(6, 1fr); gap: 14px; }

        .loop-step { padding: 18px 14px; text-align: center; }

        .loop-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #eef3ff;
            color: #4568dc;
            display: grid;
            place-items: center;
            margin: 0 auto 12px;
        }

        .loop-step b { font-size: 11.5px; display: block; }

        .loop-step small { color: var(--soft); font-size: 10px; display: block; margin-top: 4px; line-height: 1.4; }

        .loop-step.emphasis .loop-icon {
            background: linear-gradient(145deg, #4778f5, #244bd4);
            color: #fff;
            box-shadow: 0 8px 18px #315ee833;
        }

        .notice {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-top: 20px;
            background: #eef3ff;
            border: 1px solid #dde6fb;
            border-radius: 12px;
            padding: 14px 17px;
            font-size: 12.5px;
            color: #35435a;
        }

        .notice span:first-child {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            background: #fff;
            color: #2d55c8;
            display: grid;
            place-items: center;
            flex: none;
        }

        /* ── 4-step process ──────────────────────────────────── */
        .process-card { padding: 20px; }

        .process-num { font-size: 11px; font-weight: 800; color: #315ee8; letter-spacing: 1px; }

        .process-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: #eef3ff;
            color: #4568dc;
            display: grid;
            place-items: center;
            margin: 10px 0 14px;
        }

        .process-card h3 { margin: 0; font-size: 15px; }

        .process-card p { margin: 7px 0 0; font-size: 12px; color: var(--muted); }

        .process-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 13px;
            font-size: 10.5px;
            color: #5b6a83;
            background: #f5f7fb;
            border-radius: 20px;
            padding: 5px 11px;
        }

        /* ── Compliance ──────────────────────────────────────── */
        .compliance-card { padding: 22px; }

        .compliance-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            background: linear-gradient(145deg, #eef3ff, #dfe8ff);
            color: #3358cf;
            display: grid;
            place-items: center;
            margin-bottom: 15px;
        }

        .compliance-card h3 { margin: 0; font-size: 15.5px; }

        .compliance-card p { margin: 9px 0 0; font-size: 12.5px; color: var(--muted); }

        .compliance-foot {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-top: 15px;
            font-size: 11px;
            color: #2d55c8;
            font-weight: 650;
        }

        /* ── CTA ─────────────────────────────────────────────── */
        .cta {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            background: linear-gradient(120deg, #172e53, #24477d 60%, #3561a3);
            color: #fff;
            padding: 46px;
        }

        .cta::after {
            content: "";
            position: absolute;
            right: -80px;
            top: -80px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: #ffffff0f;
        }

        .cta-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff17;
            border: 1px solid #ffffff2e;
            border-radius: 30px;
            padding: 7px 14px;
            font-size: 10.5px;
            font-weight: 650;
        }

        .cta h2 {
            position: relative;
            z-index: 1;
            margin: 18px 0 0;
            font-size: 34px;
            letter-spacing: -1.1px;
            line-height: 1.16;
            max-width: 620px;
        }

        .cta p { position: relative; z-index: 1; margin: 14px 0 0; color: #d0dcf2; font-size: 14px; max-width: 520px; }

        .cta-actions { position: relative; z-index: 1; display: flex; flex-wrap: wrap; gap: 12px; margin-top: 26px; }

        .cta-foot {
            position: relative;
            z-index: 1;
            display: flex;
            gap: 22px;
            flex-wrap: wrap;
            margin-top: 22px;
            font-size: 11.5px;
            color: #c3d2ec;
        }

        .cta-foot span { display: inline-flex; align-items: center; gap: 7px; }

        /* ── Footer ──────────────────────────────────────────── */
        .footer {
            border-top: 1px solid var(--line);
            padding: 28px 0;
            background: #fff;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 11.5px;
            color: var(--soft);
        }

        .footer-badges { display: flex; gap: 20px; flex-wrap: wrap; }

        .footer-badges span { display: inline-flex; align-items: center; gap: 7px; color: #6d7a91; }

        /* ── Responsive ──────────────────────────────────────── */
        @media (max-width: 1050px) {
            .nav { display: none; }
            .hamburger { display: grid; }
            .header-right { display: none; }
            .hero-grid { grid-template-columns: 1fr; gap: 34px; }
            .hero h1 { font-size: 40px; }
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .loop { grid-template-columns: repeat(3, 1fr); }

            .nav.open {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                position: absolute;
                top: 64px;
                left: 0;
                right: 0;
                margin: 0;
                padding: 12px 18px 18px;
                background: #fff;
                border-bottom: 1px solid var(--line);
                box-shadow: 0 18px 30px #172e5314;
                gap: 4px;
            }

            .nav.open a { padding: 11px 13px; }
        }

        @media (max-width: 760px) {
            .section { padding: 54px 0; }
            .section-head h2 { font-size: 25px; }
            .hero { padding: 40px 0 24px; }
            .hero h1 { font-size: 33px; letter-spacing: -1.1px; }
            .hero-stats { gap: 22px; flex-wrap: wrap; }
            .grid-4, .grid-3 { grid-template-columns: 1fr; }
            .journey-head { flex-direction: column; align-items: flex-start; }
            .score-panel { grid-template-columns: 1fr; padding: 22px; gap: 24px; }
            .factor-grid { grid-template-columns: 1fr; }
            .loop { grid-template-columns: repeat(2, 1fr); }
            .cta { padding: 30px; }
            .cta h2 { font-size: 26px; }
            .footer-inner { flex-direction: column; align-items: flex-start; }
        }

        @media (max-width: 460px) {
            .wrap { padding: 0 16px; }
            .loop { grid-template-columns: 1fr; }
            .matrix-legend { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="wrap header-inner">
            <a href="{{ route('landing') }}" class="brand" aria-label="InsureMind AI beranda">
                <span class="brand-mark">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 2.8 20 6v5.2c0 5.1-3.4 8.5-8 10-4.6-1.5-8-4.9-8-10V6l8-3.2Z"
                              stroke="currentColor" stroke-width="1.8"/>
                        <path d="m8.5 12 2.2 2.2 4.8-5" stroke="currentColor" stroke-width="1.8"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="brand-name">InsureMind <span>AI</span></span>
            </a>

            <nav class="nav" aria-label="Navigasi utama">
                <a href="{{ route('landing') }}">Landing Page</a>
                <a href="{{ route('dashboard') }}">User Portal</a>
                <a href="#reviewer">Reviewer Center</a>
                <a href="#admin">Admin Console</a>
                <a href="#architecture">System Architecture</a>
            </nav>

            <div class="header-right">
                <div class="lang" role="group" aria-label="Pilih bahasa">
                    <button type="button" class="active">ID</button>
                    <button type="button">EN</button>
                </div>
                <a href="{{ route('dashboard') }}" class="login-link">&#x2192; Admin Login</a>
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Login</a>
            </div>

            <button type="button" class="hamburger" id="navToggle" aria-label="Buka navigasi" aria-expanded="false">
                &#x2630;
            </button>
        </div>
    </header>

    <main>
        <!-- ── Hero ───────────────────────────────────────── -->
        <section class="hero">
            <div class="wrap hero-grid">
                <div class="hero-copy">
                    <span class="pill"><i></i> Algorithmic Precision for Policyholders &amp; Adjusters</span>
                    <h1>Understand Your Policy.<br><em>Prepare Your Claim.</em></h1>
                    <p>
                        AI-powered insurance copilot that helps you understand coverage, assess your claim readiness,
                        and make better-informed decisions.
                    </p>
                    <div class="hero-cta">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Get Started</a>
                        <a href="#how" class="btn btn-ghost">&#x25B7; See How It Works</a>
                    </div>
                    <div class="hero-stats">
                        <div>
                            <b>82%</b>
                            <small>Avg. Claim Readiness</small>
                        </div>
                        <div>
                            <b>&lt; 3 Min</b>
                            <small>Policy Clause Extraction</small>
                        </div>
                        <div>
                            <b>100%</b>
                            <small>Traceable Evidence</small>
                        </div>
                    </div>
                </div>

                <div class="mock" aria-hidden="true">
                    <div class="mock-head">
                        <span class="mock-avatar">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M4 6h11l4 4v8a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1Z"
                                      stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M14 6v5h5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <b>Auto-Comprehensive Gold #A9928</b>
                            <small>PT Asuransi Raya Nusantara &middot; Aktif</small>
                        </div>
                        <span class="tag">&#x25CF; ID-CLAIM-9021</span>
                    </div>

                    <div class="mock-body">
                        <div class="mock-label">Claim Readiness Score</div>
                        <div class="score-row">
                            <div>
                                <div class="score-num">82<small>%</small></div>
                                <div class="score-desc">High likelihood of successful adjudication</div>
                            </div>
                            <div class="ring"><span>82%</span></div>
                        </div>

                        <div class="mock-list">
                            <div class="mock-li">
                                &#x2713; Police Document (e-Police) <span class="chip chip-ok">Verified</span>
                            </div>
                            <div class="mock-li">
                                &#x2713; Nasional ID (KTP Elektronik) <span class="chip chip-warn">Matched</span>
                            </div>
                            <div class="mock-li">
                                &#x2713; Incident Photos (4 Angles) <span class="chip chip-warn">Damage Assessed</span>
                            </div>
                            <div class="alert">
                                &#x26A0; Official Police Report <span class="chip chip-bad">Missing</span>
                            </div>
                        </div>

                        <div class="mock-foot">
                            <span>&#x2733; AI Grounded via Clause 7A8</span>
                            <span class="btn btn-primary btn-sm">Complete Documents</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Industry friction ──────────────────────────── -->
        <section class="section friction" id="friction">
            <div class="wrap">
                <div class="section-head">
                    <div class="eyebrow">Current Industry Friction</div>
                    <h2>Why Claiming Insurance Still Feels Complicated</h2>
                    <p>Over 64% of claim delays and disputes originate from document mismatches and unclear clause interpretation before submission.</p>
                </div>

                <div class="grid-4">
                    <article class="card fraud-card">
                        <div class="fraud-icon">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                <path d="M6 3h7l5 5v13H6z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M13 3v5h5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M9 13h6M9 17h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3>Complex Policies</h3>
                        <p>Dozens of pages of clauses with legal terminology, exclusion tables, endorsements, and ambiguous clauses that confuse even experienced holders.</p>
                        <div class="fraud-foot">&#x2715; 40+ Pages per contract</div>
                    </article>

                    <article class="card fraud-card">
                        <div class="fraud-icon">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M12 7.5v5l3 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3>Unclear Coverage</h3>
                        <p>Uncertainty whether an incident is truly covered or explicitly barred under subtle exclusion triggers and deducible thresholds.</p>
                        <div class="fraud-foot">&#x2715; Sub-limit confusion</div>
                    </article>

                    <article class="card fraud-card">
                        <div class="fraud-icon">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                <rect x="4" y="4" width="16" height="16" rx="3" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <circle cx="17" cy="17" r="3.4" fill="#fff" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </div>
                        <h3>Missing Documents</h3>
                        <p>Filings submitted with incomplete supporting documents, leading to avoidable delays or insufficient photo damage proof that leads to rejections.</p>
                        <div class="fraud-foot">&#x2715; 38% incomplete filings</div>
                    </article>

                    <article class="card fraud-card">
                        <div class="fraud-icon">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                <path d="M12 3.5 20 7v4.5c0 5-3.3 8-8 9.5-4.7-1.5-8-4.5-8-9.5V7l8-3.5Z"
                                      stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M12 9v4M12 15.6v.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3>Uncertain Next Steps</h3>
                        <p>Zero visibility into adjudication timelines, processing schedules, or exactly which adjustments are required to qualify your claim.</p>
                        <div class="fraud-foot">&#x2715; Opaque workflows</div>
                    </article>
                </div>
            </div>
        </section>

        <!-- ── Journey ────────────────────────────────────── -->
        <section class="section" id="journey">
            <div class="wrap">
                <div class="journey-head">
                    <div>
                        <div class="eyebrow">Integrated Intelligence</div>
                        <h2>One Copilot for Your Insurance Journey</h2>
                    </div>
                    <p>From first policy ingestion to final document packaging, InsureMind orchestrates every step with verifiable evidence.</p>
                </div>

                <div class="grid-3">
                    <article class="card step-card">
                        <div class="step-num">01</div>
                        <h3>Understand</h3>
                        <p>AI translates dense policy PDF pages into crystal-clear plain language summaries, structured clauses, deducibles, and deducible tables.</p>
                        <ul class="check-list">
                            <li>Indirect clause synthesis</li>
                            <li>Plain-English &amp; Bahasa summaries</li>
                        </ul>
                    </article>

                    <article class="card step-card">
                        <div class="step-num">02</div>
                        <h3>Check</h3>
                        <p>Pre-adjudicate your scenario against policy criteria with an evidence-grounded Claim Readiness Score before contacting the insurer.</p>
                        <ul class="check-list">
                            <li>Pre-submission discrepancy check</li>
                            <li>Clause-level coverage confidence</li>
                        </ul>
                    </article>

                    <article class="card step-card">
                        <div class="step-num">03</div>
                        <h3>Prepare</h3>
                        <p>Automated document checklists, missing item alerts, photo assessment hints, and structured dossier export tailored for adjuster review.</p>
                        <ul class="check-list">
                            <li>Automated claim pack generation</li>
                            <li>Structured export formats</li>
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        <!-- ── Claim readiness score ──────────────────────── -->
        <section class="section" id="score">
            <div class="wrap">
                <div class="score-panel">
                    <div class="calc-matrix">
                        <div class="calc-head">
                            <div class="mock-label">Live Calculation Matrix</div>
                            <span class="matrix-badge">v2.4 Engine</span>
                        </div>
                        <div class="big-ring">
                            <i>82%<small>Ready to Submit</small></i>
                        </div>
                        <div class="matrix-legend">
                            <div><b>100%</b><small>Policy</small></div>
                            <div><b>90%</b><small>Evidence</small></div>
                            <div><b>82%</b><small>Policy Doc</small></div>
                        </div>
                    </div>

                    <div class="score-copy">
                        <div class="eyebrow">Signature Technology</div>
                        <h2>Eliminate Guesswork with the Claim Readiness Score</h2>
                        <p>
                            InsureMind tests your uploaded evidence against the precise phrasing of your insurer's
                            terms. You receive an algorithmic score, highlighted deficiencies, and step-by-step
                            resolution advice before formal submission.
                        </p>

                        <div class="calc-list">
                            <div class="calc-row">
                                &#x2713; Incident within policy validity period
                                <span class="chip chip-ok">Passed</span>
                            </div>
                            <div class="calc-row">
                                &#x2713; Third-party damage liability checklist
                                <span class="chip chip-ok">Passed</span>
                            </div>
                            <div class="calc-row">
                                &#x25CF; Action required: Upload Police Accident Statement
                                <span class="chip chip-warn">Pending</span>
                            </div>
                        </div>

                        <div class="hero-cta" style="margin-top:20px">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">&#x25CB; Complete Documents</a>
                            <span style="font-size:11px;color:var(--soft);align-self:center">Instantly recalculates readiness when files are added</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Traceable citations ────────────────────────── -->
        <section class="section friction" id="citations">
            <div class="wrap">
                <div class="section-head">
                    <div class="eyebrow">Explainable AI &middot; Traceable Citations</div>
                    <h2>No Hallucinations. Every Assessment Grounded in Real Clauses.</h2>
                    <p>InsureMind produces audit-ready reasoning. We pair every decision suggestion with exact policy coordinates and highlight verbatim text.</p>
                </div>

                <div class="citation-card">
                    <div class="citation-head">
                        <span class="card-icon">&#x2713;</span>
                        <b>ALGORITHMIC DETERMINATION</b>
                        <span class="conf">87% Confidence</span>
                        <span class="model">&#x2733; Model DeepClaim-v4-1D8</span>
                    </div>

                    <div class="factors">
                        <div class="mock-label">Key Underwriting Factors (Why?)</div>
                        <div class="factor-grid">
                            <div class="factor">Flood Damage Included</div>
                            <div class="factor">Policy Currently Active</div>
                            <div class="factor">Included Natural Catastrophe</div>
                        </div>
                    </div>

                    <div class="quote">
                        <div class="mock-label">&#x25A4; Policy Section 4.2 Natural Disaster Coverage</div>
                        <p>
                            "The underwriter agrees to indemnify the insured against sudden flood damage, typhoon water
                            inundation, and storm-related mechanical engine stalls up to the specified aggregate limit of
                            IDR 350,000,000, subject to standard deductible."
                        </p>
                        <div class="quote-foot">
                            <span>Close Similarity: 0.942 against Incident Description</span>
                            <a href="#citations">View Policy Clause &#x2197;</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Human in the loop ──────────────────────────── -->
        <section class="section" id="reviewer">
            <div class="wrap">
                <div class="section-head">
                    <div class="eyebrow">Responsible AI Framework</div>
                    <h2>Human-in-the-Loop Claim Governance</h2>
                    <p>InsureMind assists with assessment and preparation. Final claim decisions remain with authorized human reviewers.</p>
                </div>

                <div class="loop">
                    <div class="card loop-step">
                        <div class="loop-icon">&#x25EB;</div>
                        <b>User</b>
                        <small>Submission &amp; Onboarding</small>
                    </div>
                    <div class="card loop-step">
                        <div class="loop-icon">&#x2733;</div>
                        <b>AI Analysis</b>
                        <small>Parses OCR &amp; idempotent data</small>
                    </div>
                    <div class="card loop-step">
                        <div class="loop-icon">&#x25A4;</div>
                        <b>Evidence</b>
                        <small>Machine-verified clause index</small>
                    </div>
                    <div class="card loop-step">
                        <div class="loop-icon">&#x2713;</div>
                        <b>Recommendation</b>
                        <small>Readiness score &amp; brief</small>
                    </div>
                    <div class="card loop-step emphasis">
                        <div class="loop-icon">&#x2699;</div>
                        <b>Human Review</b>
                        <small>Licensed adjudicator check</small>
                    </div>
                    <div class="card loop-step">
                        <div class="loop-icon">&#x25F7;</div>
                        <b>Final Decision</b>
                        <small>Official claim approval</small>
                    </div>
                </div>

                <div class="notice">
                    <span>&#x2713;</span>
                    <span>InsureMind acts purely as an assistive copilot. The system explicitly restricts autonomous payout triggers. All financial resolutions defer to statutory reviewer sign-off workflows.</span>
                </div>
            </div>
        </section>

        <!-- ── How it works ───────────────────────────────── -->
        <section class="section" id="how">
            <div class="wrap">
                <div class="section-head">
                    <div class="eyebrow">Seamless 4-Step Process</div>
                    <h2>How InsureMind Works</h2>
                    <p>Go from an ambiguous insurance paper to a verified, complete claim pack in four straightforward steps.</p>
                </div>

                <div class="grid-4">
                    <article class="card process-card">
                        <div class="process-num">01</div>
                        <div class="process-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 15V4M12 4 8 8M12 4l4 4" stroke="currentColor" stroke-width="1.7"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3" stroke="currentColor"
                                      stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3>Upload</h3>
                        <p>Upload your insurance document. InsureMind parses PDFs, extracts key data, and structures all critical information for analysis.</p>
                        <span class="process-tag">&#x25A4; Client-side sanitized</span>
                    </article>

                    <article class="card process-card">
                        <div class="process-num">02</div>
                        <div class="process-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M6 3h7l5 5v13H6z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M13 3v5h5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3>Understand</h3>
                        <p>Get an instant structured overview. Learn about your coverage, deductibles, limits, and explicit exclusions instantly.</p>
                        <span class="process-tag">&#x25A4; Clause index generated</span>
                    </article>

                    <article class="card process-card">
                        <div class="process-num">03</div>
                        <div class="process-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M5 12.5 10 17l9-9.5" stroke="currentColor" stroke-width="1.8"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3>Check</h3>
                        <p>Assess claim validity with our Claim Readiness Score, document readiness, and confidence indications up to pre-adjudication.</p>
                        <span class="process-tag">&#x25CB; Instant 0-100% score</span>
                    </article>

                    <article class="card process-card">
                        <div class="process-num">04</div>
                        <div class="process-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 3 20 6.5v5c0 5-3.4 8.2-8 9.5-4.6-1.3-8-4.5-8-9.5v-5L12 3Z"
                                      stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="m8.5 12 2.3 2.3 4.7-5" stroke="currentColor" stroke-width="1.7"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3>Prepare</h3>
                        <p>Compile and download a complete, submission-ready claim dossier aligned to adjudicator verification standards.</p>
                        <span class="process-tag">&#x2193; PDF Dossier Export</span>
                    </article>
                </div>
            </div>
        </section>

        <!-- ── Compliance ─────────────────────────────────── -->
        <section class="section friction" id="admin">
            <div class="wrap">
                <div class="section-head">
                    <div class="eyebrow">Regulatory Integrity</div>
                    <h2>Designed with OJK &amp; PDP Compliance Considerations</h2>
                    <p>Architected from the ground up to respect Indonesia&rsquo;s Personal Data Protection (UU PDP) law and alignment with OJK fintech cybersecurity standards.</p>
                </div>

                <div class="grid-3">
                    <article class="card compliance-card">
                        <div class="compliance-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <rect x="5" y="10" width="14" height="10" rx="2.5" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M8.5 10V8a3.5 3.5 0 0 1 7 0v2" stroke="currentColor" stroke-width="1.7"
                                      stroke-linecap="round"/>
                                <path d="M12 14v2.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3>UU PDP Data Privacy</h3>
                        <p>Granular user consent controls, automatic PII scrubbing on uploaded IDs, cards, and login history, plus platform-level zero-trust data privacy assurance on-demand clause actions.</p>
                        <span class="compliance-foot">&#x25CE; Local-Jurisdiction Detection</span>
                    </article>

                    <article class="card compliance-card">
                        <div class="compliance-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M12 3 20 6.5v5c0 5-3.4 8.2-8 9.5-4.6-1.3-8-4.5-8-9.5v-5L12 3Z"
                                      stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M12 10v3M12 15.4v.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3>Enterprise Security</h3>
                        <p>256-bit AES encryption at rest, TLS 1.3 transit encryption, and continuous authenticated audit trails with automatic vulnerability reviews.</p>
                        <span class="compliance-foot">&#x25CE; ISO 27001 Aligned Controls</span>
                    </article>

                    <article class="card compliance-card">
                        <div class="compliance-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M6 4h9l4 4v12H6z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M15 4v4h4" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3>Compliance-Aware System</h3>
                        <p>Explicit assistive classification. We avoid autonomous underwriting binding, preserving OJK mandated insurer accountability.</p>
                        <span class="compliance-foot">&#x25CB; Non-Adjudicating AI Tool</span>
                    </article>
                </div>
            </div>
        </section>

        <!-- ── CTA ────────────────────────────────────────── -->
        <section class="section" id="architecture">
            <div class="wrap">
                <div class="cta">
                    <span class="cta-tag">&#x2733; Industry Data Protection</span>
                    <h2>Take Control of Your Insurance Coverage Today</h2>
                    <p>Upload your policy in less than 60 seconds and experience the clarity of evidence-grounded AI guidance.</p>
                    <div class="cta-actions">
                        <a href="{{ route('dashboard') }}" class="btn btn-light">&#x2191; Get Started Now</a>
                        <a href="#how" class="btn btn-outline-light">&#x25B7; Explore Documentation</a>
                    </div>
                    <div class="cta-foot">
                        <span>&#x25CB; No credit card required</span>
                        <span>&#x2733; Instant OCR Extraction</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="wrap footer-inner">
            <span>Designed with OJK &amp; PDP Compliance Considerations</span>
            <div class="footer-badges">
                <span>&#x25A4; 256-AES</span>
                <span>&#x25CE; ISO 27001</span>
                <span>&#x2713; PDP Protected</span>
            </div>
            <span>&copy; {{ date('Y') }} InsureMind AI. All rights reserved.</span>
        </div>
    </footer>

    <script>
        // ── Mobile navigation toggle ──────────────────────────────────────
        const navToggle = document.getElementById('navToggle');
        const navMenu   = document.querySelector('.nav');

        navToggle.addEventListener('click', () => {
            const open = navToggle.getAttribute('aria-expanded') !== 'true';
            navToggle.setAttribute('aria-expanded', String(open));
            navToggle.classList.toggle('open', open);
            navMenu.classList.toggle('open', open);
        });

        document.querySelectorAll('.nav a').forEach(link => {
            link.addEventListener('click', () => {
                navToggle.setAttribute('aria-expanded', 'false');
                navToggle.classList.remove('open');
                navMenu.classList.remove('open');
            });
        });
    </script>
</body>

</html>
