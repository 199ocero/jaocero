<?php

namespace App\Support\Seo;

use App\Models\Project;

/**
 * Renders a project as clean markdown for AI agents (chat tools, llms.txt, .md pages).
 */
class ProjectMarkdown
{
    public static function render(Project $project): string
    {
        $facts = array_filter([
            'Page' => route('projects.show', $project),
            'Tech stack' => $project->tech_stack ? implode(', ', $project->tech_stack) : null,
            'Live site' => $project->live_url,
            'Source code' => $project->github_url,
            'Published' => $project->published_at?->toDateString(),
        ]);

        return MarkdownDocument::make()
            ->heading($project->title)
            ->quote($project->summary)
            ->facts($facts)
            ->paragraph($project->body)
            ->toString();
    }
}
