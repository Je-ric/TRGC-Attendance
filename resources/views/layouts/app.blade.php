<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'TRGC Attendance' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Sora:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --red:          #ed213a;
            --red-dark:     #93291e;
            --muted:        #bcabae;
            --surface:      #f5f4f6;
            --white:        #ffffff;
            --ink:          #1c1c1e;
            --ink-muted:    #6b6570;
            --ink-faint:    #a09aa4;
            --border:       #e4e0e2;
            --border-soft:  #ede9eb;
            --shadow-sm:    0 1px 3px rgba(28,28,30,0.08);
            --shadow-md:    0 4px 16px rgba(28,28,30,0.10);
            --shadow-lg:    0 8px 32px rgba(28,28,30,0.13);
            --focus-ring:   0 0 0 3px rgba(237,33,58,0.18);
            --sidebar-w:    240px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--surface);
            color: var(--ink);
            margin: 0;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Typography ── */
        .font-display { font-family: 'Sora', sans-serif; }

        .page-title {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--ink);
        }

        .page-eyebrow {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--red);
        }

        /* ── Sidebar ── */
        #app-sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--red-dark);
            display: flex;
            flex-direction: column;
            z-index: 40;
            transform: translateX(-100%);
            transition: transform 0.25s cubic-bezier(.4,0,.2,1);
            overflow-y: auto;
        }

        @media (min-width: 1024px) {
            #app-sidebar { transform: translateX(0); }
        }

        .sidebar-brand {
            padding: 20px 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-brand-inner { display: flex; align-items: center; gap: 10px; }

        .sidebar-logo {
            width: 34px; height: 34px;
            background: rgba(255,255,255,0.12);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-nav { padding: 12px 10px; flex: 1; }

        .sidebar-section-label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: rgba(255,255,255,0.28);
            padding: 0 8px;
            margin: 8px 0 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 10px;
            border-radius: 7px;
            font-size: 13.5px;
            font-weight: 500;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            transition: all 0.15s ease;
            margin-bottom: 1px;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.9);
        }

        .nav-item.active {
            background: var(--red);
            color: #fff;
            font-weight: 600;
        }

        .nav-item i { font-size: 16px; flex-shrink: 0; }

        /* ── Top bar ── */
        #app-topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 0 24px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* ── Main layout ── */
        #app-content {
            margin-left: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.25s cubic-bezier(.4,0,.2,1);
        }

        @media (min-width: 1024px) {
            #app-content {
                margin-left: var(--sidebar-w);
                min-height: 100vh;
            }
        }

        #app-main {
            flex: 1;
            padding: 24px;
            max-width: 1280px;
            width: 100%;
            margin: 0 auto;
        }

        /* ── Cards ── */
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
        }

        .card-soft {
            background: var(--surface);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
        }

        /* ── Divider ── */
        .ui-divider {
            border: 0;
            height: 1px;
            background: var(--border);
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            cursor: pointer;
            white-space: nowrap;
            line-height: 1;
            text-decoration: none;
        }

        .btn:active { transform: scale(0.98); }

        .btn-primary {
            background: var(--red);
            color: #fff;
            border-color: var(--red-dark);
        }
        .btn-primary:hover { background: var(--red-dark); }

        .btn-secondary {
            background: var(--red-dark);
            color: #fff;
        }
        .btn-secondary:hover { background: #7a1f15; }

        .btn-ghost {
            background: var(--white);
            border-color: var(--border);
            color: var(--ink-muted);
        }
        .btn-ghost:hover { background: var(--surface); color: var(--ink); border-color: var(--muted); }

        .btn-danger {
            background: #fff0f0;
            border-color: #ffd0d0;
            color: #c0392b;
        }
        .btn-danger:hover { background: #ffe0e0; }

        .btn-edit {
            background: #f0f4ff;
            border-color: #d0dcff;
            color: #3b5bdb;
        }
        .btn-edit:hover { background: #e0e8ff; }

        /* legacy aliases */
        .ui-btn { display: inline-flex; align-items: center; gap: 6px; border-radius: 8px; padding: 8px 14px; font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif; transition: all 0.15s ease; border: 1px solid transparent; cursor: pointer; white-space: nowrap; line-height: 1; text-decoration: none; }
        .ui-btn:active { transform: scale(0.98); }
        .ui-btn-primary { background: var(--red); color: #fff; border-color: var(--red-dark); }
        .ui-btn-primary:hover { background: var(--red-dark); }
        .ui-btn-maroon { background: var(--red-dark); color: #fff; }
        .ui-btn-maroon:hover { background: #7a1f15; }
        .ui-btn-ghost { background: var(--white); border-color: var(--border); color: var(--ink-muted); }
        .ui-btn-ghost:hover { background: var(--surface); color: var(--ink); }
        .ui-btn-delete { background: #fff0f0; border-color: #ffd0d0; color: #c0392b; }
        .ui-btn-delete:hover { background: #ffe0e0; }
        .ui-btn-edit { background: #f0f4ff; border-color: #d0dcff; color: #3b5bdb; }
        .ui-btn-edit:hover { background: #e0e8ff; }

        /* ── Inputs ── */
        .ui-input {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 9px 12px;
            background: var(--white);
            color: var(--ink);
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .ui-input:focus {
            border-color: var(--red);
            box-shadow: var(--focus-ring);
        }
        .ui-input::placeholder { color: var(--ink-faint); }

        /* ── Form label ── */
        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--ink-muted);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        /* ── Badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        .badge-red     { background: rgba(237,33,58,0.10); color: var(--red-dark); border: 1px solid rgba(237,33,58,0.20); }
        .badge-muted   { background: var(--surface); color: var(--ink-muted); border: 1px solid var(--border); }
        .badge-present { background: #f0fdf4; color: #15803d; border: 1px solid #86efac; }
        .badge-absent  { background: var(--surface); color: var(--ink-faint); border: 1px solid var(--border); }
        /* legacy */
        .badge-gold    { background: rgba(237,33,58,0.08); color: var(--red-dark); border: 1px solid rgba(237,33,58,0.18); }

        /* ── Stat card ── */
        .stat-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 18px;
            box-shadow: var(--shadow-sm);
        }
        .stat-value {
            font-family: 'Sora', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
        }
        .stat-label {
            font-size: 11.5px;
            font-weight: 500;
            color: var(--ink-faint);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        /* ── Toast ── */
        .toast-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; border-radius: 8px; padding: 10px 14px; font-size: 13px; }
        .toast-error   { background: #fff0f0; border: 1px solid #fca5a5; color: #991b1b; border-radius: 8px; padding: 10px 14px; font-size: 13px; }
        .toast-info    { background: #eff6ff; border: 1px solid #93c5fd; color: #1e3a8a; border-radius: 8px; padding: 10px 14px; font-size: 13px; }

        /* ── Check-in row ── */
        .checkin-row {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            border-bottom: 1px solid var(--border-soft);
            cursor: pointer;
            transition: background 0.1s;
            border-left: 3px solid transparent;
        }
        .checkin-row:hover { background: var(--surface); }
        .checkin-row.is-checked {
            background: rgba(237,33,58,0.05);
            border-left-color: var(--red);
        }

        /* ── Table ── */
        .kl-table-wrap { border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-sm); }
        .kl-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .kl-table thead th {
            background: var(--surface);
            padding: 10px 16px;
            text-align: left;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--ink-muted);
            border-bottom: 1px solid var(--border);
        }
        .kl-table tbody tr { border-bottom: 1px solid var(--border-soft); }
        .kl-table tbody tr:last-child { border-bottom: none; }
        .kl-table tbody tr:hover { background: var(--surface); }
        .kl-table td { padding: 11px 16px; color: var(--ink); vertical-align: middle; }

        /* ── Modal backdrop ── */
        .modal-backdrop {
            position: fixed; inset: 0;
            background: rgba(28,28,30,0.45);
            backdrop-filter: blur(3px);
            z-index: 50;
            display: flex; align-items: center; justify-content: center;
            padding: 16px;
        }

        .modal-box {
            background: var(--white);
            border-radius: 14px;
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 480px;
            padding: 28px;
        }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body>
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>

    <aside id="app-sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-inner">
                <div class="sidebar-logo">
                    <i class='bx bx-cross text-white text-lg'></i>
                </div>
                <div>
                    <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.16em;color:rgba(255,255,255,0.45)">TRGC</div>
                    <div class="font-display" style="font-size:15px;font-weight:700;color:#fff;line-height:1.1">Attendance</div>
                </div>
            </div>
            <button id="sidebar-close" class="lg:hidden" style="color:rgba(255,255,255,0.5);background:none;border:none;cursor:pointer;padding:4px">
                <i class="bx bx-x text-xl"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Menu</div>
            @php
                $navItems = [
                    ['route' => 'attendance.index',   'icon' => 'bx-check-shield', 'label' => 'Attendance'],
                    ['route' => 'attendance.records', 'icon' => 'bx-folder-open',  'label' => 'Records'],
                    ['route' => 'people.index',       'icon' => 'bx-group',         'label' => 'People'],
                    ['route' => 'families.index',     'icon' => 'bx-home-heart',    'label' => 'Families'],
                ];
            @endphp
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="nav-item {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                    <i class='bx {{ $item["icon"] }}'></i>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div style="padding:14px 18px;border-top:1px solid rgba(255,255,255,0.08)">
            <div style="font-size:10px;color:rgba(255,255,255,0.25);font-weight:500">
                {{ now()->format('l, F j') }}
            </div>
        </div>
    </aside>

    <div id="app-content">
        <header id="app-topbar">
            <div style="display:flex;align-items:center;gap:12px">
                <button id="sidebar-open" class="lg:hidden" style="background:none;border:none;cursor:pointer;color:var(--ink-muted);padding:4px;display:flex;align-items:center">
                    <i class="bx bx-menu text-xl"></i>
                </button>
                <div>
                    <div class="page-eyebrow">Management System</div>
                    <div class="font-display" style="font-size:15px;font-weight:700;color:var(--ink);line-height:1.2">Workspace</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
                <div style="width:32px;height:32px;border-radius:50%;background:var(--red);display:flex;align-items:center;justify-content:center">
                    <i class='bx bx-user text-white text-sm'></i>
                </div>
            </div>
        </header>

        <main id="app-main">
            @yield('content')
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
    @stack('modals')
    <script>
        const sidebar  = document.getElementById('app-sidebar');
        const overlay  = document.getElementById('sidebar-overlay');
        const openBtn  = document.getElementById('sidebar-open');
        const closeBtn = document.getElementById('sidebar-close');

        const open  = () => { sidebar.style.transform = 'translateX(0)'; overlay.classList.remove('hidden'); };
        const close = () => { sidebar.style.transform = ''; overlay.classList.add('hidden'); };

        openBtn?.addEventListener('click', open);
        closeBtn?.addEventListener('click', close);
        overlay?.addEventListener('click', close);

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) { overlay.classList.add('hidden'); }
        });
    </script>
</body>
</html>
