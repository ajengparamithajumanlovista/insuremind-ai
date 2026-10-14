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
            width: 38px;
            height: 38px;
            flex: none;
            border-radius: 11px;
            display: grid;
            place-items: center;
            color: #4568dc;
            background: linear-gradient(150deg, #eef3ff, #e3ebfd);
        }

        .svc-glyph.g-ok { color: #1f8a55; background: linear-gradient(150deg, #e9f8f0, #dff3e8); }
        .svc-glyph.g-warn { color: #b47b1c; background: linear-gradient(150deg, #fdf5e8, #faedd6); }
        .svc-glyph.g-ink {
            color: #44506b;
            background: linear-gradient(150deg, #f1f4fa, #e7ecf6);
        }

        .svc-tile b { display: block; font-size: 11.5px; color: #2b3a55; letter-spacing: -.1px; }
        .svc-tile small { color: var(--soft); font-size: 9.5px; }

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
            font-size: 9px;
            font-weight: 700;
            border-radius: 6px;
            padding: 4px 8px;
            background: #eef3ff;
            color: #3f5fc4;
        }

        .svc-state.ok { background: #e7f7ee; color: #1f8a55; }

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
        .hero-stat:nth-child(3n + 1) .stat-ico { color: var(--blue-dark); background: #e8edff; }
        .hero-stat:nth-child(3n + 2) .stat-ico { color: var(--teal-dark); background: #e2f7f6; }
        .hero-stat:nth-child(3n + 3) .stat-ico { color: var(--purple-dark); background: #f0eaff; }

        .brand-mark {
            background: linear-gradient(145deg, #4778f5, var(--blue-dark));
            box-shadow: 0 7px 16px #3b5fe833;
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

        .svc-tile:nth-child(3n + 1) .svc-glyph {
            color: var(--blue-dark);
            background: linear-gradient(145deg, #e8edff, #dfe6ff);
        }

        .svc-tile:nth-child(3n + 2) .svc-glyph {
            color: var(--teal-dark);
            background: linear-gradient(145deg, #e2f7f6, #d3f0ee);
        }

        .svc-tile:nth-child(3n) .svc-glyph {
            color: var(--purple-dark);
            background: linear-gradient(145deg, #f0eaff, #e7ddff);
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
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 2.8 20 6v5.2c0 5.1-3.4 8.5-8 10-4.6-1.5-8-4.9-8-10V6l8-3.2Z"
                              stroke="currentColor" stroke-width="1.8"/>
                        <path d="m8.5 12 2.2 2.2 4.8-5" stroke="currentColor" stroke-width="1.8"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
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
                        <a href="#how" class="btn btn-ghost">&#x25B7; <span data-id="Lihat Cara Kerjanya" data-en="See How It Works">Lihat Cara Kerjanya</span></a>
                    </div>
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <span class="stat-ico">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 19V9M10 19V5M16 19v-6M22 19H2" stroke="currentColor"
                                          stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <b>82%</b>
                            <small data-id="Rata-rata Kesiapan Klaim" data-en="Avg. Claim Readiness">Rata-rata Kesiapan Klaim</small>
                        </div>
                        <div class="hero-stat">
                            <span class="stat-ico">
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
                            <span class="stat-ico">
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
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2.6 20 6v6.4c0 5.3-3.4 8.5-8 9.6-4.6-1.1-8-4.3-8-9.6V6l8-3.4Z"
                                      stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <b data-id="Suite Layanan InsureMind" data-en="InsureMind Service Suite">InsureMind Service Suite</b>
                            <small data-id="Pipeline klaim end&#8209;to&#8209;end" data-en="End&#8209;to&#8209;end claim pipeline">Pipeline klaim end&#8209;to&#8209;end</small>
                        </div>
                        <span class="tag">&#x25CF; <span data-id="6 Layanan" data-en="6 Services">6 Layanan</span></span>
                    </div>

                    <div class="mock-body">
                        <div class="mock-label" data-id="Layanan Inti InsureMind" data-en="InsureMind Core Services">Layanan Inti InsureMind</div>
                        <div class="svc-viz">
                            <div class="svc-tile">
                                <span class="svc-glyph">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 6h11l4 4v8a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1Z"
                                              stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M14 6v5h5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Penguraian Polis" data-en="Policy Parsing">Policy Parsing</b><small data-id="Pasal terstruktur" data-en="Structured clauses">Pasal terstruktur</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph">
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
                                <span class="svc-glyph g-warn">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M7 3h7l4 4v14H7z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M14 3v4h4M9.5 13h5M9.5 16.5h5" stroke="currentColor" stroke-width="1.6"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Ekspor Dossier" data-en="Dossier Export">Dossier Export</b><small data-id="Berkas siap kirim" data-en="Submission-ready file">Berkas siap kirim</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph">
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
                            <span data-id="&#x2733; Enam layanan, satu pipeline terintegrasi" data-en="&#x2733; Six services, one integrated pipeline">&#x2733; Enam layanan, satu pipeline terintegrasi</span>
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
                                <span class="svc-state ok" data-id="Siap" data-en="Ready">Siap</span>
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
                                <span class="svc-state">Engine v2.4</span>
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
                                <span class="svc-state" data-id="4 Item" data-en="4 Items">4 Items</span>
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
                                <span class="svc-state ok" data-id="Terverifikasi" data-en="Grounded">Terverifikasi</span>
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
                                <span class="svc-state" data-id="Ekspor" data-en="Export">Ekspor</span>
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
                            <span>&#x2733; <span data-id="Semua layanan berjalan dari" data-en="All services run from">Semua layanan berjalan dari</span> <b data-id="satu dashboard" data-en="one dashboard">satu dashboard</b> <span data-id="terintegrasi" data-en="integrated">terintegrasi</span></span>
                            <a href="{{ route('login') }}" class="btn btn-ghost" style="padding:7px 12px;font-size:11.5px" data-id="Coba Sekarang &#x2192;" data-en="Try Now &#x2192;">
                                Coba Sekarang &#x2192;
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
                                <span class="svc-glyph">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 6h11l4 4v8a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1Z"
                                              stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M14 6v5h5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Penyerapan Polis" data-en="Policy Ingestion">Policy Ingestion</b><small data-id="PDF polis dipecah jadi pasal" data-en="Policy PDF split into clauses">PDF polis dipecah jadi pasal</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph">
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
                                <span class="svc-glyph g-warn">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                        <path d="M7 3h7l4 4v14H7z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <path d="M14 3v4h4M9.5 13h5M9.5 16.5h5" stroke="currentColor" stroke-width="1.6"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div><b data-id="Pengemasan Dossier" data-en="Dossier Packaging">Dossier Packaging</b><small data-id="Berkas siap kirim" data-en="Submission-ready file">Berkas siap kirim</small></div>
                            </div>
                            <div class="svc-tile">
                                <span class="svc-glyph">
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
                        <span class="card-icon">&#x2713;</span>
                        <b data-id="PENENTUAN ALGORITMIK" data-en="ALGORITHMIC DETERMINATION">PENENTUAN ALGORITMIK</b>
                        <span class="conf" data-id="87% Kepercayaan" data-en="87% Confidence">87% Kepercayaan</span>
                        <span class="model">&#x2733; Model DeepClaim-v4-1D8</span>
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
                        <div class="mock-label" data-id="&#x25A4; Pasal 4.2 Cakupan Bencana Alam" data-en="&#x25A4; Policy Section 4.2 Natural Disaster Coverage">&#x25A4; Pasal 4.2 Cakupan Bencana Alam</div>
                        <p data-id='"Penanggung setuju untuk mengganti kerugian tertanggung atas kerusakan banjir mendadak, genangan air topan, dan kerusakan mesin akibat badai hingga batas agregat yang ditentukan sebesar IDR 350.000.000, tunduk pada deductible standar."' data-en='"The underwriter agrees to indemnify the insured against sudden flood damage, typhoon water inundation, and storm-related mechanical engine stalls up to the specified aggregate limit of IDR 350,000,000, subject to standard deductible."'>
                            "Penanggung setuju untuk mengganti kerugian tertanggung atas kerusakan banjir mendadak, genangan air topan, dan kerusakan mesin akibat badai hingga batas agregat yang ditentukan sebesar IDR 350.000.000, tunduk pada deductible standar."
                        </p>
                        <div class="quote-foot">
                            <span data-id="Kemiripan Dekat: 0,942 terhadap Deskripsi Insiden" data-en="Close Similarity: 0.942 against Incident Description">Kemiripan Dekat: 0,942 terhadap Deskripsi Insiden</span>
                            <a href="#citations" data-id="Lihat Pasal Polis &#x2197;" data-en="View Policy Clause &#x2197;">Lihat Pasal Polis &#x2197;</a>
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
                        <div class="loop-icon">&#x25EB;</div>
                        <b data-id="Pengguna" data-en="User">Pengguna</b>
                        <small data-id="Pengajuan &amp; Orientasi" data-en="Submission &amp; Onboarding">Pengajuan &amp; Orientasi</small>
                    </div>
                    <div class="card loop-step">
                        <div class="loop-icon">&#x2733;</div>
                        <b data-id="Analisis AI" data-en="AI Analysis">Analisis AI</b>
                        <small data-id="Memproses OCR &amp; data idempoten" data-en="Parses OCR &amp; idempotent data">Memproses OCR &amp; data idempoten</small>
                    </div>
                    <div class="card loop-step">
                        <div class="loop-icon">&#x25A4;</div>
                        <b data-id="Bukti" data-en="Evidence">Bukti</b>
                        <small data-id="Indeks pasal yang diverifikasi mesin" data-en="Machine-verified clause index">Indeks pasal yang diverifikasi mesin</small>
                    </div>
                    <div class="card loop-step">
                        <div class="loop-icon">&#x2713;</div>
                        <b data-id="Rekomendasi" data-en="Recommendation">Rekomendasi</b>
                        <small data-id="Skor kesiapan &amp; ringkasan" data-en="Readiness score &amp; brief">Skor kesiapan &amp; ringkasan</small>
                    </div>
                    <div class="card loop-step emphasis">
                        <div class="loop-icon">&#x2699;</div>
                        <b data-id="Tinjauan Manusia" data-en="Human Review">Tinjauan Manusia</b>
                        <small data-id="Pemeriksaan adjudicator berlisensi" data-en="Licensed adjudicator check">Pemeriksaan adjudicator berlisensi</small>
                    </div>
                    <div class="card loop-step">
                        <div class="loop-icon">&#x25F7;</div>
                        <b data-id="Keputusan Akhir" data-en="Final Decision">Keputusan Akhir</b>
                        <small data-id="Persetujuan klaim resmi" data-en="Official claim approval">Persetujuan klaim resmi</small>
                    </div>
                </div>

                <div class="notice">
                    <span>&#x2713;</span>
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
                        <span class="process-tag" data-id="&#x25A4; Disanitasi sisi klien" data-en="&#x25A4; Client-side sanitized">&#x25A4; Disanitasi sisi klien</span>
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
                        <span class="process-tag" data-id="&#x25A4; Indeks pasal dibuat" data-en="&#x25A4; Clause index generated">&#x25A4; Indeks pasal dibuat</span>
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
                        <span class="process-tag" data-id="&#x25CB; Skor 0-100% instan" data-en="&#x25CB; Instant 0-100% score">&#x25CB; Skor 0-100% instan</span>
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
                        <span class="compliance-foot" data-id="&#x25CE; Deteksi Yurisdiksi Lokal" data-en="&#x25CE; Local-Jurisdiction Detection">&#x25CE; Deteksi Yurisdiksi Lokal</span>
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
                        <span class="compliance-foot" data-id="&#x25CE; Kontrol Selaras ISO 27001" data-en="&#x25CE; ISO 27001 Aligned Controls">&#x25CE; Kontrol Selaras ISO 27001</span>
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
                        <span class="compliance-foot" data-id="&#x25CB; Alat AI Non-Adjudicating" data-en="&#x25CB; Non-Adjudicating AI Tool">&#x25CB; Alat AI Non-Adjudicating</span>
                    </article>
                </div>
            </div>
        </section>

        <!-- ── CTA ────────────────────────────────────────── -->
        <section class="section" id="architecture">
            <div class="wrap">
                <div class="cta">
                    <span class="cta-tag" data-id="&#x2733; Perlindungan Data Industri" data-en="&#x2733; Industry Data Protection">&#x2733; Perlindungan Data Industri</span>
                    <h2 data-id="Kendalikan Cakupan Asuransi Anda Hari Ini" data-en="Take Control of Your Insurance Coverage Today">Kendalikan Cakupan Asuransi Anda Hari Ini</h2>
                    <p data-id="Unggah polis Anda dalam kurang dari 60 detik dan rasakan kejelasan panduan AI berbasis bukti." data-en="Upload your policy in less than 60 seconds and experience the clarity of evidence-grounded AI guidance.">Unggah polis Anda dalam kurang dari 60 detik dan rasakan kejelasan panduan AI berbasis bukti.</p>
                    <div class="cta-actions">
                        <a href="{{ route('login') }}" class="btn btn-light" data-id="&#x2191; Mulai Sekarang" data-en="&#x2191; Get Started Now">&#x2191; Mulai Sekarang</a>
                        <a href="#how" class="btn btn-outline-light" data-id="&#x25B7; Jelajahi Dokumentasi" data-en="&#x25B7; Explore Documentation">&#x25B7; Jelajahi Dokumentasi</a>
                    </div>
                    <div class="cta-foot">
                        <span data-id="&#x25CB; Tidak perlu kartu kredit" data-en="&#x25CB; No credit card required">&#x25CB; Tidak perlu kartu kredit</span>
                        <span data-id="&#x2733; Ekstraksi OCR Instan" data-en="&#x2733; Instant OCR Extraction">&#x2733; Ekstraksi OCR Instan</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="wrap footer-inner">
            <span data-id="Dirancang dengan Pertimbangan Kepatuhan OJK &amp; PDP" data-en="Designed with OJK &amp; PDP Compliance Considerations">Dirancang dengan Pertimbangan Kepatuhan OJK &amp; PDP</span>
            <div class="footer-badges">
                <span>&#x25A4; 256-AES</span>
                <span>&#x25CE; ISO 27001</span>
                <span data-id="&#x2713; Dilindungi PDP" data-en="&#x2713; PDP Protected">&#x2713; Dilindungi PDP</span>
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
