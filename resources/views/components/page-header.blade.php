@props(['eyebrow' => null, 'title', 'description' => null, 'doodle' => null])

<header {{ $attributes->class('flex flex-col gap-3') }} data-reveal>
    @if ($eyebrow)
        <div class="flex items-center gap-1.5">
            <x-hand-note class="-rotate-2 text-accent">{{ $eyebrow }}</x-hand-note>
            @if ($doodle)
                <x-dynamic-component :component="'doodles.'.$doodle" class="size-6 text-accent" />
            @endif
        </div>
    @endif
    <h1 class="text-4xl font-extrabold tracking-[-0.04em] sm:text-5xl">{{ $title }}</h1>
    @if ($description)
        <p class="max-w-2xl text-lg text-pretty text-muted">{{ $description }}</p>
    @endif
    {{ $slot }}
</header>
