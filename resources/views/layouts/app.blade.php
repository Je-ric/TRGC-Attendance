<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'TRGC Attendance' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --red:         #ed213a;
            --red-dark:    #93291e;
            --surface:     #f5f4f6;
            --white:       #ffffff;
            --ink:         #1c1c1e;
            --ink-muted:   #6b6570;
            --ink-faint:   #a09aa4;
            --border:      #e4e0e2;
            --border-soft: #ede9eb;
            --shadow-card: 0 2px 16px rgba(0,0,0,.07);
            --shadow-lg:   0 8px 32px rgba(237,33,58,0.15);
            --focus-ring:  0 0 0 3px rgba(237,33,58,0.2);
            --sidebar-w:   240px;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--surface);
            color: var(--ink);
            margin: 0;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .font-display { font-family: 'Oswald', sans-serif; }

        .page-title {
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            letter-spacing: -0.01em;
            color: var(--ink);
        }

        .page-eyebrow {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--red);
        }

        /* ── Sidebar ── */
        #app-sidebar {
            position: fixed;
            top: 0; bottom: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, #1a0a08 0%, var(--red-dark) 100%);
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
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.9);
        }

        .nav-item.active {
            background: rgba(237,33,58,0.25);
            color: #fff;
            font-weight: 600;
            border-left-color: var(--red);
        }

        .nav-item i { font-size: 16px; flex-shrink: 0; }

        /* ── Top bar ── */
        #app-topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            box-shadow: var(--shadow-card);
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
            #app-content { margin-left: var(--sidebar-w); }
        }

        #app-main {
            flex: 1;
            padding: 24px;
            max-width: 1280px;
            width: 100%;
            margin: 0 auto;
        }

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

        /* ── Native dialog backdrop ── */
        dialog::backdrop {
            background: rgba(28,28,30,0.5);
            backdrop-filter: blur(3px);
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
                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#93291e,#ed213a);display:flex;align-items:center;justify-content:center">
                    <i class='bx bx-user text-white text-sm'></i>
                </div>
            </div>
        </header>

        <main id="app-main">
            @yield('content')
        </main>
    </div>

    {{-- Global toasts --}}
    @if(session('toast'))
        <x-feedback-status.toast
            :type="session('toast')['type']"
            :message="session('toast')['message']" />
    @endif
    <x-feedback-status.toast />

    @livewireScripts
    @stack('scripts')
    @stack('modals')
    <script>
        const sidebar  = document.getElementById('app-sidebar');
        const overlay  = document.getElementById('sidebar-overlay');
        const openBtn  = document.getElementById('sidebar-open');
        const closeBtn = document.getElementById('sidebar-close');

        const openSidebar  = () => { sidebar.style.transform = 'translateX(0)'; overlay.classList.remove('hidden'); };
        const closeSidebar = () => { sidebar.style.transform = ''; overlay.classList.add('hidden'); };

        openBtn?.addEventListener('click', openSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) overlay.classList.add('hidden');
        });

        // Livewire modal bridge — open-modal / close-modal events
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-modal',  ({ id }) => document.getElementById(id)?.showModal());
            Livewire.on('close-modal', ({ id }) => document.getElementById(id)?.close());
        });
    </script>
</body>
</html>
