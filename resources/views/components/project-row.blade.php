@props(['project'])

<article {{ $attributes->class('group relative grid gap-3 border-t border-line py-7 sm:grid-cols-[1fr_auto] sm:gap-8') }}>
    <div class="flex flex-col gap-2.5">
        <h3 class="text-2xl font-bold tracking-[-0.03em] transition group-hover:text-accent">
            <a href="{{ route('projects.show', $project) }}" wire:navigate class="after:absolute after:inset-0">
                {{ $project->title }}
            </a>
        </h3>
        <p class="max-w-2xl text-muted">{{ $project->summary }}</p>
        @if ($project->tech_stack)
            <ul class="flex flex-wrap gap-1.5 pt-1" aria-label="Tech stack">
                @foreach ($project->tech_stack as $technology)
                    <li><x-tag>{{ $technology }}</x-tag></li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="flex items-center gap-4 text-sm text-subtle sm:flex-col sm:items-end sm:justify-between">
        @if ($project->published_at)
            <time datetime="{{ $project->published_at->toDateString() }}" class="font-medium tabular-nums">{{ $project->published_at->format('Y') }}</time>
        @endif
        <span class="grid size-9 place-items-center rounded-full border border-line bg-elevated transition group-hover:-rotate-45 group-hover:border-accent group-hover:bg-accent group-hover:text-on-accent" aria-hidden="true">→</span>
    </div>
</article>
