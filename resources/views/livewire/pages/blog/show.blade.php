<x-container class="max-w-3xl py-16 sm:py-24">
    <article class="flex flex-col gap-8">
        <a href="{{ route('blog.index') }}" wire:navigate class="text-sm text-muted hover:text-fg">← All posts</a>

        <header class="flex flex-col gap-4">
            <h1 class="text-4xl font-extrabold tracking-[-0.04em] sm:text-5xl">{{ $post->title }}</h1>
            <p class="text-sm text-subtle">
                By <a href="{{ route('about') }}" wire:navigate rel="author" class="text-muted hover:text-fg">{{ config('portfolio.owner_name') }}</a>
                · Published <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('F j, Y') }}</time>
                @if ($post->updated_at && $post->published_at && $post->updated_at->isAfter($post->published_at->copy()->addDay()))
                    · Updated <time datetime="{{ $post->updated_at->toDateString() }}">{{ $post->updated_at->format('F j, Y') }}</time>
                @endif
                · {{ $post->readingTimeInMinutes() }} min read
            </p>
            @if ($post->tags)
                <ul class="flex flex-wrap gap-2" aria-label="Tags">
                    @foreach ($post->tags as $tag)
                        <li><x-tag :href="route('blog.index', ['tag' => $tag])">{{ $tag }}</x-tag></li>
                    @endforeach
                </ul>
            @endif
        </header>

        @if ($post->coverImageUrl())
            <img src="{{ $post->coverImageUrl() }}" alt="" class="w-full rounded-2xl border border-line">
        @endif

        <div class="prose-content">{{ \App\Support\Markdown::render($post->body) }}</div>
    </article>
</x-container>
