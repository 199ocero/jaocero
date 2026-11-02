@php
    $links = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Projects', 'route' => 'projects.index'],
        ['label' => 'Blog', 'route' => 'blog.index'],
        ['label' => 'About', 'route' => 'about'],
        ['label' => 'Contact', 'route' => 'contact'],
    ];
@endphp

<header
    x-data="{ open: false }"
    x-on:keydown.escape.window="open = false"
    class="sticky top-0 z-40 bg-canvas/85 backdrop-blur-lg"
>
    <nav class="mx-auto flex h-16 max-w-5xl items-center justify-between gap-4 px-4 sm:px-6" aria-label="Main">
        <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2.5 font-bold tracking-[-0.02em]">
            <span class="grid size-8 place-items-center rounded-full bg-fg text-xs font-bold text-canvas" aria-hidden="true">
                {{ \Illuminate\Support\Str::initials(config('portfolio.owner_name')) }}
            </span>
            <span>{{ config('portfolio.owner_name') }}</span>
        </a>

        <div class="flex items-center gap-1">
            <ul class="hidden items-center gap-1 md:flex">
                @foreach ($links as $link)
                    <li>
                        <a
                            href="{{ route($link['route']) }}"
                            wire:navigate
                            class="rounded-full px-3.5 py-2 text-sm font-medium text-muted transition hover:text-fg data-current:bg-surface data-current:text-fg"
                        >{{ $link['label'] }}</a>
                    </li>
                @endforeach
            </ul>

            @include('partials.theme-toggle')

            <button
                type="button"
                class="grid size-10 place-items-center rounded-lg text-muted hover:bg-surface hover:text-fg md:hidden"
                x-on:click="open = ! open"
                x-bind:aria-expanded="open"
                aria-controls="mobile-menu"
            >
                <span class="sr-only">Toggle menu</span>
                <svg x-show="! open" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg x-show="open" x-cloak class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </nav>

    <div id="mobile-menu" x-show="open" x-cloak x-transition.opacity class="border-t border-line md:hidden">
        <ul class="mx-auto flex max-w-5xl flex-col gap-1 px-4 py-3">
            @foreach ($links as $link)
                <li>
                    <a
                        href="{{ route($link['route']) }}"
                        wire:navigate
                        x-on:click="open = false"
                        class="block rounded-lg px-3 py-2.5 text-muted hover:bg-surface hover:text-fg data-current:bg-surface data-current:font-medium data-current:text-fg"
                    >{{ $link['label'] }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</header>
