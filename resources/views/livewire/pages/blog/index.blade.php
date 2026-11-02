<x-container class="flex max-w-4xl flex-col gap-10 py-16 sm:py-24">
    <x-page-header eyebrow="notes & ideas" doodle="sparkle" title="Writing" description="Notes on building software, what I'm learning, and how I work.">
        @if ($tag !== '')
            <p class="flex items-center gap-2 text-sm text-muted">
                Tagged <x-tag active>{{ $tag }}</x-tag>
                <a href="{{ route('blog.index') }}" wire:navigate class="hover:text-fg">Clear</a>
            </p>
        @endif
    </x-page-header>

    @if ($posts->isEmpty())
        <p class="text-muted">No posts yet.</p>
    @else
        <div class="border-b border-line">
            @foreach ($posts as $post)
                <x-post-row :$post wire:key="post-{{ $post->id }}" data-reveal style="--reveal-delay: {{ min($loop->index, 4) * 80 }}ms" />
            @endforeach
        </div>

        @if ($posts->hasPages())
            <nav class="flex items-center justify-between text-sm" aria-label="Pagination">
                @if ($posts->onFirstPage())
                    <span></span>
                @else
                    <button type="button" wire:click="previousPage" class="rounded-full border border-line bg-elevated px-4 py-2 font-medium hover:border-accent hover:text-accent">← Newer</button>
                @endif
                <span class="text-subtle">Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</span>
                @if ($posts->hasMorePages())
                    <button type="button" wire:click="nextPage" class="rounded-full border border-line bg-elevated px-4 py-2 font-medium hover:border-accent hover:text-accent">Older →</button>
                @else
                    <span></span>
                @endif
            </nav>
        @endif
    @endif
</x-container>
