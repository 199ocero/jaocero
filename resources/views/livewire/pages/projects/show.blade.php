<x-container class="max-w-3xl py-16 sm:py-24">
    <article class="flex flex-col gap-8">
        <a href="{{ route('projects.index') }}" wire:navigate class="text-sm text-muted hover:text-fg">← All projects</a>

        <x-page-header :title="$project->title" :description="$project->summary">
            <div class="flex flex-wrap items-center gap-3 pt-2">
                @if ($project->live_url)
                    <a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="rounded-full bg-accent px-5 py-2.5 text-sm font-semibold text-on-accent hover:bg-accent-strong">
                        Visit live site
                    </a>
                @endif
                @if ($project->github_url)
                    <a href="{{ $project->github_url }}" target="_blank" rel="noopener" class="rounded-full border border-line bg-elevated px-5 py-2.5 text-sm font-semibold hover:border-accent hover:text-accent">
                        View on GitHub
                    </a>
                @endif
            </div>
        </x-page-header>

        @if ($project->thumbnailUrl())
            <img src="{{ $project->thumbnailUrl() }}" alt="{{ $project->title }} screenshot" class="w-full rounded-2xl border border-line">
        @endif

        @if ($project->tech_stack)
            <ul class="flex flex-wrap gap-2" aria-label="Tech stack">
                @foreach ($project->tech_stack as $technology)
                    <li><x-tag :href="route('projects.index', ['tech' => $technology])">{{ $technology }}</x-tag></li>
                @endforeach
            </ul>
        @endif

        @if ($project->body)
            <div class="prose-content">{{ \App\Support\Markdown::render($project->body) }}</div>
        @endif
    </article>
</x-container>
