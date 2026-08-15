<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'TRGC Attendance' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-[#F9F9F9] text-[#242424] font-['Inter'] antialiased">

    {{-- Mobile sidebar overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>

    {{-- ── Sidebar ──────────────────────────────────────────────────── --}}
    <aside id="app-sidebar"
           class="fixed inset-y-0 left-0 w-60 flex flex-col z-40 overflow-y-auto bg-[#242424]">

        {{-- Brand --}}
        <div class="flex items-center justify-between px-4 py-5 border-b border-white/[0.08]">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-[6px] bg-[#635BFF] flex items-center justify-center shrink-0">
                    <i class='bx bx-cross text-white text-lg'></i>
                </div>
                <div>
                    <div class="text-[9px] font-bold uppercase tracking-[0.16em] text-white/40">TRGC</div>
                    <div class="text-[15px] font-bold text-white leading-tight">Attendance</div>
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
                   class="flex items-center gap-2.5 px-2.5 py-2 rounded-[6px] text-[13.5px] font-medium no-underline transition-all duration-150
                          border-l-[3px]
                          {{ $active
                              ? 'bg-[#635BFF]/20 text-white font-semibold border-[#635BFF]'
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
        <header class="sticky top-0 z-20 bg-white border-b border-[#E6E6E6] h-14 flex items-center justify-between px-6"
                style="box-shadow: 0 2px 8px rgba(0,0,0,0.08)">

            <div class="flex items-center gap-3">
                <button id="sidebar-open"
                        class="lg:hidden text-[#6B6B6B] hover:text-[#242424] transition-colors p-1 flex items-center">
                    <i class="bx bx-menu text-xl"></i>
                </button>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#635BFF]">Management System</p>
                    <p class="text-[15px] font-bold text-[#242424] leading-tight">Workspace</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- User info --}}
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-[13px] font-semibold text-[#242424] leading-tight">{{ Auth::user()->name }}</span>
                    <span class="text-[11px] text-[#A0A0A0] leading-tight">{{ Auth::user()->email }}</span>
                </div>

                {{-- Avatar --}}
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-[13px] font-bold shrink-0 bg-[#635BFF]">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            title="Sign out"
                            class="flex items-center gap-1 px-2.5 py-1.5 rounded-[6px] border border-[#E6E6E6] text-[#6B6B6B] text-[12px] font-medium
                                   hover:bg-[#F9F9F9] hover:text-[#242424] hover:border-[#c9c4c6] transition-colors">
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
