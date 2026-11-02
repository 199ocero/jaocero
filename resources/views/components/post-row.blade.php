@props(['post'])

<article {{ $attributes->class('group relative grid gap-1.5 border-t border-line py-6 sm:grid-cols-[9rem_1fr] sm:gap-8') }}>
    <p class="text-sm text-subtle tabular-nums">
        <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('M j, Y') }}</time>
    </p>

    <div class="flex flex-col gap-1.5">
        <h3 class="text-xl font-bold tracking-[-0.025em] transition group-hover:text-accent">
            <a href="{{ route('blog.show', $post) }}" wire:navigate class="after:absolute after:inset-0">
                {{ $post->title }}
            </a>
        </h3>
        <p class="text-muted">{{ $post->excerpt }}</p>
        <p class="text-xs text-subtle">{{ $post->readingTimeInMinutes() }} min read</p>
    </div>
</article>
