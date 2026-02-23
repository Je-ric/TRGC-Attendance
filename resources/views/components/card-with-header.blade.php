@props(['title', 'description' => null, 'icon' => null])

<style>
    .page-eyebrow {
        font-size: 10.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #DFC056;
        margin-bottom: 2px;
    }
</style>
<div
    class="w-full mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-6 md:gap-0 mb-5
            border-b-4 border-yellow-500 pb-4">
    <div class="flex flex-col grow">
        <div class="page-eyebrow">People</div>
        <h2 class="page-title text-3xl text-[#6B0F1A] flex items-center gap-2">
            <i class='bx {{ $icon ?? 'bx-user-voice' }} text-[#C9A84C]'></i>
            {{ $title }}
        </h2>
        @if ($description)
            <p class="dm-sans text-sm mt-1 ">
                {{ $description }}
            </p>
        @endif
    </div>

    <div class="mt-4 md:mt-0">
        {{ $slot }} {{-- Anything, HAHAHAHA atleast hindi nagcecenter  --}}
    </div>
</div>
