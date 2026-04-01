@props([
    'type'    => 'info',
    'message' => null,
])

@php
    $toastStyles = [
        'success' => ['container' => 'bg-[#f0fdf4] text-[#166534] border-[#86efac]', 'iconBg' => 'bg-[#16a34a]', 'icon' => 'bx-check',       'title' => 'Success'],
        'error'   => ['container' => 'bg-[#fff0f0] text-[#93291e] border-[#ffd0d0]', 'iconBg' => 'bg-[#ed213a]', 'icon' => 'bx-x',           'title' => 'Error'],
        'warning' => ['container' => 'bg-[#fffbeb] text-[#92400e] border-[#fcd34d]', 'iconBg' => 'bg-[#f59e0b]', 'icon' => 'bx-error',       'title' => 'Attention'],
        'info'    => ['container' => 'bg-[#f5f4f6] text-[#1c1c1e] border-[#e4e0e2]', 'iconBg' => 'bg-[#6b6570]', 'icon' => 'bx-info-circle', 'title' => 'Information'],
    ];
    $toast = $toastStyles[$type] ?? $toastStyles['info'];
@endphp

{{-- ── 1. Session-flash toast ─────────────────────────────────────────────── --}}
@if ($message)
    <div
        x-cloak
        x-data="{ show: false }"
        x-init="$nextTick(() => { show = true; setTimeout(() => show = false, 5200); })"
        x-show="show"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-3"
        x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 translate-x-3"
        class="fixed top-5 right-3 sm:right-5 z-[9999]
               w-[calc(100vw-1.5rem)] sm:w-96 max-w-sm
               rounded-xl border p-3 shadow-xl backdrop-blur-sm {{ $toast['container'] }}"
        role="status"
        aria-live="polite">

        <div class="flex items-start gap-3">
            <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white {{ $toast['iconBg'] }}">
                <i class="bx {{ $toast['icon'] }} text-lg leading-none"></i>
            </span>
            <div class="min-w-0 flex-1">
                <p class="font-semibold leading-5 text-[13px]">{{ $toast['title'] }}</p>
                <p class="mt-1 text-[13px] leading-relaxed">{{ $message }}</p>
            </div>
            <button @click="show = false"
                class="ml-1 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg opacity-60 hover:opacity-100 transition"
                aria-label="Close notification">
                <i class="bx bx-x text-xl leading-none"></i>
            </button>
        </div>
    </div>
@endif

{{-- ── 2. Livewire / JS event-driven toast (place once in layout) ─────────── --}}
<div
    x-cloak
    x-data="{
        show:    false,
        type:    'info',
        message: '',
        _timer:  null,

        styles: {
            success: { container: 'bg-[#f0fdf4] text-[#166534] border-[#86efac]', iconBg: 'bg-[#16a34a]', icon: 'bx-check',       title: 'Success'     },
            error:   { container: 'bg-[#fff0f0] text-[#93291e] border-[#ffd0d0]', iconBg: 'bg-[#ed213a]', icon: 'bx-x',           title: 'Error'       },
            warning: { container: 'bg-[#fffbeb] text-[#92400e] border-[#fcd34d]', iconBg: 'bg-[#f59e0b]', icon: 'bx-error',       title: 'Attention'   },
            info:    { container: 'bg-[#f5f4f6] text-[#1c1c1e] border-[#e4e0e2]', iconBg: 'bg-[#6b6570]', icon: 'bx-info-circle', title: 'Information' },
        },

        get style() { return this.styles[this.type] ?? this.styles.info; },

        open(t, msg) {
            this.type    = t ?? 'info';
            this.message = msg ?? '';
            this.show    = true;
            clearTimeout(this._timer);
            this._timer  = setTimeout(() => this.show = false, 5200);
        }
    }"
    @lw-toast.window="open($event.detail.type, $event.detail.message)"
    x-show="show"
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-3"
    x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0 translate-x-3"
    :class="style.container"
    class="fixed top-5 right-3 sm:right-5 z-[9999]
           w-[calc(100vw-1.5rem)] sm:w-96 max-w-sm
           rounded-xl border p-3 shadow-xl backdrop-blur-sm"
    role="status"
    aria-live="polite">

    <div class="flex items-start gap-3">
        <span :class="style.iconBg"
            class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white">
            <i :class="'bx ' + style.icon" class="text-lg leading-none"></i>
        </span>
        <div class="min-w-0 flex-1">
            <p class="font-semibold leading-5 text-[13px]" x-text="style.title"></p>
            <p class="mt-1 text-[13px] leading-relaxed" x-text="message"></p>
        </div>
        <button @click="show = false"
            class="ml-1 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg opacity-60 hover:opacity-100 transition"
            aria-label="Close notification">
            <i class="bx bx-x text-xl leading-none"></i>
        </button>
    </div>
</div>
