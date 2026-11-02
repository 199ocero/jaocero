<x-container class="flex max-w-4xl flex-col gap-10 py-16 sm:py-24">
    <x-page-header
        eyebrow="a few of my favorites"
        doodle="code"
        title="Things I've built"
        description="A selection of products, tools and experiments, each with a short write-up of the problem and how I solved it."
    />

    @if (count($technologies) > 1)
        <nav aria-label="Filter by technology" class="flex flex-wrap gap-2" data-reveal style="--reveal-delay: 120ms">
            <x-tag :href="route('projects.index')" :active="$tech === ''">All</x-tag>
            @foreach ($technologies as $technology)
                <x-tag :href="route('projects.index', ['tech' => $technology])" :active="$tech === $technology" wire:key="tech-{{ $technology }}">
                    {{ $technology }}
                </x-tag>
            @endforeach
        </nav>
    @endif

    @if ($projects->isEmpty())
        <p class="text-muted">No projects to show yet.</p>
    @else
        <div class="border-b border-line">
            @foreach ($projects as $project)
                <x-project-row :$project wire:key="project-{{ $project->id }}" data-reveal style="--reveal-delay: {{ min($loop->index, 4) * 80 }}ms" />
            @endforeach
        </div>
    @endif
</x-container>
