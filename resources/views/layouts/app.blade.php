<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kingdom Ledger' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --kingdom-maroon: #6B0F1A;
            --kingdom-gold: #D4AF37;
            --kingdom-black: #111111;
            --kingdom-white: #FFFFFF;
            --kingdom-gold-gradient: linear-gradient(135deg, #C5A028, #F5D76E);
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--kingdom-black);
            background:
                radial-gradient(circle at 10% 10%, rgba(212, 175, 55, 0.12), transparent 40%),
                radial-gradient(circle at 90% 20%, rgba(107, 15, 26, 0.15), transparent 45%),
                #f8f5ef;
        }

        .brand-font {
            font-family: 'Cinzel', serif;
        }

        .gold-gradient {
            background: var(--kingdom-gold-gradient);
        }

        .kingdom-shell {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(212, 175, 55, 0.22);
            box-shadow: 0 10px 35px rgba(17, 17, 17, 0.08);
            backdrop-filter: blur(8px);
        }

        #kingdom-navbar[data-scrolled="false"] {
            background: transparent;
            border-color: transparent;
        }

        #kingdom-navbar[data-scrolled="true"] {
            background: rgba(107, 15, 26, 0.95);
            border-color: rgba(212, 175, 55, 0.28);
        }
    </style>
</head>
<body class="min-h-screen">
    <header id="kingdom-navbar" data-scrolled="false" class="fixed top-0 inset-x-0 z-50 border-b transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between rounded-2xl px-4 py-3 border border-white/15 bg-black/20 backdrop-blur-md">
                <a href="{{ route('attendance.index') }}" class="brand-font text-xl sm:text-2xl text-white tracking-wide">
                    Kingdom Ledger
                </a>

                <nav class="flex items-center gap-1 sm:gap-2 text-sm">
                    <a href="{{ route('attendance.index') }}"
                       class="px-3 py-2 rounded-lg transition {{ request()->routeIs('attendance.index') ? 'text-[#111111] gold-gradient font-semibold' : 'text-white hover:bg-white/10' }}">
                        Attendance
                    </a>
                    <a href="{{ route('attendance.records') }}"
                       class="px-3 py-2 rounded-lg transition {{ request()->routeIs('attendance.records') ? 'text-[#111111] gold-gradient font-semibold' : 'text-white hover:bg-white/10' }}">
                        Records
                    </a>
                    <a href="{{ route('people.index') }}"
                       class="px-3 py-2 rounded-lg transition {{ request()->routeIs('people.index') ? 'text-[#111111] gold-gradient font-semibold' : 'text-white hover:bg-white/10' }}">
                        People
                    </a>
                    <a href="{{ route('families.index') }}"
                       class="px-3 py-2 rounded-lg transition {{ request()->routeIs('families.index') ? 'text-[#111111] gold-gradient font-semibold' : 'text-white hover:bg-white/10' }}">
                        Families
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-8">
        <div class="kingdom-shell rounded-2xl p-4 sm:p-6 lg:p-8">
            @yield('content')
        </div>
    </main>

    <script>
        (function () {
            const navbar = document.getElementById('kingdom-navbar');
            if (!navbar) return;

            const updateNavbar = () => {
                navbar.setAttribute('data-scrolled', window.scrollY > 12 ? 'true' : 'false');
            };

            updateNavbar();
            window.addEventListener('scroll', updateNavbar, { passive: true });
        })();
    </script>

    @livewireScripts
</body>
</html>
