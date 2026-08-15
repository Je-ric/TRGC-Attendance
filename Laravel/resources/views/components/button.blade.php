@props([
    'href'       => null,
    'type'       => 'button',
    'variant'    => 'primary',
    'loading'    => null,
    'wireTarget' => null,
])

@php
    $iconFix = '[&_i]:leading-none [&_i]:text-[1.1em] [&_i]:translate-y-px ';

    $tableBtn = 'inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-[6px]
                 transition-colors duration-150
                 disabled:opacity-50 disabled:pointer-events-none ' . $iconFix;

    $formBtn  = 'inline-flex items-center gap-2 px-4 py-2.5 text-[13px] font-semibold rounded-[6px]
                 transition-all duration-150 active:scale-95
                 focus:outline-none
                 disabled:opacity-50 disabled:pointer-events-none ' . $iconFix;

    $smBtn    = 'inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-[6px]
                 transition-colors duration-150
                 disabled:opacity-50 disabled:pointer-events-none ' . $iconFix;

    $styles = [
        // ── Table buttons ──────────────────────────────────────────────────────
        'table-edit'    => $tableBtn . 'text-white bg-[#635BFF] hover:bg-[#4f48d4]',
        'table-view'    => $tableBtn . 'text-white bg-[#0891b2] hover:bg-[#0e7490]',
        'table-danger'  => $tableBtn . 'text-white bg-[#FF4B4B] hover:bg-[#e03e3e]',
        'table-restore' => $tableBtn . 'text-white bg-[#6d28d9] hover:bg-[#5b21b6]',
        'table-cancel'  => $tableBtn . 'bg-white text-[#6B6B6B] border border-[#E6E6E6] hover:bg-[#F9F9F9]',

        // ── Form / CRUD buttons ────────────────────────────────────────────────
        'primary'  => $formBtn . '
            bg-[#635BFF] text-white
            hover:bg-[#4f48d4]
            focus:ring-2 focus:ring-[#635BFF]/30',

        'save'     => $formBtn . '
            bg-[#635BFF] text-white
            hover:bg-[#4f48d4]
            focus:ring-2 focus:ring-[#635BFF]/30',

        'secondary' => $formBtn . '
            bg-[#242424] text-white
            hover:bg-[#333333]
            focus:ring-2 focus:ring-[#242424]/30',

        'cancel'   => $formBtn . '
            bg-white text-[#6B6B6B]
            border border-[#E6E6E6]
            hover:bg-[#F9F9F9] hover:border-[#c9c4c6]
            focus:ring-2 focus:ring-[#635BFF]/20',

        'back'     => $formBtn . '
            bg-white text-[#6B6B6B]
            border border-[#E6E6E6]
            hover:bg-[#F9F9F9] hover:border-[#c9c4c6]
            focus:ring-2 focus:ring-[#635BFF]/20',

        'danger'   => $formBtn . '
            bg-[#FF4B4B] text-white
            hover:bg-[#e03e3e]
            focus:ring-2 focus:ring-[#FF4B4B]/30',

        'ghost'    => $formBtn . '
            bg-white text-[#6B6B6B]
            border border-[#E6E6E6]
            hover:bg-[#F9F9F9] hover:text-[#242424]
            focus:ring-2 focus:ring-[#635BFF]/20',

        // ── Small buttons ──────────────────────────────────────────────────────
        'sm-primary' => $smBtn . '
            bg-[#635BFF] text-white hover:bg-[#4f48d4]',

        'sm-cancel'  => $smBtn . '
            bg-white text-[#6B6B6B]
            border border-[#E6E6E6]
            hover:bg-[#F9F9F9]',

        'sm-danger'  => $smBtn . 'bg-[#FF4B4B] text-white hover:bg-[#e03e3e]',

        'sm-ghost'   => $smBtn . '
            bg-white text-[#6B6B6B]
            border border-[#E6E6E6]
            hover:bg-[#F9F9F9]',
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
  table-edit     → blue (#635BFF) — Edit actions
  table-view     → cyan           — View / Preview
  table-danger   → red (#FF4B4B)  — Delete, Reject
  table-restore  → violet         — Restore, Undo
  table-cancel   → white/border   — Cancel inline

Form (larger, scales on click):
  primary    → blue (#635BFF)    — main submit / CTA
  save       → blue (#635BFF)    — save / update
  secondary  → dark (#242424)    — secondary action
  cancel     → white/border      — cancel / dismiss
  back       → white/border      — back navigation (use bx-arrow-left icon)
  danger     → red (#FF4B4B)     — destructive action
  ghost      → white/border      — subtle action

Small:
  sm-primary  sm-cancel  sm-danger  sm-ghost
--}}
