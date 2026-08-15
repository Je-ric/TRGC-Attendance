<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — TRGC Attendance</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-[#f5f4f6]">

    <div class="w-full max-w-sm mx-4">

        {{-- Logo / Brand --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4"
                 style="background:linear-gradient(135deg,#93291e,#ed213a);box-shadow:0 4px 20px rgba(237,33,58,0.35)">
                <i class='bx bx-cross text-white text-3xl'></i>
            </div>
            <h1 class="text-[28px] font-bold text-[#1c1c1e]" style="font-family:'Oswald',sans-serif">TRGC Attendance</h1>
            <p class="text-[13px] text-[#a09aa4] mt-1">The Risen Generation Church</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl border border-[#e4e0e2] p-8"
             style="box-shadow:0 4px 24px rgba(0,0,0,0.08)">

            <h2 class="text-[18px] font-bold text-[#1c1c1e] mb-1" style="font-family:'Oswald',sans-serif">Sign In</h2>
            <p class="text-[13px] text-[#a09aa4] mb-6">Admin access only.</p>

            @if($errors->any())
                <div class="rounded-xl border border-[#ffd0d0] bg-[#fff0f0] p-3 mb-5 flex items-center gap-2">
                    <i class='bx bx-error-circle text-[#ed213a] text-base shrink-0'></i>
                    <span class="text-[13px] text-[#93291e]">{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                @csrf

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#6b6570] mb-1.5">
                        Email Address
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="admin@trgc.org"
                           class="w-full rounded-lg border border-[#e4e0e2] bg-white px-3 py-2.5 text-[13px] text-[#1c1c1e]
                                  placeholder:text-[#a09aa4] outline-none transition-all
                                  focus:border-[#ed213a]"
                           style="box-shadow:none"
                           onfocus="this.style.boxShadow='0 0 0 3px rgba(237,33,58,0.2)'"
                           onblur="this.style.boxShadow='none'">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-[0.12em] text-[#6b6570] mb-1.5">
                        Password
                    </label>
                    <input type="password" name="password" required
                           placeholder="••••••••"
                           class="w-full rounded-lg border border-[#e4e0e2] bg-white px-3 py-2.5 text-[13px] text-[#1c1c1e]
                                  placeholder:text-[#a09aa4] outline-none transition-all
                                  focus:border-[#ed213a]"
                           style="box-shadow:none"
                           onfocus="this.style.boxShadow='0 0 0 3px rgba(237,33,58,0.2)'"
                           onblur="this.style.boxShadow='none'">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                           class="w-4 h-4 rounded accent-[#ed213a]">
                    <label for="remember" class="text-[13px] text-[#6b6570] cursor-pointer">Remember me</label>
                </div>

                <button type="submit"
                        class="w-full py-2.5 rounded-lg text-[13px] font-semibold text-white transition-all active:scale-[0.98]"
                        style="background:linear-gradient(135deg,#93291e,#ed213a);box-shadow:0 2px 12px rgba(237,33,58,0.3)">
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-[12px] text-[#a09aa4] mt-6">
            © {{ date('Y') }} The Risen Generation Church
        </p>
    </div>

</body>
</html>
