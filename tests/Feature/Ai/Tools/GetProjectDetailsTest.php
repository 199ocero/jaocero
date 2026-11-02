<?php

use App\Ai\Tools\GetProjectDetails;
use App\Models\Project;
use Laravel\Ai\Tools\Request;

it('returns the full write-up of a published project', function () {
    $project = Project::factory()->create([
        'title' => 'Helpdesk Copilot',
        'body' => 'It drafts replies to support tickets.',
        'tech_stack' => ['Laravel', 'Livewire'],
    ]);

    $result = (string) (new GetProjectDetails)->handle(new Request(['slug' => $project->slug]));

    expect($result)
        ->toContain('# Helpdesk Copilot')
        ->toContain('It drafts replies to support tickets.')
        ->toContain('Tech stack: Laravel, Livewire')
        ->toContain(route('projects.show', $project));
});

it('does not reveal projects that are not published', function (string $state) {
    $project = Project::factory()->{$state}()->create(['body' => 'Top secret details']);

    $result = (string) (new GetProjectDetails)->handle(new Request(['slug' => $project->slug]));

    expect($result)
        ->not->toContain('Top secret details')
        ->toContain("No published project has the slug \"{$project->slug}\"");
})->with(['draft', 'scheduled']);

it('points the model to GetProjects for an unknown slug', function () {
    expect((string) (new GetProjectDetails)->handle(new Request(['slug' => 'missing'])))
        ->toContain('Call GetProjects');
});
