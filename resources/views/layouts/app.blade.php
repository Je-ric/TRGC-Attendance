<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'TRGC Attendance' }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Oswald:wght@300;400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        #kingdom-navbar .brand-font {
            color: #1A1A2E;
            transition: color 0.3s;
        }
        #kingdom-navbar[data-scrolled="true"] .brand-font {
            color: #FFF5D6;
        }
        #kingdom-navbar .brand-font.active-link {
            color: #C9A84C;
        }
        :root {
            --maroon:         #6B0F1A;
            --maroon-light:   #8B2635;
            --gold:           #C9A84C;
            --gold-light:     #DFC056;
            --gold-muted:     rgba(201,168,76,0.14);
            --gold-border:    rgba(201,168,76,0.30);
            --ink:            #1A1A2E;
            --ink-muted:      #4A4A6A;
            --ink-faint:      #8888A8;
            --surface:        #F8F7F4;
            --surface-card:   #FFFFFF;
            --surface-soft:   #FDFBF5;
            --border:         #EAE3D0;
            --border-strong:  #D4C5A0;
            --shadow-card:    0 1px 3px rgba(26,26,46,0.07), 0 1px 2px rgba(26,26,46,0.04);
            --shadow-modal:   0 4px 20px rgba(26,26,46,0.14);
            --shadow-focus:   0 0 0 3px rgba(201,168,76,0.24);
            /* legacy compat */
            --kingdom-maroon: #6B0F1A;
            --kingdom-gold:   #C9A84C;
            --kingdom-gold-gradient: linear-gradient(135deg, #B8952A, #DFC056);
            --kingdom-maroon-gradient: linear-gradient(135deg, #6B0F1A, #8B2635);
            --kingdom-border: rgba(107,15,26,0.16);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 8% 8%, rgba(201,168,76,0.08), transparent 40%),
                radial-gradient(circle at 92% 12%, rgba(107,15,26,0.06), transparent 36%),
                var(--surface);
        }

        .brand-font {
            font-family: 'Cinzel', serif;
        }

        .page-title {
            font-family: 'Oswald', sans-serif;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .dm-sans {
            font-family: 'DM Sans', sans-serif;
        }

        /* ── Shell ── */
        .kingdom-shell {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid var(--gold-border);
            box-shadow: var(--shadow-card);
            backdrop-filter: blur(2px);
        }

        /* ── Navbar ── */
        /* ===== Gradient Glass Navbar ===== */

#kingdom-navbar {
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    border-bottom: 1px solid rgba(212, 175, 55, 0.18);
    transition: all 0.35s ease;
}

/* Top state (light glass) */
#kingdom-navbar[data-scrolled="false"] {
    background:
        linear-gradient(
            135deg,
            rgba(107, 15, 26, 0.55),
            rgba(139, 38, 53, 0.55)
        );
}

/* Scrolled state (deeper royal glass) */
#kingdom-navbar[data-scrolled="true"] {
    background:
        linear-gradient(
            135deg,
            rgba(107, 15, 26, 0.88),
            rgba(139, 38, 53, 0.92)
        );
    border-bottom: 1px solid rgba(212, 175, 55, 0.28);
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.25);
}

    #kingdom-navbar {
        transition: all 0.35s cubic-bezier(.4,0,.2,1);
        border-bottom: 1px solid rgba(212, 175, 55, 0.18);
        backdrop-filter: blur(14px) saturate(140%);
        -webkit-backdrop-filter: blur(14px) saturate(140%);
    }

    #kingdom-navbar[data-scrolled="false"] {
        background: rgba(255,255,255,0.18);
        color: #1A1A2E;
        box-shadow: 0 2px 12px 0 rgba(0,0,0,0.07);
    }

    #kingdom-navbar[data-scrolled="true"] {
        background: linear-gradient(90deg, #6B0F1A 0%, #8B2635 100%);
        color: #FFF5D6;
        border-bottom: 1px solid rgba(212, 175, 55, 0.28);
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.25);
    }

    #kingdom-navbar .nav-link {
        color: #1A1A2E;
    }
    #kingdom-navbar[data-scrolled="true"] .nav-link {
        color: #FFF5D6;
    }
    #kingdom-navbar .nav-link.active-link {
        color: #C9A84C;
    }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: rgba(255, 245, 214, 0.82);
            padding: 0.4rem 0;
            position: relative;
            transition: 160ms ease;
            font-family: 'DM Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 500;
        }
        .nav-link:hover { color: #ffffff; }
        .active-link { color: #E8CA72; }
        .active-link::after {
            content: "";
            position: absolute;
            left: 0; bottom: -6px;
            width: 100%; height: 2px;
            background: var(--gold);
            border-radius: 1px;
        }

        /* ── Cards ── */
        .ui-card {
            background: var(--surface-card);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: var(--shadow-card);
        }
        .ui-card-soft {
            background: var(--surface-soft);
            border: 1px solid var(--gold-border);
            border-radius: 10px;
            box-shadow: var(--shadow-card);
        }

        /* ── Divider ── */
        .ui-divider {
            border: 0;
            height: 1px;
            background: linear-gradient(90deg,
                transparent,
                var(--border-strong) 20%,
                var(--gold-border) 50%,
                var(--border-strong) 80%,
                transparent);
        }

        /* ── Buttons ── */
        .ui-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border-radius: 6px;
            padding: 0.5rem 0.9rem;
            font-size: 13.5px;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            transition: all 150ms ease;
            border: 1px solid transparent;
            cursor: pointer;
            white-space: nowrap;
        }
        .ui-btn:hover { transform: translateY(-1px); }

        .ui-btn-primary {
            background: var(--kingdom-gold-gradient);
            color: var(--ink);
            border-color: rgba(107,15,26,0.14);
        }
        .ui-btn-primary:hover { filter: brightness(0.96); }

        .ui-btn-maroon {
            background: var(--kingdom-maroon-gradient);
            color: #FFF5D6;
        }
        .ui-btn-maroon:hover { filter: brightness(1.06); }

        .ui-btn-ghost {
            background: var(--surface-card);
            border-color: var(--border-strong);
            color: var(--maroon);
        }
        .ui-btn-ghost:hover { background: var(--gold-muted); }

        .ui-btn-edit {
            background: #EFF6FF;
            border-color: #BFDBFE;
            color: #1D4ED8;
        }
        .ui-btn-edit:hover { background: #DBEAFE; }

        .ui-btn-delete {
            background: #FEF2F2;
            border-color: #FECACA;
            color: #DC2626;
        }
        .ui-btn-delete:hover { background: #FEE2E2; }

        /* Alpine cloak */
        [x-cloak] { display: none !important; }

        /* ── Inputs ── */
        .ui-input {
            width: 100%;
            border: 1px solid var(--border-strong);
            border-radius: 6px;
            padding: 0.52rem 0.7rem;
            background: #FDFCF8;
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
            font-size: 13.5px;
            outline: none;
            transition: border-color 140ms, box-shadow 140ms;
        }
        .ui-input:focus {
            border-color: var(--gold);
            box-shadow: var(--shadow-focus);
        }

        /* ── Stat values ── */
        .stat-value {
            font-family: 'Oswald', sans-serif;
            font-size: 26px;
            font-weight: 600;
            color: var(--ink);
            line-height: 1;
        }
        .stat-label {
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            color: var(--ink-muted);
            margin-bottom: 4px;
        }

        /* ── Form labels ── */
        .form-label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--ink-muted);
            letter-spacing: 0.04em;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        /* ── Badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.03em;
        }
        .badge-present { background: #F0FDF4; color: #15803D; border: 1px solid #86EFAC; }
        .badge-absent  { background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1; }
        .badge-gold    { background: var(--gold-muted); color: #7A5F12; border: 1px solid var(--gold-border); }

        /* ── Toast feedback ── */
        .toast-success { background: #F0FDF4; border: 1px solid #86EFAC; color: #166534; border-radius: 6px; padding: 10px 14px; font-size: 13px; }
        .toast-error   { background: #FEF2F2; border: 1px solid #FCA5A5; color: #991B1B; border-radius: 6px; padding: 10px 14px; font-size: 13px; }
        .toast-info    { background: #EFF6FF; border: 1px solid #93C5FD; color: #1E3A8A; border-radius: 6px; padding: 10px 14px; font-size: 13px; }

        /* ── Check-in row ── */
        .checkin-row {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: background 120ms;
        }
        .checkin-row:hover { background: var(--gold-muted); }
        .checkin-row.is-checked {
            background: rgba(201,168,76,0.12);
            border-left: 3px solid var(--gold);
        }
        .checkin-row:not(.is-checked) { border-left: 3px solid transparent; }

        /* ── Page eyebrow ── */


        /* ── Table ── */
        .kl-table-wrap {
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow-card);
        }
        .kl-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .kl-table thead th {
            background: var(--surface-soft);
            padding: 9px 14px;
            text-align: left;
            font-size: 10.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--ink-muted);
            border-bottom: 1px solid var(--border);
        }
        .kl-table tbody tr { border-bottom: 1px solid var(--border); }
        .kl-table tbody tr:last-child { border-bottom: none; }
        .kl-table tbody tr:hover { background: var(--gold-muted); }
        .kl-table td { padding: 10px 14px; color: var(--ink); vertical-align: middle; }
    </style>
</head>

<body class="min-h-screen">


    <div class="min-h-screen">
        <div id="sidebar-overlay" class="fixed inset-0 bg-green-900/50 backdrop-blur-sm z-30 hidden lg:hidden"></div>

        <aside id="app-sidebar"
                class="fixed inset-y-0 left-0 z-40 w-72
                    bg-white shadow-2xl border-r border-slate-200
                    transform -translate-x-full lg:translate-x-0
                    transition-transform duration-300 ease-out
                    h-full overflow-y-auto no-scrollbar">
            <div class="sticky top-0 z-20 px-6 py-6 border-b border-slate-200 bg-white text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] font-semibold text-amber-400">TRGC</p>
                        <h1 class="brand-title text-2xl mt-1 text-green-700">Attendance</h1>
                    </div>
                    <button id="sidebar-close" class="lg:hidden text-white/80 hover:text-white transition">
                        <i class="bx bx-x text-2xl"></i>
                    </button>
                </div>
            </div>

            <div class="pl-5 py-6 space-y-6">

                <nav class="space-y-2 text-sm">
                        <a href="{{ route('attendance.index') }}"
                            class="nav-link {{ request()->routeIs('attendance.index') ? 'active-link' : '' }}">
                            <i class='bx bx-check-shield text-base'></i>
                            Attendance
                        </a>

                        <a href="{{ route('attendance.records') }}"
                            class="nav-link {{ request()->routeIs('attendance.records') ? 'active-link' : '' }}">
                            <i class='bx bx-folder-open text-base'></i>
                            Records
                        </a>

                        <a href="{{ route('people.index') }}"
                            class="nav-link {{ request()->routeIs('people.index') ? 'active-link' : '' }}">
                            <i class='bx bx-group text-base'></i>
                            People
                        </a>

                        <a href="{{ route('families.index') }}"
                            class="nav-link {{ request()->routeIs('families.index') ? 'active-link' : '' }}">
                            <i class='bx bx-home-heart text-base'></i>
                            Families
                        </a>
                </nav>


            </div>
        </aside>

        <div class="flex flex-col min-h-screen lg:ml-72">
            <header class="sticky top-0 z-20 bg-red-900 backdrop-blur border-b border-slate-200">
                <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button id="sidebar-open" class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-100 transition">
                            <i class="bx bx-menu text-2xl"></i>
                        </button>
                        <div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] font-semibold text-amber-400">Management System</p>
                                <h1 class="brand-title text-2xl mt-1 text-white">Workspace</h1>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <main class="flex-1 mx-auto w-full px-4 py-4 overflow-y-auto">
                <div class="bg-white shadow-lg rounded-xl border border-slate-100 p-6 sm:p-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
    <script>
        const sidebar = document.getElementById('app-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const openBtn = document.getElementById('sidebar-open');
        const closeBtn = document.getElementById('sidebar-close');

        const openSidebar = () => {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        };

        const closeSidebar = () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        };

        if (openBtn) {
            openBtn.addEventListener('click', openSidebar);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeSidebar);
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                overlay.classList.add('hidden');
                sidebar.classList.remove('-translate-x-full');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });

    </script>
</body>

</html>
