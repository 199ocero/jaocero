<?php

namespace App\Ai\Tools;

use App\Models\Project;
use App\Support\Seo\ProjectMarkdown;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetProjectDetails implements Tool
{
    public function description(): Stringable|string
    {
        return 'Get the full write-up of one published project by its slug, including links and tech stack. Use GetProjects first to find the slug.';
    }

    public function handle(Request $request): Stringable|string
    {
        $slug = (string) $request->string('slug');

        $project = Project::published()->where('slug', $slug)->first();

        if ($project === null) {
            return "No published project has the slug \"{$slug}\". Call GetProjects to see the available projects.";
        }

        return ProjectMarkdown::render($project);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'slug' => $schema->string()
                ->description('The project slug, e.g. "helpdesk-copilot".')
                ->required(),
        ];
    }
}
