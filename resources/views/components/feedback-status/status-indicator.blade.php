@props([
    'status'  => null,
    'label'   => null,
    'variant' => null,
    'dot'     => false,
    'icon'    => null,
    'size'    => 'md',
])

@php
$statusStyles = [
    'success'  => 'bg-[#f0fdf4] text-[#166534] ring-1 ring-[#86efac]',
    'neutral'  => 'bg-[#f5f4f6] text-[#6b6570] ring-1 ring-[#e4e0e2]',
    'info'     => 'bg-[#eff6ff] text-[#1e40af] ring-1 ring-[#bfdbfe]',
    'warning'  => 'bg-[#fffbeb] text-[#92400e] ring-1 ring-[#fcd34d]',
    'danger'   => 'bg-[#fff0f0] text-[#93291e] ring-1 ring-[#ffd0d0]',
    'present'  => 'bg-[#f0fdf4] text-[#166534] ring-1 ring-[#86efac]',
    'absent'   => 'bg-[#f5f4f6] text-[#6b6570] ring-1 ring-[#e4e0e2]',
    'today'    => 'bg-[#ed213a] text-white ring-1 ring-[#93291e]',
    'tomorrow' => 'bg-[#fff0f0] text-[#93291e] ring-1 ring-[#ffd0d0]',
    'member'   => 'bg-[#fff0f0] text-[#93291e] ring-1 ring-[#ffd0d0]',
    'adult'    => 'bg-[#eff6ff] text-[#1e40af] ring-1 ring-[#bfdbfe]',
    'youth'    => 'bg-[#fffbeb] text-[#92400e] ring-1 ring-[#fcd34d]',
    'child'    => 'bg-[#f0fdf4] text-[#166534] ring-1 ring-[#86efac]',
];

$statusIcons = [
    'success'  => 'bx bx-check-circle',
    'neutral'  => 'bx bx-minus-circle',
    'info'     => 'bx bx-info-circle',
    'warning'  => 'bx bx-error-circle',
    'danger'   => 'bx bx-x-circle',
    'present'  => 'bx bx-check-circle',
    'absent'   => 'bx bx-minus-circle',
    'today'    => 'bx bx-cake',
    'tomorrow' => 'bx bx-time-five',
    'member'   => 'bx bx-user',
];

$variantStyles = [
    'red'     => ['pill' => 'bg-[#fff0f0] text-[#93291e] ring-1 ring-[#ffd0d0]',  'dot' => 'bg-[#ed213a]'],
    'maroon'  => ['pill' => 'bg-[#93291e] text-white ring-1 ring-[#7a1f15]',       'dot' => 'bg-[#93291e]'],
    'green'   => ['pill' => 'bg-[#f0fdf4] text-[#166534] ring-1 ring-[#86efac]',  'dot' => 'bg-[#16a34a]'],
    'amber'   => ['pill' => 'bg-[#fffbeb] text-[#92400e] ring-1 ring-[#fcd34d]',  'dot' => 'bg-[#f59e0b]'],
    'blue'    => ['pill' => 'bg-[#eff6ff] text-[#1e40af] ring-1 ring-[#bfdbfe]',  'dot' => 'bg-[#3b82f6]'],
    'slate'   => ['pill' => 'bg-[#f5f4f6] text-[#6b6570] ring-1 ring-[#e4e0e2]',  'dot' => 'bg-[#a09aa4]'],
];

$isStatusMode = filled($status);

$style = $isStatusMode
    ? ($statusStyles[$status] ?? 'bg-[#f5f4f6] text-[#6b6570] ring-1 ring-[#e4e0e2]')
    : ($variantStyles[$variant]['pill'] ?? $variantStyles['slate']['pill']);

$defaultStatusIcon = $isStatusMode ? ($statusIcons[$status] ?? null) : null;
$iconClass  = $icon ?? $defaultStatusIcon;
$dotClass   = $variantStyles[$variant]['dot'] ?? 'bg-[#a09aa4]';

$resolvedLabel = $label
    ?? ($slot->isNotEmpty() ? null : ($isStatusMode ? ucfirst(str_replace('_', ' ', (string) $status)) : null));
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1 font-semibold rounded-full',
    'text-[11px] px-2.5 py-0.5',
    $style,
]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $dotClass }}" aria-hidden="true"></span>
    @elseif ($iconClass)
        <i class="{{ $iconClass }} text-[11px] shrink-0" aria-hidden="true"></i>
    @endif

    @if ($resolvedLabel)
        {{ $resolvedLabel }}
    @else
        {{ $slot }}
    @endif
</span>
