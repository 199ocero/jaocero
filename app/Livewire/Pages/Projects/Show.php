<?php

namespace App\Livewire\Pages\Projects;

use App\Models\Project;
use App\Support\Seo\SeoData;
use App\Support\Seo\StructuredData;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Project $project;

    public function mount(Project $project): void
    {
        abort_unless($project->isPublished(), 404);

        $this->project = $project;
    }

    public function render(): View
    {
        return view('livewire.pages.projects.show')->layoutData([
            'seo' => new SeoData(
                title: $this->project->seo_title ?: $this->project->title,
                description: $this->project->seo_description ?: $this->project->summary,
                canonicalUrl: route('projects.show', $this->project),
                image: $this->project->thumbnailUrl(),
                type: 'article',
                publishedAt: $this->project->published_at,
                modifiedAt: $this->project->updated_at,
                jsonLd: [
                    StructuredData::project($this->project),
                    StructuredData::breadcrumbs([
                        'Home' => route('home'),
                        'Projects' => route('projects.index'),
                        $this->project->title => route('projects.show', $this->project),
                    ]),
                ],
            ),
        ]);
    }
}
