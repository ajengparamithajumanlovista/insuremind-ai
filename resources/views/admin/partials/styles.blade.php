/* ── Design tokens (aligned with the user layout) ──────── */
:root {
    --ink: #17243b;
    --muted: #77839a;
    --line: #e7ebf2;
    --blue: #315ee8;
    --pale: #f6f8fc;
    --green: #17a673;
    --amber: #d98b12;
    --rose: #d8446a;
    --purple: #7a4fd6;
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

/* ── Sidebar ─────────────────────────────────────────────── */
.sidebar {
    position: fixed;
    inset: 0 auto 0 0;
    width: 250px;
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
    padding: 2px 11px 26px;
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

.brand-badge {
    display: inline-block;
    margin-left: 6px;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .8px;
    text-transform: uppercase;
    color: #2a53c4;
    background: #eef3ff;
    border-radius: 999px;
    padding: 2px 7px;
    vertical-align: middle;
}

.nav-label {
    font-size: 10px;
    color: #a0a9b8;
    font-weight: 750;
    letter-spacing: 1.2px;
    padding: 16px 12px 10px;
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

.nav-icon {
    display: grid;
    place-items: center;
    flex: none;
    width: 32px;
    height: 32px;
    border-radius: 10px;
    color: #63789d;
    background: #f3f6fb;
    transition: color .18s ease, background .18s ease, box-shadow .18s ease, transform .18s ease;
}

.nav-icon svg { width: 17px; height: 17px; }
.nav a:hover .nav-icon { color: #315ee8; background: #fff; transform: translateY(-1px); }
.nav a.active .nav-icon { color: #315ee8; background: #fff; box-shadow: 0 2px 7px #315ee81a; }

.nav-count {
    margin-left: auto;
    font-size: 10.5px;
    font-weight: 750;
    color: #8490a4;
    background: #f2f4f8;
    border-radius: 999px;
    padding: 1px 8px;
}

.nav a.active .nav-count { background: #dde7ff; color: #2a53c4; }

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

/* ── Main layout ─────────────────────────────────────────── */
.main {
    margin-left: 250px;
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

/* ── Flash messages ─────────────────────────────────────── */
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


/* ── Cards ───────────────────────────────────────────────── */
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

/* Spacing between summary blocks on the admin page. */
.dash-section { margin-bottom: 16px; }
.card-accent { border-left: 4px solid var(--blue); }
.section-note { font-size: 12px; color: #4f5c72; margin: 0; line-height: 1.55; }

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
.pill-purple { background: #f3eeff; color: #6a3fd0; }

/* ── Buttons ─────────────────────────────────────────────── */
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

/* ── Forms ───────────────────────────────────────────────── */
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

.filters {
    display: flex;
    align-items: end;
    gap: 12px;
    flex-wrap: wrap;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 14px;
    padding: 15px 17px;
    margin-bottom: 16px;
}

.filters .field { min-width: 165px; }


/* ── Tables ──────────────────────────────────────────────── */
.table-wrap { overflow-x: auto; border: 1px solid var(--line); border-radius: 14px; background: #fff; }

table.data { width: 100%; border-collapse: collapse; font-size: 12.5px; }
table.data th, table.data td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--line); vertical-align: top; }
table.data th { background: #f7f9fd; font-size: 10.5px; color: #79859a; text-transform: uppercase; letter-spacing: .6px; font-weight: 750; }
table.data tr:last-child td { border-bottom: 0; }
table.data td.num { text-align: right; font-variant-numeric: tabular-nums; }
table.data tr.best { background: #f4fbf8; }
table.data .muted { color: var(--muted); font-size: 11px; }
table.data .row-actions { display: flex; gap: 6px; flex-wrap: wrap; }

/* ── Progress / meters ───────────────────────────────────── */
.meter { height: 8px; border-radius: 999px; background: #eef1f7; overflow: hidden; }
.meter > span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #4778f5, #315ee8); transition: width .6s ease; }
.meter.green > span { background: linear-gradient(90deg, #34c48f, #17a673); }
.meter.amber > span { background: linear-gradient(90deg, #f0b23f, #d98b12); }
.meter.rose > span { background: linear-gradient(90deg, #f0718f, #d8446a); }

/* ── Timeline (processing & review history) ──────────────── */
.timeline { display: grid; gap: 0; }

.timeline-item {
    display: grid;
    grid-template-columns: 18px minmax(0, 1fr);
    gap: 12px;
    padding-bottom: 16px;
    position: relative;
}

.timeline-item:not(:last-child)::before {
    content: "";
    position: absolute;
    left: 8px;
    top: 18px;
    bottom: 0;
    width: 2px;
    background: var(--line);
}

.timeline-dot {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px var(--line);
    margin-top: 1px;
}

.timeline-dot.green { background: var(--green); box-shadow: 0 0 0 2px #c7eedd; }
.timeline-dot.rose { background: var(--rose); box-shadow: 0 0 0 2px #f5cfd9; }
.timeline-dot.blue { background: var(--blue); box-shadow: 0 0 0 2px #dbe5ff; }
.timeline-dot.amber { background: var(--amber); box-shadow: 0 0 0 2px #f6e3bd; }
.timeline-dot.grey { background: #b6bfcd; }

.timeline-body b { font-size: 12.5px; display: block; }
.timeline-body span { font-size: 11px; color: var(--muted); }
.timeline-body p { margin: 4px 0 0; font-size: 12px; color: #4f5c72; }

/* ── Diff (before / after) ───────────────────────────────── */
.diff { display: grid; gap: 8px; }
.diff-row { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 8px; font-size: 11.5px; }
.diff-cell { border: 1px solid var(--line); border-radius: 9px; padding: 8px 10px; background: #fbfcfe; }
.diff-cell.old { background: #fdf1f4; border-color: #f5cfd9; color: #9b2e49; }
.diff-cell.new { background: #eefaf4; border-color: #c7eedd; color: #14785a; }
.diff-cell small { display: block; color: #8490a4; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 2px; }

/* ── Empty state ─────────────────────────────────────────── */
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

/* ── Pagination ──────────────────────────────────────────── */
.pager { margin-top: 16px; }
.pager nav { font-size: 12px; }
.pager svg { width: 16px; height: 16px; display: inline-block; vertical-align: middle; }

/* ── Detail list ─────────────────────────────────────────── */
.kv { display: grid; gap: 10px; }
.kv-row { display: grid; grid-template-columns: 150px minmax(0, 1fr); gap: 10px; font-size: 12.5px; padding-bottom: 10px; border-bottom: 1px solid var(--line); }
.kv-row:last-child { border-bottom: 0; padding-bottom: 0; }
.kv-row dt { color: var(--muted); font-size: 11px; font-weight: 650; text-transform: uppercase; letter-spacing: .4px; margin: 0; }
.kv-row dd { margin: 0; }
/* Summary row with the label on the left and value on the right. */
.kv-row-split { grid-template-columns: minmax(0, 1fr) auto; align-items: center; }
.kv-strong { font-weight: 750; }

/* ── Responsive ──────────────────────────────────────────── */
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
    .diff-row { grid-template-columns: minmax(0, 1fr); }
    .kv-row { grid-template-columns: minmax(0, 1fr); }
    .kv-row-split { grid-template-columns: minmax(0, 1fr) auto; }
}
