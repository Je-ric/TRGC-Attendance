@props(['icon', 'title', 'value', 'href' => '#', 'gradientVariant' => 'sunset'])

@php
    $gradientVariants = [
        'brand' => 'bg-gradient-to-br from-[#1a2235] to-[#ffb51b]',
        'slate' => 'bg-gradient-to-br from-slate-700 to-slate-500',
        'deep-rose' => 'bg-gradient-to-br from-red-800 to-rose-600',
        'sunset' => 'bg-gradient-to-br from-orange-600 to-amber-400',

        // New gradients inspired by uigradients.com
        'aqua' => 'bg-gradient-to-br from-cyan-400 via-blue-200 to-emerald-100',
        'peach' => 'bg-gradient-to-br from-amber-200 via-orange-100 to-pink-100',
        'midnight' => 'bg-gradient-to-br from-gray-900 via-slate-800 to-indigo-900',
        'sunrise' => 'bg-gradient-to-br from-pink-200 via-yellow-100 to-amber-200',
        'fire' => 'bg-gradient-to-br from-red-500 via-orange-400 to-yellow-300',
        'cherry' => 'bg-gradient-to-br from-rose-400 via-pink-300 to-red-400',
        'ocean' => 'bg-gradient-to-br from-blue-400 via-cyan-300 to-teal-400',
        'forest' => 'bg-gradient-to-br from-green-400 via-emerald-300 to-teal-400',
        'sunset-orange' => 'bg-gradient-to-br from-orange-400 via-red-300 to-pink-400',
        'lavender-purple' => 'bg-gradient-to-br from-purple-400 via-violet-300 to-indigo-400',
        'mint-green' => 'bg-gradient-to-br from-green-300 via-emerald-200 to-teal-300',
        'rose-pink' => 'bg-gradient-to-br from-pink-400 via-rose-300 to-red-400',
        'blue-sky' => 'bg-gradient-to-br from-sky-400 via-blue-300 to-indigo-400',
        'golden' => 'bg-gradient-to-br from-yellow-400 via-amber-300 to-orange-400',
        'emerald-teal' => 'bg-gradient-to-br from-emerald-400 via-teal-300 to-cyan-400',
        'violet-purple' => 'bg-gradient-to-br from-violet-400 via-purple-300 to-indigo-400',
        'coral' => 'bg-gradient-to-br from-orange-300 via-red-200 to-pink-300',
        'azure' => 'bg-gradient-to-br from-blue-300 via-cyan-200 to-teal-300',
        'lime-green' => 'bg-gradient-to-br from-lime-400 via-green-300 to-emerald-400',
        'fuchsia-pink' => 'bg-gradient-to-br from-fuchsia-400 via-pink-300 to-rose-400',
        'indigo-blue' => 'bg-gradient-to-br from-indigo-400 via-blue-300 to-cyan-400',
        'amber-orange' => 'bg-gradient-to-br from-amber-400 via-orange-300 to-red-400',
        'teal-cyan' => 'bg-gradient-to-br from-teal-400 via-cyan-300 to-blue-400',
        'purple-violet' => 'bg-gradient-to-br from-purple-400 via-violet-300 to-indigo-400',
        'green-emerald' => 'bg-gradient-to-br from-green-400 via-emerald-300 to-teal-400',
    ];

    $selectedGradient = $gradientVariants[$gradientVariant] ?? $gradientVariants['sunset'];
@endphp

<a href="{{ $href }}"
    class="block p-4 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 {{ $selectedGradient }} text-white">

    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-white/80">
                {{ $title }}
            </p>

            <h3 class="text-2xl font-bold">
                {{ $value }}
            </h3>
        </div>

        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-white/20">
            <i class="bx {{ $icon }} text-2xl text-white"></i>
        </div>
    </div>

</a>

{{--
Usage: 
Basic:
<x-overview.stat-card icon="bx-users" title="Total Users" value="1,234" href="/users" />


--}}