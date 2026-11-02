<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Concerns\ReturnsJson;
use App\Models\Project;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetProjects implements Tool
{
    use ReturnsJson;

    public function description(): Stringable|string
    {
        return "List the owner's published projects with a summary, tech stack and page URL. Optionally filter by a keyword or technology.";
    }

    public function handle(Request $request): Stringable|string
    {
        $filter = trim((string) $request->string('filter'));

        $projects = Project::published()->get()
            ->when($filter !== '', fn ($projects) => $projects->filter(
                fn (Project $project): bool => $this->matches($project, $filter),
            ));

        if ($projects->isEmpty()) {
            return $filter === ''
                ? 'There are no published projects yet.'
                : "No published projects match \"{$filter}\".";
        }

        return $this->json($projects->map(fn (Project $project): array => [
            'title' => $project->title,
            'slug' => $project->slug,
            'summary' => $project->summary,
            'tech_stack' => $project->tech_stack ?? [],
            'featured' => $project->is_featured,
            'url' => route('projects.show', $project),
        ])->values()->all());
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'filter' => $schema->string()
                ->description('Optional keyword or technology to filter by, e.g. "Laravel" or "AI".'),
        ];
    }

    private function matches(Project $project, string $filter): bool
    {
        $searchable = implode(' ', [$project->title, $project->summary, ...($project->tech_stack ?? [])]);

        return Str::contains($searchable, $filter, ignoreCase: true);
    }
}
