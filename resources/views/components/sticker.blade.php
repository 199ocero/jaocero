{{-- A floating, tappable sticker. Purely decorative, so it's hidden from assistive tech. --}}
@props(['tilt' => '0deg', 'color' => 'sky', 'delay' => '0s'])

@php
    $colors = [
        'sky' => 'bg-sky-200 text-sky-950 dark:bg-sky-300',
        'amber' => 'bg-amber-200 text-amber-950 dark:bg-amber-300',
        'rose' => 'bg-rose-200 text-rose-950 dark:bg-rose-300',
        'lime' => 'bg-lime-200 text-lime-950 dark:bg-lime-300',
    ];
@endphp

<div
    {{ $attributes->class('select-none motion-safe:animate-float') }}
    style="--tilt: {{ $tilt }}; animation-delay: {{ $delay }}"
    aria-hidden="true"
>
    <div
        x-data="{ taps: 0 }"
        x-on:click="taps++; $el.classList.remove('animate-pop'); void $el.offsetWidth; $el.classList.add('animate-pop')"
        class="relative cursor-pointer rounded-xl border-2 border-fg/80 px-3 py-1.5 text-sm font-bold shadow-[3px_3px_0_0_var(--fg)] {{ $colors[$color] ?? $colors['sky'] }}"
        style="--tilt: {{ $tilt }}; transform: rotate({{ $tilt }})"
    >
        {{ $slot }}
        <span
            x-show="taps > 0"
            x-transition
            x-text="['hehe', 'again!', 'okay okay 😅', 'you found a secret ✨'][Math.min(taps - 1, 3)]"
            class="hand-note absolute -top-7 left-1/2 -translate-x-1/2 text-lg whitespace-nowrap text-accent"
        ></span>
    </div>
</div>
