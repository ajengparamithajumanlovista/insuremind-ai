<!DOCTYPE html>
<html lang="id" id="htmlRoot">

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
            --blue: #3b5fe3;
            --blue-dark: #2949bd;
            --pale: #f7f8fc;
            --navy: #172e53;
            --green: #159b75;
            --green-dark: #087b5b;
            --green-bg: #e5f7ef;
            --orange: #e99a28;
            --orange-dark: #bd7110;
            --orange-bg: #fff3df;
            --purple: #8b63d9;
            --purple-dark: #6944b5;
            --purple-bg: #f0eaff;
            --teal: #149ca5;
            --teal-dark: #087b82;
            --teal-bg: #e2f7f6;
            --pink: #df6688;
            --pink-dark: #b84468;
            --pink-bg: #ffedf2;
        }

        /* ── Reset ───────────────────────────────────────────── */
        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            background:
                radial-gradient(ellipse at 8% 8%, #e6f6f3 0, transparent 28rem),
                radial-gradient(ellipse at 94% 22%, #f0eaff 0, transparent 30rem),
                var(--pale);
            color: var(--ink);
            font: 14px/1.5 Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        img, svg { display: block; }

        a { color: inherit; text-decoration: none; }

        button { font: inherit; cursor: pointer; }

        /* ── Keyframes ───────────────────────────────────────── */
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        @keyframes fadeInScale {
            0% { opacity: 0; transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(2deg); }
        }
        @keyframes pulseSoft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes glowPulse {
            0%, 100% { box-shadow: 0 0 0 0px #315ee866; }
            50% { box-shadow: 0 0 0 8px #315ee822; }
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes drawLine {
            0% { stroke-dashoffset: 100; }
            100% { stroke-dashoffset: 0; }
        }
        @keyframes shim {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes drift {
            0%, 100% { transform: translate3d(0, 0, 0); }
            50% { transform: translate3d(12px, -16px, 0); }
        }

        /* ── Animation utility classes ─────────────────────────── */
        .animate-fade-up { animation: fadeInUp .7s ease-out both; }
        .animate-fade { animation: fadeIn .6s ease-out both; }
        .animate-fade-scale { animation: fadeInScale .6s cubic-bezier(.25,.4,.25,1) both; }
        .animate-float { animation: float 4s ease-in-out infinite; }
        .animate-float-slow { animation: floatSlow 6s ease-in-out infinite; }
        .animate-pulse-soft { animation: pulseSoft 2s ease-in-out infinite; }
        .animate-gradient { background-size: 200% 200%; animation: gradientShift 8s ease infinite; }
        .animate-glow { animation: glowPulse 2.5s ease-in-out infinite; }
        .animate-spin-slow { animation: spin 12s linear infinite; }
        .animate-stagger-1 { animation-delay: 0.1s; }
        .animate-stagger-2 { animation-delay: 0.2s; }
        .animate-stagger-3 { animation-delay: 0.3s; }
        .animate-stagger-4 { animation-delay: 0.4s; }
        .animate-stagger-5 { animation-delay: 0.5s; }
        .animate-stagger-6 { animation-delay: 0.6s; }

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

        /* ── Icon consistency ────────────────────────────────── */
        .btn > svg,
        .svc-foot a > svg,
        .quote-foot a > svg,
        .mock-foot a > svg,
        .process-tag > svg,
        .compliance-foot > svg,
        .cta-foot > span > svg,
        .footer-badges > span > svg,
        .notice > span > svg {
            flex: none;
        }

        .svc-foot a svg,
        .quote-foot a svg,
        .btn svg {
            transition: transform .32s cubic-bezier(.2,.7,.25,1);
        }

        .btn:hover > svg,
        .svc-foot a:hover > svg { transform: translateX(2px); }

        .quote-foot a:hover > svg { transform: translate(2px, -2px); }

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
            transition: transform .32s cubic-bezier(.2,.7,.25,1), box-shadow .32s ease, background-color .32s ease, border-color .32s ease, color .32s ease;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(120deg, var(--blue), var(--purple), var(--teal));
            background-size: 200% 200%;
            color: #fff;
            box-shadow: 0 10px 22px #3b5fe833;
            transition: transform .32s cubic-bezier(.2,.7,.25,1), box-shadow .32s ease, background-color .32s ease, border-color .32s ease, color .32s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px #3b5fe82b;
        }

        .btn:hover { transform: translateY(-1px); }

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
            width: 38px;
            height: 38px;
            border-radius: 12px;
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

        .brand-wordmark { flex: none; display: block; }

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
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 30px;
        }

        .hero-stat {
            position: relative;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px 15px 15px;
            box-shadow: 0 12px 26px #172e530d;
            overflow: hidden;
            transition: transform .32s cubic-bezier(.2,.7,.25,1), box-shadow .32s ease, border-color .32s ease;
        }

        .hero-stat::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--blue), #7fa2ff);
        }

        .hero-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 34px #172e5317;
            border-color: #c9d8f5;
        }

        .hero-stat .stat-ico {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: #eef3ff;
            color: #4568dc;
            margin-bottom: 9px;
        }

        .hero-stats b {
            display: block;
            font-size: 23px;
            letter-spacing: -.8px;
            color: #16233a;
        }

        .hero-stats small { display: block; margin-top: 2px; color: var(--soft); font-size: 10.5px; line-height: 1.35; }

        /* ── Mock card ───────────────────────────────────────── */
        .mock {
            position: relative;
            background: #fff;
            border: 1px solid #e7ecf7;
            border-radius: 20px;
            box-shadow:
                0 2px 4px #172e530a,
                0 26px 60px #172e531a;
            overflow: hidden;
        }

        .mock::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 3px;
            background: linear-gradient(90deg, #4f7ef8, #16a29a 45%, #8b5cf6 80%, #e79a1f);
            opacity: .9;
        }

        .mock-head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 17px 18px;
            border-bottom: 1px solid var(--line);
            background:
                radial-gradient(circle at 0% 0%, #eef3ff 0, transparent 12rem),
                linear-gradient(180deg, #fcfdff, #f7f9ff);
        }

        .mock-avatar {
            width: 38px;
            height: 38px;
            border-radius: 13px;
            background: linear-gradient(145deg, #5f8bfb, #2d54d8);
            color: #fff;
            display: grid;
            place-items: center;
            flex: none;
            box-shadow: 0 7px 16px #2d54d83d, inset 0 0 0 1px #ffffff33;
        }

        .mock-head b { display: block; font-size: 12.5px; color: #16233a; letter-spacing: -.1px; }

        .mock-head small { color: var(--soft); font-size: 10.5px; }

        .tag {
            margin-left: auto;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .4px;
            border-radius: 999px;
            padding: 5px 11px;
            background: linear-gradient(145deg, #eef3ff, #e2eaff);
            color: #3355c9;
            box-shadow: inset 0 0 0 1px #ffffffb3;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .tag-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex: none;
            background: linear-gradient(145deg, #5f8bfb, #2d54d8);
            box-shadow: 0 0 0 2px #ffffffcc, 0 0 0 4px #2d54d81f;
        }

        .mock-body { padding: 17px; }

        .mock-label {
            font-size: 10px;
            font-weight: 750;
            letter-spacing: 1px;
            color: #9aa4b6;
            text-transform: uppercase;
        }

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

        /* ── Hero services visual (vector) ───────────────────── */
        .svc-viz {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 12px;
        }

        .svc-tile {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            transition: border-color .32s ease, box-shadow .32s ease, transform .32s cubic-bezier(.2,.7,.25,1);
        }

        .svc-tile:hover {
            border-color: #c9d8f5;
            box-shadow: 0 6px 16px #172e5310;
            transform: translateY(-2px);
        }

        .svc-glyph {
            position: relative;
            width: 38px;
            height: 38px;
            flex: none;
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: #3d61de;
            background: linear-gradient(150deg, #eff4ff, #e0e9fd);
            box-shadow: inset 0 0 0 1px #ffffffcc;
        }

        .svc-glyph::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(150deg, #ffffffb8, transparent 58%);
            pointer-events: none;
        }

        .svc-glyph svg { position: relative; z-index: 1; }
        .svc-glyph svg [stroke] { stroke-width: 1.85; stroke-linecap: round; stroke-linejoin: round; }

        .svc-glyph.g-ok { color: #128a55; background: linear-gradient(150deg, #e6f9ef, #d6f0e3); }
        .svc-glyph.g-ink { color: #3c4a68; background: linear-gradient(150deg, #f2f5fb, #e3e9f5); }
        .svc-glyph.g-purple { color: #7a49e0; background: linear-gradient(150deg, #f2ecff, #e6dbff); }
        .svc-glyph.g-teal { color: #0e8f87; background: linear-gradient(150deg, #e6faf8, #d3f2ef); }
        .svc-glyph.g-amber { color: #a9700d; background: linear-gradient(150deg, #fdf5e5, #f8ead1); }
        .svc-glyph.g-purple-soft { color: #6a5acd; background: linear-gradient(150deg, #f1eeff, #e8e2fb); }

        /* Accent gradients reused by the small stat / metric icons and badges */
        .grad-blue { background: linear-gradient(145deg, #5f8bfb, #2d54d8); box-shadow: 0 5px 12px #2d54d83d; }
        .grad-teal { background: linear-gradient(145deg, #4fd0c3, #16a29a); box-shadow: 0 5px 12px #16a29a3d; }
        .grad-purple { background: linear-gradient(145deg, #a98bf6, #7a52e4); box-shadow: 0 5px 12px #7a52e43d; }

        .svc-tile b { display: block; font-size: 11.5px; color: #2b3a55; letter-spacing: -.1px; }
        .svc-tile small { color: var(--soft); font-size: 9.5px; }

        /* ── Hero service-suite pipeline illustration ────────── */
        .suite-viz {
            display: block;
            width: 100%;
            height: auto;
            margin-top: 12px;
            border: 1px solid #e8eefa;
            border-radius: 15px;
            background:
                radial-gradient(circle at 12% 8%, #eef3ff 0, transparent 9rem),
                radial-gradient(circle at 88% 92%, #f1ecff 0, transparent 9rem),
                linear-gradient(160deg, #fbfdff, #f5f8ff);
        }

        .suite-viz .sv-g { transform-box: fill-box; transform-origin: center; }

        .suite-viz .sv-g1 { animation: imFloat1 5.4s ease-in-out infinite; }
        .suite-viz .sv-g2 { animation: imFloat2 5.4s ease-in-out .25s infinite; }
        .suite-viz .sv-g3 { animation: imFloat3 5.4s ease-in-out .5s infinite; }
        .suite-viz .sv-g4 { animation: imFloat4 5.4s ease-in-out .75s infinite; }
        .suite-viz .sv-g5 { animation: imFloat5 5.4s ease-in-out 1s infinite; }
        .suite-viz .sv-g6 { animation: imFloat3 6.2s ease-in-out 1.2s infinite; }

        @keyframes imFloat1 { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }
        @keyframes imFloat2 { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
        @keyframes imFloat3 { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-3.5px); } }
        @keyframes imFloat4 { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
        @keyframes imFloat5 { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }

        @media (prefers-reduced-motion: reduce) {
            .suite-viz .sv-g { animation: none; }
        }

        .mock-metas {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 14px;
        }

        .meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 11px;
            border-radius: 999px;
            border: 1px solid #e9eef8;
            background: #f8faff;
            color: #4b5a75;
            font-size: 9.5px;
            font-weight: 650;
            letter-spacing: .2px;
        }

        .meta-chip i {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex: none;
            background: linear-gradient(145deg, #5f8bfb, #2d54d8);
        }

        .meta-chip.teal i { background: linear-gradient(145deg, #4fd0c3, #16a29a); }
        .meta-chip.purple i { background: linear-gradient(145deg, #a98bf6, #7a52e4); }
        .meta-chip.amber i { background: linear-gradient(145deg, #ffc46b, #e79a1f); }

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

        /* ── Services showcase (sistem InsureMind) ──────────── */
        .svc-panel {
            position: relative;
            overflow: hidden;
            background: linear-gradient(160deg, #f7faff 0%, #eef3fd 100%);
            border: 1px solid #e3eafa;
            border-radius: 16px;
            padding: 20px;
        }

        .svc-panel::after {
            content: "";
            position: absolute;
            right: -60px;
            top: -60px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, #315ee81f, transparent 70%);
            pointer-events: none;
        }

        .svc-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .svc-live {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            background: #e7f7ee;
            color: #1f8a55;
            border-radius: 999px;
            padding: 4px 9px;
        }

        .svc-live i {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22a863;
            box-shadow: 0 0 0 3px #22a86333;
        }

        .svc-steps {
            position: relative;
            display: grid;
            gap: 10px;
        }

        .svc-steps::before {
            content: "";
            position: absolute;
            left: 21px;
            top: 22px;
            bottom: 22px;
            width: 2px;
            background: linear-gradient(180deg, #c9d8fb, #dfe7fa);
            border-radius: 2px;
        }

        .svc-step {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid #e6ecf9;
            border-radius: 12px;
            padding: 11px 13px;
            box-shadow: 0 6px 16px #172e530b;
            transition: transform .32s cubic-bezier(.2,.7,.25,1), box-shadow .32s ease, border-color .32s ease;
        }

        .svc-step:hover {
            transform: translateY(-2px);
            border-color: #ccdaf6;
            box-shadow: 0 10px 22px #172e5312;
        }

        .svc-icon {
            position: relative;
            z-index: 1;
            flex: none;
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            background: linear-gradient(150deg, #e8efff, #d9e4fd);
            color: #3157c8;
        }

        .svc-step.is-key .svc-icon {
            background: linear-gradient(150deg, #4778f5, #244bd4);
            color: #fff;
            box-shadow: 0 8px 18px #315ee83d;
        }

        .svc-copy { min-width: 0; }

        .svc-copy b {
            display: block;
            font-size: 12.5px;
            letter-spacing: -.1px;
            color: #1d2b45;
        }

        .svc-copy small {
            display: block;
            margin-top: 2px;
            font-size: 10.5px;
            color: var(--muted);
            line-height: 1.4;
        }

        .svc-state {
            margin-left: auto;
            flex: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .2px;
            border-radius: 999px;
            padding: 4px 10px;
            background: #eef3ff;
            color: #3f5fc4;
            box-shadow: inset 0 0 0 1px #ffffffb3;
        }

        .svc-state.ok { background: #e7f7ee; color: #1f8a55; }
        .svc-state.key { background: #efeaff; color: #6640ce; }

        .svc-state .svc-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex: none;
            background: linear-gradient(145deg, #5f8bfb, #2d54d8);
            box-shadow: 0 0 0 2px #ffffffb3;
        }

        .svc-state.ok .svc-dot { background: linear-gradient(145deg, #57d69b, #1c9a62); }
        .svc-state.key .svc-dot { background: linear-gradient(145deg, #a98bf6, #7a52e4); }

        .svc-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px dashed #dbe3f3;
            font-size: 10.5px;
            color: var(--soft);
        }

        .svc-foot b { color: #2d55c8; }

        .score-copy h2 { margin: 10px 0 0; font-size: 30px; letter-spacing: -.9px; line-height: 1.2; }

        .score-copy > p { margin: 12px 0 0; color: var(--muted); font-size: 13.5px; }

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
            background: linear-gradient(145deg, #f0f4ff, #e6ecff);
            color: #4568dc;
            display: grid;
            place-items: center;
            flex: none;
            box-shadow: inset 0 0 0 1px #ffffffcc;
        }

        .citation-head b { font-size: 13px; }

        .conf { font-size: 10px; font-weight: 700; background: #e7f7ee; color: #1f8a55; border-radius: 20px; padding: 3px 10px; }

        .citation-head .model {
            margin-left: auto;
            font-size: 10.5px;
            color: var(--soft);
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

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

        .quote-foot a {
            color: #2d55c8;
            font-weight: 650;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* ── Human in the loop ───────────────────────────────── */
        .loop { display: grid; grid-template-columns: repeat(6, 1fr); gap: 14px; }

        .loop-step { padding: 18px 14px; text-align: center; }

        .loop-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(145deg, #f0f4ff, #e6ecff);
            color: #4568dc;
            display: grid;
            place-items: center;
            margin: 0 auto 12px;
            box-shadow: inset 0 0 0 1px #ffffffcc, 0 5px 12px #315ee81a;
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

        .cta-visual {
            position: relative;
            z-index: 1;
            display: block;
            width: 100%;
            max-width: 360px;
            height: auto;
            margin-top: 30px;
            overflow: visible;
        }

        .cv-node { transform-box: fill-box; transform-origin: center; animation: cvPulse 4.8s ease-in-out infinite; }
        .cv-n1 { animation-delay: 0s; }
        .cv-n2 { animation-delay: .35s; }
        .cv-n3 { animation-delay: .7s; }
        .cv-n4 { animation-delay: 1.05s; }
        .cv-n5 { animation-delay: 1.4s; }

        @keyframes cvPulse {
            0%, 100% { opacity: .82; }
            42% { opacity: 1; }
        }

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

        /* ── Color and motion polish ─────────────────────────── */
        .hero {
            position: relative;
            isolation: isolate;
        }

        .hero::before {
            content: "";
            position: absolute;
            z-index: -1;
            inset: -50px 0 0;
            background:
                radial-gradient(ellipse at 78% 38%, #dff5f1 0, transparent 24rem),
                radial-gradient(ellipse at 96% 4%, #eee7ff 0, transparent 19rem),
                radial-gradient(ellipse at 8% 72%, #fff0e4 0, transparent 17rem);
            pointer-events: none;
        }

        .hero .wrap { position: relative; }

        .hero h1 em {
            background: linear-gradient(100deg, var(--blue) 4%, var(--purple) 48%, var(--teal) 94%);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }

        .hero .pill {
            background: linear-gradient(100deg, #e9efff, #f1eaff 55%, #e5f8f4);
            color: #4354a0;
        }

        .hero .pill i {
            background: var(--teal);
            box-shadow: 0 0 0 4px #149ca526;
        }

        .hero-stat:nth-child(3n + 1)::before { background: linear-gradient(180deg, var(--blue), #8b9cf0); }
        .hero-stat:nth-child(3n + 2)::before { background: linear-gradient(180deg, var(--teal), #75cec6); }
        .hero-stat:nth-child(3n + 3)::before { background: linear-gradient(180deg, var(--purple), #bd9ce9); }
        .hero-stat:nth-child(3n + 1) .stat-ico { color: #fff; background: linear-gradient(145deg, #5f8bfb, #2d54d8); box-shadow: 0 5px 12px #2d54d83d; }
        .hero-stat:nth-child(3n + 2) .stat-ico { color: #fff; background: linear-gradient(145deg, #4fd0c3, #16a29a); box-shadow: 0 5px 12px #16a29a3d; }
        .hero-stat:nth-child(3n + 3) .stat-ico { color: #fff; background: linear-gradient(145deg, #a98bf6, #7a52e4); box-shadow: 0 5px 12px #7a52e43d; }

        .brand-mark {
            background: linear-gradient(145deg, #4778f5, #244bd4);
            box-shadow: 0 8px 18px #3b5fe838, inset 0 0 0 1px #ffffff2e;
        }

        .section-head .eyebrow,
        .journey-head .eyebrow,
        .score-copy .eyebrow {
            color: var(--teal-dark);
        }

        .friction {
            background:
                radial-gradient(ellipse at 0% 0%, #fff0e5 0, transparent 26rem),
                radial-gradient(ellipse at 100% 100%, #e8e9ff 0, transparent 28rem),
                linear-gradient(180deg, #f7f8fc 0%, #f0f4f9 100%);
        }

        .card,
        .citation-card,
        .score-panel,
        .hero-stat,
        .mock {
            transition: transform .28s cubic-bezier(.2,.75,.25,1), box-shadow .28s ease, border-color .28s ease;
        }

        .card:hover,
        .citation-card:hover,
        .score-panel:hover,
        .hero-stat:hover {
            transform: translateY(-3px);
            border-color: #d5dbee;
            box-shadow: 0 20px 42px #25385b16;
        }

        .fraud-card:nth-child(4n + 1) .fraud-icon,
        .step-card:nth-child(3n + 1) .step-num,
        .process-card:nth-child(4n + 1) .process-icon,
        .compliance-card:nth-child(3n + 1) .compliance-icon {
            color: var(--blue-dark);
            background: linear-gradient(145deg, #e8edff, #dfe6ff);
        }

        .fraud-card:nth-child(4n + 2) .fraud-icon,
        .step-card:nth-child(3n + 2) .step-num,
        .process-card:nth-child(4n + 2) .process-icon,
        .compliance-card:nth-child(3n + 2) .compliance-icon {
            color: var(--teal-dark);
            background: linear-gradient(145deg, #e2f7f6, #d3f0ee);
        }

        .fraud-card:nth-child(4n + 3) .fraud-icon,
        .step-card:nth-child(3n + 3) .step-num,
        .process-card:nth-child(4n + 3) .process-icon,
        .compliance-card:nth-child(3n + 3) .compliance-icon {
            color: var(--purple-dark);
            background: linear-gradient(145deg, #f0eaff, #e7ddff);
        }

        .fraud-card:nth-child(4n) .fraud-icon,
        .process-card:nth-child(4n) .process-icon {
            color: var(--orange-dark);
            background: linear-gradient(145deg, #fff3df, #ffebcc);
        }

        .fraud-card,
        .step-card,
        .process-card,
        .compliance-card {
            position: relative;
            overflow: hidden;
        }

        .fraud-card::after,
        .step-card::after,
        .process-card::after,
        .compliance-card::after {
            content: "";
            position: absolute;
            inset: auto 18px 0;
            height: 2px;
            border-radius: 2px 2px 0 0;
            background: linear-gradient(90deg, var(--blue), var(--purple), var(--teal));
            opacity: 0;
            transform: scaleX(.55);
            transition: opacity .25s ease, transform .25s ease;
        }

        .fraud-card:hover::after,
        .step-card:hover::after,
        .process-card:hover::after,
        .compliance-card:hover::after {
            opacity: 1;
            transform: scaleX(1);
        }

        .svc-panel {
            background:
                radial-gradient(circle at 100% 0%, #e9e2ff 0, transparent 15rem),
                linear-gradient(145deg, #f2f9fb, #f7f6ff 62%, #fff8ef);
            border-color: #e5e8f2;
        }

        .svc-step:nth-child(3n + 1) .svc-icon {
            background: linear-gradient(145deg, #e8edff, #dfe6ff);
            color: var(--blue-dark);
        }

        .svc-step:nth-child(3n + 2) .svc-icon {
            background: linear-gradient(145deg, #e2f7f6, #d3f0ee);
            color: var(--teal-dark);
        }

        .svc-step:nth-child(3n) .svc-icon {
            background: linear-gradient(145deg, #f0eaff, #e7ddff);
            color: var(--purple-dark);
        }

        .svc-step.is-key .svc-icon {
            background: linear-gradient(135deg, var(--blue), var(--purple), var(--teal));
            color: #fff;
        }

        .loop-step:nth-child(3n + 1) .loop-icon { color: var(--blue-dark); background: #e8edff; }
        .loop-step:nth-child(3n + 2) .loop-icon { color: var(--teal-dark); background: #e2f7f6; }
        .loop-step:nth-child(3n) .loop-icon { color: var(--purple-dark); background: #f0eaff; }

        .loop-step.emphasis .loop-icon {
            color: #fff;
            background: linear-gradient(135deg, var(--blue), var(--purple), var(--teal));
        }

        .loop-icon,
        .fraud-icon,
        .step-num,
        .process-icon,
        .compliance-icon,
        .svc-glyph {
            transition: transform .28s ease, box-shadow .28s ease, background .28s ease;
        }

        .card:hover .fraud-icon,
        .card:hover .step-num,
        .card:hover .process-icon,
        .card:hover .compliance-icon,
        .loop-step:hover .loop-icon,
        .svc-tile:hover .svc-glyph {
            transform: scale(1.04);
            box-shadow: 0 5px 12px #26395b14;
        }

        .quote {
            background: linear-gradient(110deg, #f1f4ff, #effaf8 72%, #fff8ee);
            border-left-color: var(--purple);
        }

        .notice {
            background: linear-gradient(100deg, #edf1ff, #f3edff 52%, #eaf8f5);
            border-color: #e2e7f3;
        }

        .cta { background: linear-gradient(115deg, #253d74, #504b91 48%, #177f83); }

        .cta::after {
            background: radial-gradient(circle, #ffffff24 0, #ffffff0d 50%, transparent 72%);
        }

        .btn,
        .nav a,
        .lang button,
        .svc-step,
        .svc-tile {
            transition: color .32s ease, background-color .32s ease, border-color .32s ease, box-shadow .32s ease, transform .32s cubic-bezier(.2,.7,.25,1);
        }

        .btn:focus-visible,
        .nav a:focus-visible,
        .lang button:focus-visible,
        .hamburger:focus-visible {
            outline: 3px solid #8b63d966;
            outline-offset: 3px;
        }

        .reveal-on-scroll {
            transition: opacity .55s ease var(--reveal-delay, 0ms), transform .55s cubic-bezier(.2,.7,.25,1) var(--reveal-delay, 0ms);
        }

        .motion-ready .reveal-on-scroll:not(.is-visible) {
            opacity: 0;
            transform: translateY(22px);
        }

        .motion-ready .reveal-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }

            .motion-ready .reveal-on-scroll:not(.is-visible) {
                opacity: 1;
                transform: none;
            }

            .svc-step,
            .svc-tile,
            .cv-node {
                animation: none !important;
                opacity: 1 !important;
            }
        }

        /* ── Responsive ──────────────────────────────────────── */
        @media (max-width: 1050px) {
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
            .hero-stats { grid-template-columns: 1fr; gap: 10px; }
            .grid-4, .grid-3 { grid-template-columns: 1fr; }
            .journey-head { flex-direction: column; align-items: flex-start; }
            .score-panel { grid-template-columns: 1fr; padding: 22px; gap: 24px; }
            .factor-grid { grid-template-columns: 1fr; }
            .loop { grid-template-columns: repeat(2, 1fr); }
            .cta { padding: 30px; }
            .cta h2 { font-size: 26px; }
            .footer-inner { flex-direction: column; align-items: flex-start; }
            .svc-viz { gap: 8px; }
            .svc-tile { padding: 10px; gap: 9px; }
        }

        @media (max-width: 460px) {
            .wrap { padding: 0 16px; }
            .loop { grid-template-columns: 1fr; }
            .svc-panel { padding: 15px; }
            .svc-copy small { display: none; }
            .svc-foot { flex-direction: column; align-items: flex-start; }
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
    <header class="header">
        <div class="wrap header-inner">
                        <a href="{{ route('landing') }}" class="brand"
               data-id-aria="InsureMind AI beranda" data-en-aria="InsureMind AI home"
               aria-label="InsureMind AI beranda">
                <span class="brand-mark">
                    <svg width="23" height="23" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                        <g fill="#ffffff" opacity=".7">
                            <circle cx="5.6" cy="5.6" r="1.5"/>
                            <circle cx="14" cy="5.6" r="1.5"/>
                            <circle cx="22.4" cy="5.6" r="1.5"/>
                            <circle cx="5.6" cy="22.4" r="1.5"/>
                            <circle cx="14" cy="22.4" r="1.5"/>
                            <circle cx="22.4" cy="22.4" r="1.5"/>
                        </g>
                        <path d="M14 7.2 22 10v4.6c0 4.6-3.1 7.9-8 9.4-4.9-1.5-8-4.8-8-9.4V10l8-2.8Z"
                              stroke="#ffffff" stroke-width="1.6" stroke-linejoin="round"/>
                        <path d="m10.6 15.6 2.5 2.5 5.2-5.7" stroke="#ffffff" stroke-width="1.9"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                        <svg class="brand-wordmark" width="22" height="22" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                            <path d="M14 2.8 24 6.6v6.6c0 6.1-4.2 10.4-10 11.9C8.2 23.6 4 19.3 4 13.2V6.6L14 2.8Z"
                                  fill="#eef3ff" stroke="#2d54d8" stroke-width="1.7" stroke-linejoin="round"/>
                            <path d="m9.6 13.9 3.1 3.1 6.1-6.6" stroke="#2d54d8" stroke-width="2.3"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                <span class="brand-name">InsureMind <span>AI</span></span>
            </a>

                        <nav class="nav" aria-label="Navigasi utama"
                 data-id-aria="Navigasi utama" data-en-aria="Main navigation">
                <a href="{{ route('dashboard') }}" data-id="Portal Pengguna" data-en="User Portal">User Portal</a>
                <a href="#reviewer" data-id="Pusat Reviewer" data-en="Reviewer Center">Reviewer Center</a>
                <a href="#admin" data-id="Konsol Admin" data-en="Admin Console">Admin Console</a>
                <a href="#architecture" data-id="Arsitektur Sistem" data-en="System Architecture">System Architecture</a>
            </nav>

            <div class="header-right">
                                <div class="lang" role="group" aria-label="Pilih bahasa"
                     data-id-aria="Pilih bahasa" data-en-aria="Choose language">
                    <button type="button" id="btnLangID" class="active" onclick="setLang('id')">ID</button>
                    <button type="button" id="btnLangEN" onclick="setLang('en')">EN</button>
                </div>
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm" data-id="Login" data-en="Login">Login</a>
            </div>

                        <button type="button" class="hamburger" id="navToggle"
                    aria-label="Buka navigasi"
                    data-id-aria="Buka navigasi" data-en-aria="Open navigation"
                    aria-expanded="false">
                &#x2630;
            </button>
        </div>
    </header>

    <main>
        <!-- ── Hero ───────────────────────────────────────── -->
        <section class="hero">
            <div class="wrap hero-grid">
                <div class="hero-copy">
                    <span class="pill"><i></i> <span data-id="Ketepatan Algoritmik untuk Pemegang Polis &amp; Adjuster" data-en="Algorithmic Precision for Policyholders &amp; Adjusters">Ketepatan Algoritmik untuk Pemegang Polis &amp; Adjuster</span></span>
                    <h1><span data-id="Pahami Polis Anda." data-en="Understand Your Policy.">Pahami Polis Anda.</span><br><em><span data-id="Siapkan Klaim Anda." data-en="Prepare Your Claim.">Siapkan Klaim Anda.</span></em></h1>
                    <p data-id="Kopilot asuransi berbasis AI yang membantu Anda memahami cakupan, menilai kesiapan klaim, dan membuat keputusan yang lebih baik." data-en="AI-powered insurance copilot that helps you understand coverage, assess your claim readiness, and make better-informed decisions.">
                        Kopilot asuransi berbasis AI yang membantu Anda memahami cakupan, menilai kesiapan klaim, dan membuat keputusan yang lebih baik.
                    </p>
                    <div class="hero-cta">
                        <a href="{{ route('login') }}" class="btn btn-primary" data-id="Mulai Sekarang" data-en="Get Started">Mulai Sekarang</a>
                        <a href="#how" class="btn btn-ghost">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M8 5.5v13l11-6.5-11-6.5Z" fill="currentColor"/>
                            </svg>
                            <span data-id="Lihat Cara Kerjanya" data-en="See How It Works">Lihat Cara Kerjanya</span>
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <span class="stat-ico grad-blue">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 19V9M10 19V5M16 19v-6M22 19H2" stroke="currentColor"
                                          stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <b>82%</b>
                            <small data-id="Rata-rata Kesiapan Klaim" data-en="Avg. Claim Readiness">Rata-rata Kesiapan Klaim</small>
                        </div>
                        <div class="hero-stat">
                            <span class="stat-ico grad-teal">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="13" r="8" stroke="currentColor" stroke-width="1.9"/>
                                    <path d="M12 9v4.4l2.6 1.6M9 2h6" stroke="currentColor"
                                          stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <b>&lt; 3 Min</b>
                            <small data-id="Ekstraksi Pasal Polis" data-en="Policy Clause Extraction">Ekstraksi Pasal Polis</small>
                        </div>
                        <div class="hero-stat">
                            <span class="stat-ico grad-purple">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2.8 19.5 6v6c0 5-3.2 8.2-7.5 9.2C7.7 20.2 4.5 17 4.5 12V6L12 2.8Z"
                                          stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
                                    <path d="M9 12l2.2 2.2L15.4 10" stroke="currentColor"
                                          stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <b>100%</b>
                            <small data-id="Bukti Tertraceable" data-en="Traceable Evidence">Bukti Tertraceable</small>
                        </div>
                    </div>
                </div>

                <div class="mock" aria-hidden="true">
                    <div class="mock-head">
                        <span class="mock-avatar">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2.6 20 6v6.4c0 5.3-3.4 8.5-8 9.6-4.6-1.1-8-4.3-8-9.6V6l8-3.4Z"
                                      stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M9.2 12.2l2.1 2.1 4.4-4.7" stroke="currentColor" stroke-width="1.9"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <b data-id="Suite Layanan InsureMind" data-en="InsureMind Service Suite">InsureMind Service Suite</b>
                            <small data-id="Pipeline klaim end&#8209;to&#8209;end" data-en="End&#8209;to&#8209;end claim pipeline">Pipeline klaim end&#8209;to&#8209;end</small>
                        </div>
                        <span class="tag"><i class="tag-dot"></i> <span data-id="6 Layanan" data-en="6 Services">6 Layanan</span></span>
                    </div>

                    <div class="mock-body">
                        <div class="mock-label" data-id="Layanan Inti InsureMind" data-en="InsureMind Core Services">Layanan Inti InsureMind</div>

                        <svg class="suite-viz" viewBox="0 0 240 104" role="img"
                             aria-label="Alur pipeline suite layanan InsureMind"
                             data-id-aria="Alur pipeline suite layanan InsureMind"
                             data-en-aria="InsureMind service suite pipeline flow">
                            <defs>
                                <linearGradient id="imSuiteA" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0" stop-color="#5f8bfb"/>
                                    <stop offset="1" stop-color="#2d54d8"/>
                                </linearGradient>
                                <linearGradient id="imSuiteB" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0" stop-color="#4fd0c3"/>
                                    <stop offset="1" stop-color="#16a29a"/>
                                </linearGradient>
                                <linearGradient id="imSuiteC" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0" stop-color="#a98bf6"/>
                                    <stop offset="1" stop-color="#7a52e4"/>
                                </linearGradient>
                                <linearGradient id="imFlowLine" x1="0" y1="0" x2="1" y2="0">
                                    <stop offset="0" stop-color="#4f7ef8"/>
                                    <stop offset=".5" stop-color="#16a29a"/>
                                    <stop offset="1" stop-color="#7a52e4"/>
                                </linearGradient>
                            </defs>

                            <ellipse cx="120" cy="97" rx="104" ry="7" fill="#16233a" opacity=".07"/>
                            <path d="M34 54h172" stroke="url(#imFlowLine)" stroke-width="2" stroke-dasharray="5 6"
                                  stroke-linecap="round" opacity=".65"/>

                            <g class="sv-g sv-g1">
                                <rect x="18" y="35" width="32" height="38" rx="7" fill="url(#imSuiteA)"/>
                                <path d="M28 45h12M28 52h9M28 59h11" stroke="#fff" stroke-width="1.9"
                                      stroke-linecap="round" opacity=".92"/>
                            </g>

                            <g class="sv-g sv-g2">
                                <circle cx="76" cy="54" r="19" fill="#fff" stroke="#e6ecfa" stroke-width="1.4"/>
                                <circle cx="76" cy="54" r="13.6" fill="none" stroke="#e4eaf8" stroke-width="4"/>
                                <circle cx="76" cy="54" r="13.6" fill="none" stroke="url(#imSuiteB)" stroke-width="4"
                                        stroke-linecap="round" stroke-dasharray="60 86" transform="rotate(-90 76 54)"/>
                                <text x="76" y="58" text-anchor="middle" font-size="10" font-weight="700" fill="#12405c">82</text>
                            </g>

                            <g class="sv-g sv-g3">
                                <circle cx="120" cy="54" r="19" fill="#fff" stroke="#e6ecfa" stroke-width="1.4"/>
                                <path d="M110 44h13l6 6v14a2 2 0 0 1-2 2h-17a2 2 0 0 1-2-2V46a2 2 0 0 1 2-2Z"
                                      fill="none" stroke="#5a54cf" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M123 44v6h6" fill="none" stroke="#5a54cf" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="m113 56 3 3 5.5-6.5" fill="none" stroke="#16a29a" stroke-width="2.1"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </g>

                            <g class="sv-g sv-g4">
                                <circle cx="164" cy="54" r="19" fill="#fff" stroke="#e6ecfa" stroke-width="1.4"/>
                                <path d="M157 48.5c2.6-2 6.4-3.1 10.4-3.1s7.8 1.1 10.4 3.1" fill="none"
                                      stroke="#4f7ef8" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M157 48.5v1.6M180.8 48.5v1.6M157 50.1h23.8" fill="none"
                                      stroke="#4f7ef8" stroke-width="1.8" stroke-linecap="round"/>
                                <rect x="162" y="53" width="5" height="12" rx="1.6" fill="#4f7ef8"/>
                                <rect x="170" y="55.5" width="5" height="9.5" rx="1.6" fill="#8aa7fb"/>
                            </g>

                            <g class="sv-g sv-g5">
                                <rect x="192" y="37" width="30" height="36" rx="7" fill="url(#imSuiteA)" opacity=".14"/>
                                <path d="M199 47h16M199 54h11M199 61h13" stroke="#2d54d8" stroke-width="1.9"
                                      stroke-linecap="round"/>
                                <path d="M215 65.5V77M215 77l-4-4.2M215 77l4-4.2" stroke="#2d54d8" stroke-width="1.8"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M207 84.5h16" stroke="#2d54d8" stroke-width="1.9" stroke-linecap="round"/>
                            </g>

                            <g class="sv-g sv-g6">
                                <circle cx="120" cy="86" r="17" fill="#fff" stroke="#e6ecfa" stroke-width="1.4"/>
                                <circle cx="120" cy="81.5" r="4.4" fill="url(#imSuiteC)"/>
                                <path d="M111.5 95c.6-4 4-6.2 8.5-6.2s7.9 2.2 8.5 6.2" fill="url(#imSuiteC)" opacity=".9"/>
                                <path d="M133 76.5 135.5 74l2.5 2.5M138.5 82l2.5 2.5-2.5 2.5" fill="none"
                                      stroke="#7a52e4" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                        </svg>

                        <div class="mock-metas">
                            <span class="meta-chip"><i></i> <span data-id="6 Modul Terhubung" data-en="6 Connected Modules">6 Modul Terhubung</span></span>
                            <span class="meta-chip teal"><i></i> <span data-id="Tertraceable ke Pasal" data-en="Traceable to Clauses">Tertraceable ke Pasal</span></span>
                            <span class="meta-chip purple"><i></i> <span data-id="Human&#8209;in&#8209;the&#8209;Loop" data-en="Human&#8209;in&#8209;the&#8209;Loop">Human&#8209;in&#8209;the&#8209;Loop</span></span>
                        </div>

                        <div class="svc-viz">
                            <div class="svc-tile">
                                <span class="svc-glyph g-teal">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 6h11l4 4v8a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1Z"
                                              stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M14 6v5h5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Penguraian Polis" data-en="Policy Parsing">Policy Parsing</b><small data-id="Pasal terstruktur" data-en="Structured clauses">Pasal terstruktur</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph g-purple-soft">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 3v7l3-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 19a7 7 0 1 0-7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M3.5 12h1.9M18.6 12h1.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Skor Kesiapan" data-en="Readiness Score">Readiness Score</b><small data-id="Skor 0&ndash;100" data-en="Score 0&ndash;100">Skor 0&ndash;100</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph g-ok">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 6h11l4 4v8H4z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M7.5 13.5 10 16l4.2-5" stroke="currentColor" stroke-width="1.7"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Pemeriksaan Dokumen" data-en="Document Check">Document Check</b><small data-id="Verifikasi berkas" data-en="File verification">Verifikasi berkas</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph g-ink">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 3.5 20 7v4.5c0 5-3.3 8-8 9.5-4.7-1.5-8-4.5-8-9.5V7l8-3.5Z"
                                              stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M9 12l2.2 2.2L15.5 10" stroke="currentColor" stroke-width="1.7"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Kutipan Pasal" data-en="Clause Citation">Clause Citation</b><small data-id="Kutipan pasal" data-en="Clause reference">Kutipan pasal</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph g-purple-soft">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M7 3h7l4 4v14H7z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M14 3v4h4M9.5 13h5M9.5 16.5h5" stroke="currentColor" stroke-width="1.6"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Ekspor Dossier" data-en="Dossier Export">Dossier Export</b><small data-id="Berkas siap kirim" data-en="Submission-ready file">Berkas siap kirim</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph g-purple">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/>
                                        <path d="M5 20c0-3.6 3.1-5.6 7-5.6s7 2 7 5.6" stroke="currentColor" stroke-width="1.7"
                                              stroke-linecap="round"/>
                                        <path d="M17 6.5l1.2 1.2L20.5 5.4" stroke="currentColor" stroke-width="1.7"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Tinjauan Manusia" data-en="Human Review">Human Review</b><small data-id="Keputusan akhir" data-en="Final decision">Keputusan akhir</small></div>
                            </div>
                        </div>

                        <div class="mock-foot">
                            <span data-id="Enam layanan, satu pipeline terintegrasi" data-en="Six services, one integrated pipeline"><i class="tag-dot"></i> <span data-id="Enam layanan, satu pipeline terintegrasi" data-en="Six services, one integrated pipeline">Enam layanan, satu pipeline terintegrasi</span></span>
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm" data-id="Coba Sekarang" data-en="Try Now">Coba Sekarang</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Industry friction ──────────────────────────── -->
        <section class="section friction" id="friction">
            <div class="wrap">
                <div class="section-head">
                    <div class="eyebrow" data-id="Hambatan Industri Saat Ini" data-en="Current Industry Friction">Hambatan Industri Saat Ini</div>
                    <h2 data-id="Mengapa Mengklaim Asuransi Masih Terasa Rumit" data-en="Why Claiming Insurance Still Feels Complicated">Mengapa Mengklaim Asuransi Masih Terasa Rumit</h2>
                    <p data-id="Lebih dari 64% penundaan dan sengketa klaim berasal dari ketidaksesuaian dokumen dan interpretasi pasal yang tidak jelas sebelum pengajuan." data-en="Over 64% of claim delays and disputes originate from document mismatches and unclear clause interpretation before submission.">Lebih dari 64% penundaan dan sengketa klaim berasal dari ketidaksesuaian dokumen dan interpretasi pasal yang tidak jelas sebelum pengajuan.</p>
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
                        <h3 data-id="Polis yang Kompleks" data-en="Complex Policies">Polis yang Kompleks</h3>
                        <p data-id="Puluhan halaman pasal dengan terminologi hukum, tabel pengecualian, endorsemen, dan klausa ambigu yang membingungkan bahkan pemegang berpengalaman." data-en="Dozens of pages of clauses with legal terminology, exclusion tables, endorsements, and ambiguous clauses that confuse even experienced holders.">Puluhan halaman pasal dengan terminologi hukum, tabel pengecualian, endorsemen, dan klausa ambigu yang membingungkan bahkan pemegang berpengalaman.</p>
                        <div class="fraud-foot" data-id="&#x2715; 40+ Halaman per kontrak" data-en="&#x2715; 40+ Pages per contract">&#x2715; 40+ Halaman per kontrak</div>
                    </article>

                    <article class="card fraud-card">
                        <div class="fraud-icon">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M12 7.5v5l3 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3 data-id="Cakupan Tidak Jelas" data-en="Unclear Coverage">Cakupan Tidak Jelas</h3>
                        <p data-id="Ketidakpastian apakah suatu kejadian benar-benar ditanggung atau dilarang secara eksplisit berdasarkan pemicu pengecualian halus dan ambang batas deductible." data-en="Uncertainty whether an incident is truly covered or explicitly barred under subtle exclusion triggers and deducible thresholds.">Ketidakpastian apakah suatu kejadian benar-benar ditanggung atau dilarang secara eksplisit berdasarkan pemicu pengecualian halus dan ambang batas deductible.</p>
                        <div class="fraud-foot" data-id="&#x2715; Kebingungan sub-limit" data-en="&#x2715; Sub-limit confusion">&#x2715; Kebingungan sub-limit</div>
                    </article>

                    <article class="card fraud-card">
                        <div class="fraud-icon">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                <rect x="4" y="4" width="16" height="16" rx="3" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <circle cx="17" cy="17" r="3.4" fill="#fff" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </div>
                        <h3 data-id="Dokumen Tidak Lengkap" data-en="Missing Documents">Dokumen Tidak Lengkap</h3>
                        <p data-id="Pengajuan dengan dokumen pendukung yang tidak lengkap menyebabkan penundaan yang bisa dihindari atau bukti foto kerusakan yang tidak memadai sehingga berujung penolakan." data-en="Filings submitted with incomplete supporting documents, leading to avoidable delays or insufficient photo damage proof that leads to rejections.">Pengajuan dengan dokumen pendukung yang tidak lengkap menyebabkan penundaan yang bisa dihindari atau bukti foto kerusakan yang tidak memadai sehingga berujung penolakan.</p>
                        <div class="fraud-foot" data-id="&#x2715; 38% pengajuan tidak lengkap" data-en="&#x2715; 38% incomplete filings">&#x2715; 38% pengajuan tidak lengkap</div>
                    </article>

                    <article class="card fraud-card">
                        <div class="fraud-icon">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                <path d="M12 3.5 20 7v4.5c0 5-3.3 8-8 9.5-4.7-1.5-8-4.5-8-9.5V7l8-3.5Z"
                                      stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M12 9v4M12 15.6v.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3 data-id="Langkah Selanjutnya Tidak Pasti" data-en="Uncertain Next Steps">Langkah Selanjutnya Tidak Pasti</h3>
                        <p data-id="Tidak ada visibilitas ke linimasa adjudikasi, jadwal pemrosesan, atau penyesuaian mana yang diperlukan untuk memenuhi syarat klaim Anda." data-en="Zero visibility into adjudication timelines, processing schedules, or exactly which adjustments are required to qualify your claim.">Tidak ada visibilitas ke linimasa adjudikasi, jadwal pemrosesan, atau penyesuaian mana yang diperlukan untuk memenuhi syarat klaim Anda.</p>
                        <div class="fraud-foot" data-id="&#x2715; Alur kerja tidak transparan" data-en="&#x2715; Opaque workflows">&#x2715; Alur kerja tidak transparan</div>
                    </article>
                </div>
            </div>
        </section>

        <!-- ── Journey ────────────────────────────────────── -->
        <section class="section" id="journey">
            <div class="wrap">
                <div class="journey-head">
                    <div>
                        <div class="eyebrow" data-id="Kecerdasan Terintegrasi" data-en="Integrated Intelligence">Kecerdasan Terintegrasi</div>
                        <h2 data-id="Satu Kopilot untuk Perjalanan Asuransi Anda" data-en="One Copilot for Your Insurance Journey">Satu Kopilot untuk Perjalanan Asuransi Anda</h2>
                    </div>
                    <p data-id="Dari penyerapan polis pertama hingga pengemasan dokumen akhir, InsureMind mengatur setiap langkah dengan bukti yang dapat diverifikasi." data-en="From first policy ingestion to final document packaging, InsureMind orchestrates every step with verifiable evidence.">Dari penyerapan polis pertama hingga pengemasan dokumen akhir, InsureMind mengatur setiap langkah dengan bukti yang dapat diverifikasi.</p>
                </div>

                <div class="grid-3">
                    <article class="card step-card">
                        <div class="step-num">01</div>
                        <h3 data-id="Pahami" data-en="Understand">Pahami</h3>
                        <p data-id="AI menerjemahkan halaman PDF polis yang padat menjadi ringkasan bahasa sederhana yang jelas, pasal terstruktur, dan tabel deductible." data-en="AI translates dense policy PDF pages into crystal-clear plain language summaries, structured clauses, deducibles, and deducible tables.">AI menerjemahkan halaman PDF polis yang padat menjadi ringkasan bahasa sederhana yang jelas, pasal terstruktur, dan tabel deductible.</p>
                        <ul class="check-list">
                            <li data-id="Sintesis pasal tidak langsung" data-en="Indirect clause synthesis">Sintesis pasal tidak langsung</li>
                            <li data-id="Ringkasan Bahasa Inggris &amp; Bahasa Indonesia" data-en="Plain-English &amp; Bahasa summaries">Ringkasan Bahasa Inggris &amp; Bahasa Indonesia</li>
                        </ul>
                    </article>

                    <article class="card step-card">
                        <div class="step-num">02</div>
                        <h3 data-id="Periksa" data-en="Check">Periksa</h3>
                        <p data-id="Pra-adjudikasi skenario Anda terhadap kriteria polis dengan Skor Kesiapan Klaim berbasis bukti sebelum menghubungi penanggung." data-en="Pre-adjudicate your scenario against policy criteria with an evidence-grounded Claim Readiness Score before contacting the insurer.">Pra-adjudikasi skenario Anda terhadap kriteria polis dengan Skor Kesiapan Klaim berbasis bukti sebelum menghubungi penanggung.</p>
                        <ul class="check-list">
                            <li data-id="Pemeriksaan perbedaan pra-pengiriman" data-en="Pre-submission discrepancy check">Pemeriksaan perbedaan pra-pengiriman</li>
                            <li data-id="Kepercayaan cakupan tingkat pasal" data-en="Clause-level coverage confidence">Kepercayaan cakupan tingkat pasal</li>
                        </ul>
                    </article>

                    <article class="card step-card">
                        <div class="step-num">03</div>
                        <h3 data-id="Siapkan" data-en="Prepare">Siapkan</h3>
                        <p data-id="Daftar periksa dokumen otomatis, peringatan item yang hilang, petunjuk penilaian foto, dan ekspor dossier terstruktur untuk tinjauan adjuster." data-en="Automated document checklists, missing item alerts, photo assessment hints, and structured dossier export tailored for adjuster review.">Daftar periksa dokumen otomatis, peringatan item yang hilang, petunjuk penilaian foto, dan ekspor dossier terstruktur untuk tinjauan adjuster.</p>
                        <ul class="check-list">
                            <li data-id="Pembuatan paket klaim otomatis" data-en="Automated claim pack generation">Pembuatan paket klaim otomatis</li>
                            <li data-id="Format ekspor terstruktur" data-en="Structured export formats">Format ekspor terstruktur</li>
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        <!-- ── Claim readiness score ──────────────────────── -->
        <section class="section" id="score">
            <div class="wrap">
                <div class="score-panel">
                    <div class="svc-panel">
                        <div class="svc-head">
                            <div class="mock-label" data-id="Layanan Inti InsureMind" data-en="InsureMind Core Services">Layanan Inti InsureMind</div>
                            <span class="svc-live"><i></i> <span data-id="Sistem Aktif" data-en="System Active">Sistem Aktif</span></span>
                        </div>

                        <div class="svc-steps">
                            <div class="svc-step">
                                <div class="svc-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 6h11l4 4v8a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1Z"
                                              stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M14 6v5h5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="svc-copy">
                                    <b data-id="Penyerapan Polis &amp; Penguraian Pasal" data-en="Policy Ingestion &amp; Clause Parsing">Policy Ingestion &amp; Clause Parsing</b>
                                    <small data-id="PDF polis dipecah menjadi pasal terstruktur" data-en="Policy PDF split into structured clauses">PDF polis dipecah menjadi pasal terstruktur</small>
                                </div>
                                <span class="svc-state ok"><i class="svc-dot"></i> <span data-id="Siap" data-en="Ready">Siap</span></span>
                            </div>

                            <div class="svc-step is-key">
                                <div class="svc-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 3v7l3-2" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 19a7 7 0 1 0-7-7" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                        <path d="M3.5 12h1.9M18.6 12h1.9" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="svc-copy">
                                    <b data-id="Skor Kesiapan Klaim" data-en="Claim Readiness Score">Claim Readiness Score</b>
                                    <small data-id="Skor 0&ndash;100 atas kelayakan klaim Anda" data-en="Score 0&ndash;100 on your claim eligibility">Skor 0&ndash;100 atas kelayakan klaim Anda</small>
                                </div>
                                <span class="svc-state key"><i class="svc-dot"></i> Engine v2.4</span>
                            </div>

                            <div class="svc-step">
                                <div class="svc-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 6h11l4 4v8H4z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M7.5 13.5 10 16l4.2-5" stroke="currentColor" stroke-width="1.7"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="svc-copy">
                                    <b data-id="Bukti &amp; Pemeriksaan Dokumen" data-en="Evidence &amp; Document Check">Evidence &amp; Document Check</b>
                                    <small data-id="Verifikasi e-Police, KTP, foto &amp; laporan" data-en="Verify e-Policy, ID, photos &amp; reports">Verifikasi e-Police, KTP, foto &amp; laporan</small>
                                </div>
                                <span class="svc-state"><i class="svc-dot"></i> <span data-id="4 Item" data-en="4 Items">4 Items</span></span>
                            </div>

                            <div class="svc-step">
                                <div class="svc-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 3.5 20 7v4.5c0 5-3.3 8-8 9.5-4.7-1.5-8-4.5-8-9.5V7l8-3.5Z"
                                              stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M9 12l2.2 2.2L15.5 10" stroke="currentColor" stroke-width="1.7"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="svc-copy">
                                    <b data-id="Kutipan Pasal Tertraceable" data-en="Traceable Clause Citation">Traceable Clause Citation</b>
                                    <small data-id="Setiap penilaian dikutip ke pasal asli" data-en="Every assessment cited to original clause">Setiap penilaian dikutip ke pasal asli</small>
                                </div>
                                <span class="svc-state ok"><i class="svc-dot"></i> <span data-id="Terverifikasi" data-en="Grounded">Terverifikasi</span></span>
                            </div>

                            <div class="svc-step">
                                <div class="svc-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <path d="M7 3h7l4 4v14H7z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M14 3v4h4M9.5 13h5M9.5 16.5h5" stroke="currentColor" stroke-width="1.6"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="svc-copy">
                                    <b data-id="Pengemasan Dossier" data-en="Dossier Packaging">Dossier Packaging</b>
                                    <small data-id="Berkas siap kirim untuk review adjuster" data-en="Submission-ready file for adjuster review">Berkas siap kirim untuk review adjuster</small>
                                </div>
                                <span class="svc-state"><i class="svc-dot"></i> <span data-id="Ekspor" data-en="Export">Ekspor</span></span>
                            </div>

                            <div class="svc-step">
                                <div class="svc-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/>
                                        <path d="M5 20c0-3.6 3.1-5.6 7-5.6s7 2 7 5.6" stroke="currentColor" stroke-width="1.7"
                                              stroke-linecap="round"/>
                                        <path d="M17 6.5l1.2 1.2L20.5 5.4" stroke="currentColor" stroke-width="1.7"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="svc-copy">
                                    <b data-id="Tinjauan Manusia di dalam Loop" data-en="Human-in-the-Loop Review">Human-in-the-Loop Review</b>
                                    <small data-id="Keputusan akhir tetap oleh adjudicator" data-en="Final decision remains with adjudicator">Keputusan akhir tetap oleh adjudicator</small>
                                </div>
                                <span class="svc-state" data-id="Diatur" data-en="Governed">Diatur</span>
                            </div>
                        </div>

                        <div class="svc-foot">
                            <span><i class="tag-dot"></i> <span data-id="Semua layanan berjalan dari" data-en="All services run from">Semua layanan berjalan dari</span> <b data-id="satu dashboard" data-en="one dashboard">satu dashboard</b> <span data-id="terintegrasi" data-en="integrated">terintegrasi</span></span>
                            <a href="{{ route('login') }}" class="btn btn-ghost" style="padding:7px 12px;font-size:11.5px">
                                <span data-id="Coba Sekarang" data-en="Try Now">Coba Sekarang</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4.5 12h14M13 6.5l5.5 5.5-5.5 5.5" stroke="currentColor" stroke-width="2.1"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="score-copy">
                        <div class="eyebrow" data-id="Teknologi Unggulan" data-en="Signature Technology">Teknologi Unggulan</div>
                        <h2 data-id="Setiap Layanan Inti, Satu Pipeline Terintegrasi" data-en="Every Core Service, One Integrated Pipeline">Setiap Layanan Inti, Satu Pipeline Terintegrasi</h2>
                        <p data-id="Dari penyerapan polis hingga pengemasan dossier akhir, InsureMind menjalankan setiap layanan dalam satu alur yang dapat dilacak &mdash; sehingga Anda selalu tahu apa yang telah diperiksa, apa yang kurang, dan apa yang harus dilakukan selanjutnya sebelum pengiriman formal." data-en="From policy ingestion to final dossier packaging, InsureMind runs each service in a single traceable flow &mdash; so you always know what has been checked, what is missing, and what to do next before formal submission.">
                            Dari penyerapan polis hingga pengemasan dossier akhir, InsureMind menjalankan setiap layanan dalam satu alur yang dapat dilacak &mdash; sehingga Anda selalu tahu apa yang telah diperiksa, apa yang kurang, dan apa yang harus dilakukan selanjutnya sebelum pengiriman formal.
                        </p>

                        <div class="svc-viz">
                            <div class="svc-tile">
                                <span class="svc-glyph g-teal">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 6h11l4 4v8a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1Z"
                                              stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M14 6v5h5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Penyerapan Polis" data-en="Policy Ingestion">Policy Ingestion</b><small data-id="PDF polis dipecah jadi pasal" data-en="Policy PDF split into clauses">PDF polis dipecah jadi pasal</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph g-purple">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 3v7l3-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 19a7 7 0 1 0-7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M3.5 12h1.9M18.6 12h1.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Skor Kesiapan Klaim" data-en="Claim Readiness Score">Claim Readiness Score</b><small data-id="Skor kelayakan 0&ndash;100" data-en="Eligibility score 0&ndash;100">Skor kelayakan 0&ndash;100</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph g-ok">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 6h11l4 4v8H4z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M7.5 13.5 10 16l4.2-5" stroke="currentColor" stroke-width="1.7"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Pemeriksaan Bukti" data-en="Evidence Check">Evidence Check</b><small data-id="Verifikasi e&#8209;Polisi, KTP, foto" data-en="Verify e&#8209;Policy, ID, photos">Verifikasi e&#8209;Polisi, KTP, foto</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph g-ink">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 3.5 20 7v4.5c0 5-3.3 8-8 9.5-4.7-1.5-8-4.5-8-9.5V7l8-3.5Z"
                                              stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M9 12l2.2 2.2L15.5 10" stroke="currentColor" stroke-width="1.7"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Kutipan Tertraceable" data-en="Traceable Citation">Traceable Citation</b><small data-id="Dikutip ke pasal asli" data-en="Cited to original clause">Dikutip ke pasal asli</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph g-amber">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M7 3h7l4 4v14H7z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M14 3v4h4M9.5 13h5M9.5 16.5h5" stroke="currentColor" stroke-width="1.6"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Pengemasan Dossier" data-en="Dossier Packaging">Dossier Packaging</b><small data-id="Berkas siap kirim" data-en="Submission-ready file">Berkas siap kirim</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph g-purple-soft">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/>
                                        <path d="M5 20c0-3.6 3.1-5.6 7-5.6s7 2 7 5.6" stroke="currentColor" stroke-width="1.7"
                                              stroke-linecap="round"/>
                                        <path d="M17 6.5l1.2 1.2L20.5 5.4" stroke="currentColor" stroke-width="1.7"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Manusia di dalam Loop" data-en="Human-in-the-Loop">Human-in-the-Loop</b><small data-id="Keputusan akhir adjudicator" data-en="Final adjudicator decision">Keputusan akhir adjudicator</small></div>
                            </div>
                        </div>

                        <div class="hero-cta" style="margin-top:20px">
                            <a href="{{ route('login') }}" class="btn btn-primary" data-id="Coba Semua Layanan" data-en="Try All Services">Coba Semua Layanan</a>
                            <span style="font-size:11px;color:var(--soft);align-self:center" data-id="Satu dashboard, dari upload sampai dossier" data-en="One dashboard, from upload to dossier">Satu dashboard, dari upload sampai dossier</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Traceable citations ────────────────────────── -->
        <section class="section friction" id="citations">
            <div class="wrap">
                <div class="section-head">
                    <div class="eyebrow" data-id="AI yang Dapat Dijelaskan &middot; Kutipan Tertraceable" data-en="Explainable AI &middot; Traceable Citations">AI yang Dapat Dijelaskan &middot; Kutipan Tertraceable</div>
                    <h2 data-id="Tanpa Halusinasi. Setiap Penilaian Berdasarkan Pasal Nyata." data-en="No Hallucinations. Every Assessment Grounded in Real Clauses.">Tanpa Halusinasi. Setiap Penilaian Berdasarkan Pasal Nyata.</h2>
                    <p data-id="InsureMind menghasilkan penalaran yang siap diaudit. Kami memasangkan setiap saran keputusan dengan koordinat polis yang tepat dan menyoroti teks verbatim." data-en="InsureMind produces audit-ready reasoning. We pair every decision suggestion with exact policy coordinates and highlight verbatim text.">InsureMind menghasilkan penalaran yang siap diaudit. Kami memasangkan setiap saran keputusan dengan koordinat polis yang tepat dan menyoroti teks verbatim.</p>
                </div>

                <div class="citation-card">
                    <div class="citation-head">
                        <span class="card-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 2.6 19.6 5.9v6.1c0 5-3.3 8.2-7.6 9.2-4.3-1-7.6-4.2-7.6-9.2V5.9L12 2.6Z"
                                      stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M8.8 12.1l2.2 2.2 4.2-4.6" stroke="currentColor" stroke-width="1.9"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <b data-id="PENENTUAN ALGORITMIK" data-en="ALGORITHMIC DETERMINATION">PENENTUAN ALGORITMIK</b>
                        <span class="conf" data-id="87% Kepercayaan" data-en="87% Confidence">87% Kepercayaan</span>
                        <span class="model"><i class="tag-dot"></i> <span data-id="Model DeepClaim-v4-1D8" data-en="Model DeepClaim-v4-1D8">Model DeepClaim-v4-1D8</span></span>
                    </div>

                    <div class="factors">
                        <div class="mock-label" data-id="Faktor Penjaminan Utama (Mengapa?)" data-en="Key Underwriting Factors (Why?)">Faktor Penjaminan Utama (Mengapa?)</div>
                        <div class="factor-grid">
                            <div class="factor" data-id="Kerusakan Banjir Termasuk" data-en="Flood Damage Included">Kerusakan Banjir Termasuk</div>
                            <div class="factor" data-id="Polis Saat Ini Aktif" data-en="Policy Currently Active">Polis Saat Ini Aktif</div>
                            <div class="factor" data-id="Termasuk Bencana Alam" data-en="Included Natural Catastrophe">Termasuk Bencana Alam</div>
                        </div>
                    </div>

                    <div class="quote">
                        <div class="mock-label"><span data-id="Pasal 4.2 Cakupan Bencana Alam" data-en="Policy Section 4.2 Natural Disaster Coverage">Pasal 4.2 Cakupan Bencana Alam</span></div>
                        <p data-id='"Penanggung setuju untuk mengganti kerugian tertanggung atas kerusakan banjir mendadak, genangan air topan, dan kerusakan mesin akibat badai hingga batas agregat yang ditentukan sebesar IDR 350.000.000, tunduk pada deductible standar."' data-en='"The underwriter agrees to indemnify the insured against sudden flood damage, typhoon water inundation, and storm-related mechanical engine stalls up to the specified aggregate limit of IDR 350,000,000, subject to standard deductible."'>
                            "Penanggung setuju untuk mengganti kerugian tertanggung atas kerusakan banjir mendadak, genangan air topan, dan kerusakan mesin akibat badai hingga batas agregat yang ditentukan sebesar IDR 350.000.000, tunduk pada deductible standar."
                        </p>
                        <div class="quote-foot">
                            <span data-id="Kemiripan Dekat: 0,942 terhadap Deskripsi Insiden" data-en="Close Similarity: 0.942 against Incident Description">Kemiripan Dekat: 0,942 terhadap Deskripsi Insiden</span>
                            <a href="#citations">
                                <span data-id="Lihat Pasal Polis" data-en="View Policy Clause">Lihat Pasal Polis</span>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M6 18 17.4 6.6M10 6.4h7.6v7.6" stroke="currentColor" stroke-width="2.2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Human in the loop ──────────────────────────── -->
        <section class="section" id="reviewer">
            <div class="wrap">
                <div class="section-head">
                    <div class="eyebrow" data-id="Kerangka AI yang Bertanggung Jawab" data-en="Responsible AI Framework">Kerangka AI yang Bertanggung Jawab</div>
                    <h2 data-id="Tata Kelola Klaim dengan Manusia di Dalam Loop" data-en="Human-in-the-Loop Claim Governance">Tata Kelola Klaim dengan Manusia di Dalam Loop</h2>
                    <p data-id="InsureMind membantu dengan penilaian dan persiapan. Keputusan klaim akhir tetap berada pada peninjau manusia yang berwenang." data-en="InsureMind assists with assessment and preparation. Final claim decisions remain with authorized human reviewers.">InsureMind membantu dengan penilaian dan persiapan. Keputusan klaim akhir tetap berada pada peninjau manusia yang berwenang.</p>
                </div>

                <div class="loop">
                    <div class="card loop-step">
                        <div class="loop-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="8" r="3.4" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M5.2 20c0-3.5 3-5.6 6.8-5.6s6.8 2.1 6.8 5.6" stroke="currentColor"
                                      stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <b data-id="Pengguna" data-en="User">Pengguna</b>
                        <small data-id="Pengajuan &amp; Orientasi" data-en="Submission &amp; Onboarding">Pengajuan &amp; Orientasi</small>
                    </div>
                    <div class="card loop-step">
                        <div class="loop-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <rect x="5.5" y="5.5" width="13" height="13" rx="3.4" stroke="currentColor"
                                      stroke-width="1.7"/>
                                <path d="M12 2.6v2.6M12 18.8v2.6M2.6 12h2.6M18.8 12h2.6" stroke="currentColor"
                                      stroke-width="1.7" stroke-linecap="round"/>
                                <circle cx="12" cy="12" r="2.6" fill="currentColor" opacity=".85"/>
                            </svg>
                        </div>
                        <b data-id="Analisis AI" data-en="AI Analysis">Analisis AI</b>
                        <small data-id="Memproses OCR &amp; data idempoten" data-en="Parses OCR &amp; idempotent data">Memproses OCR &amp; data idempoten</small>
                    </div>
                    <div class="card loop-step">
                        <div class="loop-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M14.5 3H7a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V6.5L14.5 3Z"
                                      stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M14.2 3.2V7h3.6M9.2 12.5h5.6M9.2 16h3.8" stroke="currentColor"
                                      stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <b data-id="Bukti" data-en="Evidence">Bukti</b>
                        <small data-id="Indeks pasal yang diverifikasi mesin" data-en="Machine-verified clause index">Indeks pasal yang diverifikasi mesin</small>
                    </div>
                    <div class="card loop-step">
                        <div class="loop-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M4 18V9M9.3 18V5.5M14.7 18v-6.5M20 18V3.5" stroke="currentColor"
                                      stroke-width="1.9" stroke-linecap="round"/>
                                <path d="M2.4 21.4h19.2" stroke="currentColor" stroke-width="1.7"
                                      stroke-linecap="round" opacity=".55"/>
                            </svg>
                        </div>
                        <b data-id="Rekomendasi" data-en="Recommendation">Rekomendasi</b>
                        <small data-id="Skor kesiapan &amp; ringkasan" data-en="Readiness score &amp; brief">Skor kesiapan &amp; ringkasan</small>
                    </div>
                    <div class="card loop-step emphasis">
                        <div class="loop-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M12 3.4 19.6 6.6v6c0 5-3.3 8.2-7.6 9.2-4.3-1-7.6-4.2-7.6-9.2v-6L12 3.4Z"
                                      stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M8.9 11.9l2.2 2.2 4.1-4.4" stroke="currentColor" stroke-width="1.8"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <b data-id="Tinjauan Manusia" data-en="Human Review">Tinjauan Manusia</b>
                        <small data-id="Pemeriksaan adjudicator berlisensi" data-en="Licensed adjudicator check">Pemeriksaan adjudicator berlisensi</small>
                    </div>
                    <div class="card loop-step">
                        <div class="loop-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M3.6 19.4 10.4 4.2l3.4 6.4 3-1.4 3.6 10.2H3.6Z" stroke="currentColor"
                                      stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M3.6 19.4h16.8" stroke="currentColor" stroke-width="1.7"
                                      stroke-linecap="round"/>
                            </svg>
                        </div>
                        <b data-id="Keputusan Akhir" data-en="Final Decision">Keputusan Akhir</b>
                        <small data-id="Persetujuan klaim resmi" data-en="Official claim approval">Persetujuan klaim resmi</small>
                    </div>
                </div>

                <div class="notice">
                    <span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 2.6 19.6 5.9v6.1c0 5-3.3 8.2-7.6 9.2-4.3-1-7.6-4.2-7.6-9.2V5.9L12 2.6Z"
                                  stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M8.8 12.1l2.2 2.2 4.2-4.6" stroke="currentColor" stroke-width="1.9"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span data-id="InsureMind berperan murni sebagai kopilot pendukung. Sistem secara eksplisit membatasi pemicu pembayaran otomatis. Semua penyelesaian keuangan bergantung pada alur kerja penandatanganan peninjau yang ditetapkan undang-undang." data-en="InsureMind acts purely as an assistive copilot. The system explicitly restricts autonomous payout triggers. All financial resolutions defer to statutory reviewer sign-off workflows.">InsureMind berperan murni sebagai kopilot pendukung. Sistem secara eksplisit membatasi pemicu pembayaran otomatis. Semua penyelesaian keuangan bergantung pada alur kerja penandatanganan peninjau yang ditetapkan undang-undang.</span>
                </div>
            </div>
        </section>

        <!-- ── How it works ───────────────────────────────── -->
        <section class="section" id="how">
            <div class="wrap">
                <div class="section-head">
                    <div class="eyebrow" data-id="Proses 4 Langkah yang Mudah" data-en="Seamless 4-Step Process">Proses 4 Langkah yang Mudah</div>
                    <h2 data-id="Cara Kerja InsureMind" data-en="How InsureMind Works">Cara Kerja InsureMind</h2>
                    <p data-id="Mulai dari dokumen asuransi yang ambigu hingga paket klaim yang terverifikasi dan lengkap dalam empat langkah mudah." data-en="Go from an ambiguous insurance paper to a verified, complete claim pack in four straightforward steps.">Mulai dari dokumen asuransi yang ambigu hingga paket klaim yang terverifikasi dan lengkap dalam empat langkah mudah.</p>
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
                        <h3 data-id="Unggah" data-en="Upload">Unggah</h3>
                        <p data-id="Unggah dokumen asuransi Anda. InsureMind memproses PDF, mengekstrak data penting, dan menyusun semua informasi kritis untuk analisis." data-en="Upload your insurance document. InsureMind parses PDFs, extracts key data, and structures all critical information for analysis.">Unggah dokumen asuransi Anda. InsureMind memproses PDF, mengekstrak data penting, dan menyusun semua informasi kritis untuk analisis.</p>
                        <span class="process-tag" data-id="Disanitasi sisi klien" data-en="Client-side sanitized"><i class="tag-dot"></i> <span data-id="Disanitasi sisi klien" data-en="Client-side sanitized">Disanitasi sisi klien</span></span>
                    </article>

                    <article class="card process-card">
                        <div class="process-num">02</div>
                        <div class="process-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M6 3h7l5 5v13H6z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M13 3v5h5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3 data-id="Pahami" data-en="Understand">Pahami</h3>
                        <p data-id="Dapatkan gambaran terstruktur secara instan. Pelajari cakupan, deductible, batas, dan pengecualian eksplisit secara langsung." data-en="Get an instant structured overview. Learn about your coverage, deductibles, limits, and explicit exclusions instantly.">Dapatkan gambaran terstruktur secara instan. Pelajari cakupan, deductible, batas, dan pengecualian eksplisit secara langsung.</p>
                        <span class="process-tag" data-id="Indeks pasal dibuat" data-en="Clause index generated"><i class="tag-dot"></i> <span data-id="Indeks pasal dibuat" data-en="Clause index generated">Indeks pasal dibuat</span></span>
                    </article>

                    <article class="card process-card">
                        <div class="process-num">03</div>
                        <div class="process-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M5 12.5 10 17l9-9.5" stroke="currentColor" stroke-width="1.8"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3 data-id="Periksa" data-en="Check">Periksa</h3>
                        <p data-id="Nilai keabsahan klaim dengan Skor Kesiapan Klaim, kesiapan dokumen, dan indikasi kepercayaan hingga pra-adjudikasi." data-en="Assess claim validity with our Claim Readiness Score, document readiness, and confidence indications up to pre-adjudication.">Nilai keabsahan klaim dengan Skor Kesiapan Klaim, kesiapan dokumen, dan indikasi kepercayaan hingga pra-adjudikasi.</p>
                        <span class="process-tag" data-id="Skor 0-100% instan" data-en="Instant 0-100% score"><i class="tag-dot"></i> <span data-id="Skor 0-100% instan" data-en="Instant 0-100% score">Skor 0-100% instan</span></span>
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
                        <h3 data-id="Siapkan" data-en="Prepare">Siapkan</h3>
                        <p data-id="Kompilasi dan unduh dossier klaim yang lengkap dan siap kirim sesuai standar verifikasi adjudicator." data-en="Compile and download a complete, submission-ready claim dossier aligned to adjudicator verification standards.">Kompilasi dan unduh dossier klaim yang lengkap dan siap kirim sesuai standar verifikasi adjudicator.</p>
                        <span class="process-tag" data-id="&#x2193; Ekspor Dossier PDF" data-en="&#x2193; PDF Dossier Export">&#x2193; Ekspor Dossier PDF</span>
                    </article>
                </div>
            </div>
        </section>

        <!-- ── Compliance ─────────────────────────────────── -->
        <section class="section friction" id="admin">
            <div class="wrap">
                <div class="section-head">
                    <div class="eyebrow" data-id="Integritas Regulasi" data-en="Regulatory Integrity">Integritas Regulasi</div>
                    <h2 data-id="Dirancang dengan Pertimbangan Kepatuhan OJK &amp; PDP" data-en="Designed with OJK &amp; PDP Compliance Considerations">Dirancang dengan Pertimbangan Kepatuhan OJK &amp; PDP</h2>
                    <p data-id="Dibangun dari awal untuk menghormati Undang-Undang Perlindungan Data Pribadi (UU PDP) Indonesia dan keselarasan dengan standar keamanan siber fintech OJK." data-en="Architected from the ground up to respect Indonesia&rsquo;s Personal Data Protection (UU PDP) law and alignment with OJK fintech cybersecurity standards.">Dibangun dari awal untuk menghormati Undang-Undang Perlindungan Data Pribadi (UU PDP) Indonesia dan keselarasan dengan standar keamanan siber fintech OJK.</p>
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
                        <p data-id="Kontrol persetujuan pengguna yang terperinci, penghapusan PII otomatis pada KTP, kartu, dan riwayat login yang diunggah, serta jaminan privasi data zero-trust tingkat platform untuk tindakan pasal sesuai permintaan." data-en="Granular user consent controls, automatic PII scrubbing on uploaded IDs, cards, and login history, plus platform-level zero-trust data privacy assurance on-demand clause actions.">Kontrol persetujuan pengguna yang terperinci, penghapusan PII otomatis pada KTP, kartu, dan riwayat login yang diunggah, serta jaminan privasi data zero-trust tingkat platform untuk tindakan pasal sesuai permintaan.</p>
                        <span class="compliance-foot" data-id="Deteksi Yurisdiksi Lokal" data-en="Local-Jurisdiction Detection"><i class="tag-dot"></i> <span data-id="Deteksi Yurisdiksi Lokal" data-en="Local-Jurisdiction Detection">Deteksi Yurisdiksi Lokal</span></span>
                    </article>

                    <article class="card compliance-card">
                        <div class="compliance-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M12 3 20 6.5v5c0 5-3.4 8.2-8 9.5-4.6-1.3-8-4.5-8-9.5v-5L12 3Z"
                                      stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M12 10v3M12 15.4v.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3 data-id="Keamanan Enterprise" data-en="Enterprise Security">Keamanan Enterprise</h3>
                        <p data-id="Enkripsi AES 256-bit saat istirahat, enkripsi transit TLS 1.3, dan jejak audit terautentikasi berkelanjutan dengan tinjauan kerentanan otomatis." data-en="256-bit AES encryption at rest, TLS 1.3 transit encryption, and continuous authenticated audit trails with automatic vulnerability reviews.">Enkripsi AES 256-bit saat istirahat, enkripsi transit TLS 1.3, dan jejak audit terautentikasi berkelanjutan dengan tinjauan kerentanan otomatis.</p>
                        <span class="compliance-foot" data-id="Kontrol Selaras ISO 27001" data-en="ISO 27001 Aligned Controls"><i class="tag-dot"></i> <span data-id="Kontrol Selaras ISO 27001" data-en="ISO 27001 Aligned Controls">Kontrol Selaras ISO 27001</span></span>
                    </article>

                    <article class="card compliance-card">
                        <div class="compliance-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M6 4h9l4 4v12H6z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M15 4v4h4" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3 data-id="Sistem Sadar Kepatuhan" data-en="Compliance-Aware System">Sistem Sadar Kepatuhan</h3>
                        <p data-id="Klasifikasi asistif yang eksplisit. Kami menghindari penikatan penjaminan otonom, menjaga akuntabilitas penanggung yang diamanatkan OJK." data-en="Explicit assistive classification. We avoid autonomous underwriting binding, preserving OJK mandated insurer accountability.">Klasifikasi asistif yang eksplisit. Kami menghindari penikatan penjaminan otonom, menjaga akuntabilitas penanggung yang diamanatkan OJK.</p>
                        <span class="compliance-foot" data-id="Alat AI Non-Adjudicating" data-en="Non-Adjudicating AI Tool"><i class="tag-dot"></i> <span data-id="Alat AI Non-Adjudicating" data-en="Non-Adjudicating AI Tool">Alat AI Non-Adjudicating</span></span>
                    </article>
                </div>
            </div>
        </section>

        <!-- ── CTA ────────────────────────────────────────── -->
        <section class="section" id="architecture">
            <div class="wrap">
                <div class="cta">
                    <span class="cta-tag" data-id="Perlindungan Data Industri" data-en="Industry Data Protection"><i class="tag-dot"></i> <span data-id="Perlindungan Data Industri" data-en="Industry Data Protection">Perlindungan Data Industri</span></span>
                    <h2 data-id="Kendalikan Cakupan Asuransi Anda Hari Ini" data-en="Take Control of Your Insurance Coverage Today">Kendalikan Cakupan Asuransi Anda Hari Ini</h2>
                    <p data-id="Unggah polis Anda dalam kurang dari 60 detik dan rasakan kejelasan panduan AI berbasis bukti." data-en="Upload your policy in less than 60 seconds and experience the clarity of evidence-grounded AI guidance.">Unggah polis Anda dalam kurang dari 60 detik dan rasakan kejelasan panduan AI berbasis bukti.</p>
                    <div class="cta-actions">
                        <a href="{{ route('login') }}" class="btn btn-light" data-id="Mulai Sekarang" data-en="Get Started Now">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 19V5M12 5l-5.2 5.2M12 5l5.2 5.2" stroke="currentColor" stroke-width="2.1"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span data-id="Mulai Sekarang" data-en="Get Started Now">Mulai Sekarang</span>
                        </a>
                        <a href="#how" class="btn btn-outline-light">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M8 5.5v13l11-6.5-11-6.5Z" fill="currentColor"/>
                            </svg>
                            <span data-id="Jelajahi Dokumentasi" data-en="Explore Documentation">Jelajahi Dokumentasi</span>
                        </a>
                    </div>
                    <div class="cta-foot">
                        <span>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="2.5" y="6" width="19" height="12" rx="2.5" stroke="currentColor"
                                      stroke-width="1.9"/>
                                <path d="M2.5 10h19" stroke="currentColor" stroke-width="1.9"/>
                            </svg>
                            <span data-id="Tidak perlu kartu kredit" data-en="No credit card required">Tidak perlu kartu kredit</span>
                        </span>
                        <span>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 7V4.8h15.2V7M12 4.8V19M9 19h6" stroke="currentColor" stroke-width="1.9"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span data-id="Ekstraksi OCR Instan" data-en="Instant OCR Extraction">Ekstraksi OCR Instan</span>
                        </span>
                    </div>

                    <svg class="cta-visual" viewBox="0 0 320 74" role="img"
                         aria-label="Pipeline layanan InsureMind"
                         data-id-aria="Pipeline layanan InsureMind"
                         data-en-aria="InsureMind service pipeline">
                        <path d="M40 37h240" stroke="#ffffff" stroke-width="2" stroke-dasharray="4 7"
                              stroke-linecap="round" opacity=".45"/>

                        <g class="cv-node cv-n1">
                            <circle cx="40" cy="37" r="19" fill="#ffffffee"/>
                            <path d="M34 27h9.5l4.5 4.5V47h-14z" fill="none" stroke="#2d54d8" stroke-width="1.8"
                                  stroke-linejoin="round"/>
                            <path d="M36 35h6M36 39.5h6M36 44h4" stroke="#2d54d8" stroke-width="1.6"
                                  stroke-linecap="round"/>
                        </g>

                        <g class="cv-node cv-n2">
                            <circle cx="110" cy="37" r="19" fill="#ffffffee"/>
                            <circle cx="110" cy="37" r="11" fill="none" stroke="#d5e3ec" stroke-width="3.4"/>
                            <circle cx="110" cy="37" r="11" fill="none" stroke="#16a29a" stroke-width="3.4"
                                    stroke-linecap="round" stroke-dasharray="48 70" transform="rotate(-90 110 37)"/>
                            <text x="110" y="41" text-anchor="middle" font-size="9" font-weight="700" fill="#12405c">82</text>
                        </g>

                        <g class="cv-node cv-n3">
                            <circle cx="180" cy="37" r="19" fill="#ffffffee"/>
                            <path d="M172 30h12l5 5v12h-17z" fill="none" stroke="#7a52e4" stroke-width="1.8"
                                  stroke-linejoin="round"/>
                            <path d="m176 38.5 2.6 2.6 5-5.6" fill="none" stroke="#16a29a" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </g>

                        <g class="cv-node cv-n4">
                            <circle cx="250" cy="37" r="19" fill="#ffffffee"/>
                            <path d="M243 30h14M243 37h9M243 44h11" stroke="#2d54d8" stroke-width="1.8"
                                  stroke-linecap="round"/>
                            <path d="M254 44v9M254 53l-3.4-3.6M254 53l3.4-3.6" fill="none" stroke="#2d54d8"
                                  stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>

                        <g class="cv-node cv-n5">
                            <circle cx="300" cy="37" r="15" fill="#ffffffee"/>
                            <circle cx="300" cy="34" r="3.6" fill="#7a52e4"/>
                            <path d="M293.5 45c.5-3.4 3.3-5.2 6.5-5.2s6 1.8 6.5 5.2" fill="#7a52e4" opacity=".85"/>
                        </g>
                    </svg>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="wrap footer-inner">
            <span data-id="Dirancang dengan Pertimbangan Kepatuhan OJK &amp; PDP" data-en="Designed with OJK &amp; PDP Compliance Considerations">Dirancang dengan Pertimbangan Kepatuhan OJK &amp; PDP</span>
            <div class="footer-badges">
                <span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.9"/>
                        <path d="M8.4 10V7.6a3.6 3.6 0 0 1 7.2 0V10" stroke="currentColor" stroke-width="1.9"
                              stroke-linecap="round"/>
                    </svg>
                    256-AES
                </span>
                <span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 2.8 19.5 6v6c0 5-3.2 8.2-7.5 9.2C7.7 20.2 4.5 17 4.5 12V6L12 2.8Z"
                              stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
                        <path d="M9 12l2.2 2.2L15.4 10" stroke="currentColor" stroke-width="1.9"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    ISO 27001
                </span>
                <span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="8.6" stroke="currentColor" stroke-width="1.9"/>
                        <path d="m8.4 12.3 2.4 2.4 4.8-5.1" stroke="currentColor" stroke-width="1.9"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span data-id="Dilindungi PDP" data-en="PDP Protected">Dilindungi PDP</span>
                </span>
            </div>
            <span data-id="&copy; {{ date('Y') }} InsureMind AI. Seluruh hak cipta dilindungi." data-en="&copy; {{ date('Y') }} InsureMind AI. All rights reserved.">&copy; {{ date('Y') }} InsureMind AI. Seluruh hak cipta dilindungi.</span>
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

        // ── Scroll reveal motion ─────────────────────────────────────────
        const revealItems = document.querySelectorAll(
            '.hero-copy, .mock, .hero-stat, .section-head, .grid-4 > *, .grid-3 > *, .loop > *, .svc-step, .svc-tile, .score-panel, .citation-card, .cta'
        );

        if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.body.classList.add('motion-ready');
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -36px 0px' });

            revealItems.forEach((item, index) => {
                item.classList.add('reveal-on-scroll');
                item.style.setProperty('--reveal-delay', `${(index % 4) * 75}ms`);
                revealObserver.observe(item);
            });
        }

        // ── Language switcher ───────────────────────────────────────────
        function applyLang(lang) {
            // Update all elements with data-id / data-en (text content)
            document.querySelectorAll('[data-id], [data-en]').forEach(el => {
                const text = lang === 'id' ? el.getAttribute('data-id') : el.getAttribute('data-en');
                if (text !== null && text !== '') {
                    el.innerHTML = text;
                }
            });

            // Update all elements with data-id-aria / data-en-aria (aria-label)
            document.querySelectorAll('[data-id-aria], [data-en-aria]').forEach(el => {
                const label = lang === 'id' ? el.getAttribute('data-id-aria') : el.getAttribute('data-en-aria');
                if (label !== null) {
                    el.setAttribute('aria-label', label);
                }
            });

            // Update <html lang> attribute
            document.getElementById('htmlRoot').setAttribute('lang', lang);

            // Update button active states
            document.getElementById('btnLangID').classList.toggle('active', lang === 'id');
            document.getElementById('btnLangEN').classList.toggle('active', lang === 'en');

            // Persist choice
            localStorage.setItem('im_lang', lang);
        }

        function setLang(lang) {
            applyLang(lang);
        }

        // On page load, restore saved language
        (function () {
            const saved = localStorage.getItem('im_lang') || 'id';
            applyLang(saved);
        })();
    </script>

    <span class="version-badge" aria-hidden="true">Beta v1.1</span>
</body>

</html>
