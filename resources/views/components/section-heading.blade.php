@props(['id', 'note' => null, 'doodle' => null, 'title', 'href' => null, 'linkLabel' => null])

<div {{ $attributes->class('flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between') }} data-reveal>
    <div class="flex flex-col gap-1">
        @if ($note)
            <div class="flex items-center gap-1.5">
                <x-hand-note class="-rotate-2 text-accent">{{ $note }}</x-hand-note>
                @if ($doodle)
                    <x-dynamic-component :component="'doodles.'.$doodle" class="size-6 text-accent" />
                @endif
            </div>
        @endif
        <h2 id="{{ $id }}" class="text-3xl font-extrabold tracking-[-0.035em] sm:text-4xl">{{ $title }}</h2>
    </div>
    @if ($href)
        <a href="{{ $href }}" wire:navigate class="group shrink-0 text-sm font-medium text-muted hover:text-fg">
            {{ $linkLabel }} <span class="inline-block transition group-hover:translate-x-0.5" aria-hidden="true">→</span>
        </a>
    @endif
</div>
