<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'TRGC Attendance' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* Only things Tailwind cannot express */

        /* Sidebar width token used by both sidebar and content offset */
        :root { --sidebar-w: 240px; }

        /* Sidebar off-canvas toggle (driven by JS) */
        #app-sidebar { transform: translateX(-100%); transition: transform 0.25s cubic-bezier(.4,0,.2,1); }
        @media (min-width: 1024px) { #app-sidebar { transform: translateX(0); } }
        #app-content { transition: margin-left 0.25s cubic-bezier(.4,0,.2,1); }
        @media (min-width: 1024px) { #app-content { margin-left: var(--sidebar-w); } }

        /* Check-in row — stateful, applied via PHP @class */
        .checkin-row {
            display: flex; align-items: center;
            padding: 10px 14px;
            border-bottom: 1px solid #ede9eb;
            cursor: pointer;
            transition: background 0.1s;
            border-left: 3px solid transparent;
        }
        .checkin-row:hover { background: #f5f4f6; }
        .checkin-row.is-checked { background: rgba(237,33,58,0.05); border-left-color: #ed213a; }

        /* Native dialog backdrop — no Tailwind equivalent */
        dialog::backdrop { background: rgba(28,28,30,0.5); backdrop-filter: blur(3px); }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="min-h-screen bg-[#f5f4f6] text-[#1c1c1e] font-['Inter'] antialiased">

    {{-- Mobile sidebar overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>

    {{-- ── Sidebar ──────────────────────────────────────────────────── --}}
    <aside id="app-sidebar"
           class="fixed inset-y-0 left-0 w-60 flex flex-col z-40 overflow-y-auto"
           style="background: linear-gradient(180deg, #1a0a08 0%, #93291e 100%)">

        {{-- Brand --}}
        <div class="flex items-center justify-between px-4 py-5 border-b border-white/[0.08]">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/[0.12] flex items-center justify-center shrink-0">
                    <i class='bx bx-cross text-white text-lg'></i>
                </div>
                <div>
                    <div class="text-[9px] font-bold uppercase tracking-[0.16em] text-white/40">TRGC</div>
                    <div class="text-[15px] font-bold text-white leading-tight font-['Oswald']">Attendance</div>
                </div>
            </div>
            <button id="sidebar-close"
                    class="lg:hidden text-white/50 hover:text-white/80 transition-colors p-1">
                <i class="bx bx-x text-xl"></i>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-2.5 py-3 flex flex-col gap-0.5">
            <p class="text-[9.5px] font-bold uppercase tracking-[0.14em] text-white/25 px-2 mb-1 mt-2">Menu</p>

            @php
                $navItems = [
                    ['route' => 'dashboard',          'icon' => 'bx-home-alt-2',   'label' => 'Dashboard'],
                    ['route' => 'attendance.index',   'icon' => 'bx-check-shield', 'label' => 'Attendance'],
                    ['route' => 'attendance.records', 'icon' => 'bx-folder-open',  'label' => 'Records'],
                    ['route' => 'people.index',       'icon' => 'bx-group',        'label' => 'People'],
                    ['route' => 'families.index',     'icon' => 'bx-home-heart',   'label' => 'Families'],
                ];
            @endphp

            @foreach($navItems as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150
                          border-l-[3px]
                          {{ $active
                              ? 'bg-[#ed213a]/25 text-white font-semibold border-[#ed213a]'
                              : 'text-white/55 border-transparent hover:bg-white/[0.08] hover:text-white/90' }}">
                    <i class='bx {{ $item["icon"] }} text-base shrink-0'></i>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Date footer --}}
        <div class="px-4 py-3.5 border-t border-white/[0.08]">
            <p class="text-[10px] font-medium text-white/25">{{ now()->format('l, F j') }}</p>
        </div>
    </aside>

    {{-- ── Main content ─────────────────────────────────────────────── --}}
    <div id="app-content" class="min-h-screen flex flex-col">

        {{-- Topbar --}}
        <header class="sticky top-0 z-20 bg-white border-b border-[#e4e0e2] h-14 flex items-center justify-between px-6"
                style="box-shadow: 0 2px 16px rgba(0,0,0,.07)">

            <div class="flex items-center gap-3">
                <button id="sidebar-open"
                        class="lg:hidden text-[#6b6570] hover:text-[#1c1c1e] transition-colors p-1 flex items-center">
                    <i class="bx bx-menu text-xl"></i>
                </button>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#ed213a]">Management System</p>
                    <p class="text-[15px] font-bold text-[#1c1c1e] leading-tight font-['Oswald']">Workspace</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- User info --}}
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-[13px] font-semibold text-[#1c1c1e] leading-tight">{{ Auth::user()->name }}</span>
                    <span class="text-[11px] text-[#a09aa4] leading-tight">{{ Auth::user()->email }}</span>
                </div>

                {{-- Avatar --}}
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-[13px] font-bold shrink-0"
                     style="background: linear-gradient(135deg, #93291e, #ed213a)">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            title="Sign out"
                            class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-[#e4e0e2] text-[#6b6570] text-[12px] font-medium
                                   hover:bg-[#f5f4f6] hover:text-[#1c1c1e] hover:border-[#c9c4c6] transition-colors">
                        <i class="bx bx-log-out text-base"></i>
                        <span class="hidden sm:inline">Sign out</span>
                    </button>
                </form>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 p-6 w-full max-w-[1280px] mx-auto">
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
        const sidebar   = document.getElementById('app-sidebar');
        const overlay   = document.getElementById('sidebar-overlay');
        const openBtn   = document.getElementById('sidebar-open');
        const closeBtn  = document.getElementById('sidebar-close');

        const openSidebar  = () => { sidebar.style.transform = 'translateX(0)'; overlay.classList.remove('hidden'); };
        const closeSidebar = () => { sidebar.style.transform = '';               overlay.classList.add('hidden'); };

        openBtn?.addEventListener('click', openSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);
        window.addEventListener('resize', () => { if (window.innerWidth >= 1024) overlay.classList.add('hidden'); });

        // Livewire modal bridge
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-modal',  ({ id }) => document.getElementById(id)?.showModal());
            Livewire.on('close-modal', ({ id }) => document.getElementById(id)?.close());
        });
    </script>
</body>
</html>
