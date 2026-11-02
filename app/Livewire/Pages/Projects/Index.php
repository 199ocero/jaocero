<?php

namespace App\Livewire\Pages\Projects;

use App\Models\Project;
use App\Support\Seo\SeoData;
use App\Support\Seo\StructuredData;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    /**
     * Optional technology filter, e.g. /projects?tech=Laravel.
     */
    #[Url]
    public string $tech = '';

    public function render(): View
    {
        $projects = Project::published()->get();
        $visibleProjects = $this->tech === ''
            ? $projects
            : $projects->filter(fn (Project $project): bool => in_array($this->tech, $project->tech_stack ?? [], true));
        $description = 'Projects built by '.config('portfolio.owner_name').', with write-ups, tech stacks and links.';

        return view('livewire.pages.projects.index', [
            'projects' => $visibleProjects,
            'technologies' => $this->technologies($projects),
        ])->layoutData([
            'seo' => new SeoData(
                title: 'Projects',
                description: $description,
                canonicalUrl: route('projects.index'),
                jsonLd: [StructuredData::collectionPage('Projects', $description, route('projects.index'), $visibleProjects
                    ->map(fn (Project $project): array => ['name' => $project->title, 'url' => route('projects.show', $project)])
                    ->all())],
            ),
        ]);
    }

    /**
     * Every technology used across published projects, most used first.
     *
     * @param  Collection<int, Project>  $projects
     * @return list<string>
     */
    private function technologies(Collection $projects): array
    {
        return array_values($projects
            ->flatMap(fn (Project $project): array => $project->tech_stack ?? [])
            ->countBy()
            ->sortDesc()
            ->keys()
            // Array keys turn numeric names (e.g. "5") into integers, so restore them as strings.
            ->map(fn (int|string $technology): string => (string) $technology)
            ->all());
    }
}
