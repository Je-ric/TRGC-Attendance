@props([
    'href'       => null,
    'type'       => 'button',
    'variant'    => 'primary',
    'loading'    => null,
    'wireTarget' => null,
])

@php
    $iconFix = '[&_i]:leading-none [&_i]:text-[1.1em] [&_i]:translate-y-px ';

    $tableBtn = 'inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-lg
                 transition-colors duration-150
                 disabled:opacity-50 disabled:pointer-events-none ' . $iconFix;

    $formBtn  = 'inline-flex items-center gap-2 px-4 py-2.5 text-[13px] font-semibold rounded-lg
                 transition-all duration-150 active:scale-95
                 focus:outline-none
                 disabled:opacity-50 disabled:pointer-events-none ' . $iconFix;

    $smBtn    = 'inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg
                 transition-colors duration-150
                 disabled:opacity-50 disabled:pointer-events-none ' . $iconFix;

    $styles = [
        // ── Table buttons ──────────────────────────────────────────────────────
        'table-edit'    => $tableBtn . 'text-white bg-[#3b5bdb] hover:bg-[#2f4ac0]',
        'table-view'    => $tableBtn . 'text-white bg-[#0891b2] hover:bg-[#0e7490]',
        'table-danger'  => $tableBtn . 'text-white bg-[#e11d48] hover:bg-[#be123c]',
        'table-restore' => $tableBtn . 'text-white bg-[#6d28d9] hover:bg-[#5b21b6]',
        'table-cancel'  => $tableBtn . 'bg-white text-[#475569] border border-[#e4e0e2] hover:bg-[#f5f4f6]',

        // ── Form / CRUD buttons ────────────────────────────────────────────────
        'primary'  => $formBtn . '
            bg-[linear-gradient(135deg,#93291e_0%,#ed213a_100%)]
            text-white hover:brightness-110
            focus:ring-2 focus:ring-[#ed213a]/30',

        'save'     => $formBtn . '
            bg-[linear-gradient(135deg,#ed213a_0%,#c41a2e_100%)]
            text-white hover:brightness-110
            focus:ring-2 focus:ring-[#ed213a]/30',

        'secondary' => $formBtn . '
            bg-[#93291e] text-white
            hover:bg-[#7a1f15]
            focus:ring-2 focus:ring-[#93291e]/30',

        'cancel'   => $formBtn . '
            bg-white text-[#475569]
            border border-[#e4e0e2]
            hover:bg-[#f5f4f6] hover:border-[#c9c4c6]
            focus:ring-2 focus:ring-[#a09aa4]/20',

        'back'     => $formBtn . '
            bg-white text-[#475569]
            border border-[#e4e0e2]
            hover:bg-[#f5f4f6] hover:border-[#c9c4c6]
            focus:ring-2 focus:ring-[#a09aa4]/20',

        'danger'   => $formBtn . '
            bg-[#e11d48] text-white
            hover:bg-[#be123c]
            focus:ring-2 focus:ring-[#e11d48]/30',

        'ghost'    => $formBtn . '
            bg-white text-[#6b6570]
            border border-[#e4e0e2]
            hover:bg-[#f5f4f6] hover:text-[#1c1c1e]
            focus:ring-2 focus:ring-[#a09aa4]/20',

        // ── Small buttons ──────────────────────────────────────────────────────
        'sm-primary' => $smBtn . '
            bg-[linear-gradient(135deg,#93291e_0%,#ed213a_100%)]
            text-white hover:brightness-110',

        'sm-cancel'  => $smBtn . '
            bg-white text-[#475569]
            border border-[#e4e0e2]
            hover:bg-[#f5f4f6]',

        'sm-danger'  => $smBtn . 'bg-[#e11d48] text-white hover:bg-[#be123c]',

        'sm-ghost'   => $smBtn . '
            bg-white text-[#6b6570]
            border border-[#e4e0e2]
            hover:bg-[#f5f4f6]',
    ];

    $class = $styles[strval($variant)] ?? $styles['primary'];

    $attributeKeys = array_keys($attributes->getAttributes());
    $wireClickKey = null;
    foreach ($attributeKeys as $key) {
        if (str_starts_with($key, 'wire:click')) { $wireClickKey = $key; break; }
    }
    $wireClickValue = $wireClickKey ? $attributes->get($wireClickKey) : null;
    $existingTargetAttr = $attributes->get('wire:target');
    $parsedTarget = null;
    if (is_string($wireClickValue) && preg_match('/^\s*([A-Za-z_][A-Za-z0-9_]*)\s*(?:\(|$)/', $wireClickValue, $m)) {
        $parsedTarget = $m[1];
    }
    $autoTarget = $wireTarget ?: $parsedTarget;
    $spinnerTarget = $autoTarget ?: $existingTargetAttr;
    $shouldHandleLoading = filled($loading) || filled($spinnerTarget);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}"
        {{ $attributes->merge(['class' => $class]) }}
        @if ($shouldHandleLoading && !$attributes->has('wire:loading.attr')) wire:loading.attr="disabled" @endif
        @if ($shouldHandleLoading && $autoTarget && !$attributes->has('wire:target')) wire:target="{{ $autoTarget }}" @endif>

        @if ($shouldHandleLoading)
            @if (filled($loading))
                <span wire:loading.remove @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                      class="inline-flex items-center gap-1.5 leading-none">{{ $slot }}</span>
                <span wire:loading @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                      class="inline-flex items-center gap-1.5 leading-none">
                    <svg class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span class="leading-none">{{ $loading }}</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 leading-none">
                    <span class="inline-flex items-center gap-1.5 leading-none">{{ $slot }}</span>
                    <svg wire:loading @if($spinnerTarget) wire:target="{{ $spinnerTarget }}" @endif
                         class="animate-spin h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
            @endif
        @else
            <span class="inline-flex items-center gap-1.5 leading-none">{{ $slot }}</span>
        @endif
    </button>
@endif

{{--
VARIANTS
────────────────────────────────────────────────────────────────────
Table (compact):
  table-edit     → blue      — Edit actions
  table-view     → cyan      — View / Preview
  table-danger   → rose/red  — Delete, Reject
  table-restore  → violet    — Restore, Undo
  table-cancel   → white     — Cancel inline

Form (larger, scales on click):
  primary    → red gradient  — main submit / CTA
  save       → red gradient  — save / update
  secondary  → dark maroon   — secondary action
  cancel     → white/border  — cancel / dismiss
  back       → white/border  — back navigation (use bx-arrow-left icon)
  danger     → solid red     — destructive action
  ghost      → white/border  — subtle action

Small:
  sm-primary  sm-cancel  sm-danger  sm-ghost
--}}
