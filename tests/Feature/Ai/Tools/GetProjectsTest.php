<?php

use App\Ai\Tools\GetProjects;
use App\Models\Project;
use Laravel\Ai\Tools\Request;

it('lists published projects with their page URLs', function () {
    $project = Project::factory()->create(['title' => 'Helpdesk Copilot', 'slug' => 'helpdesk-copilot']);
    Project::factory()->draft()->create(['title' => 'Secret Draft']);
    Project::factory()->scheduled()->create(['title' => 'Coming Soon']);

    $result = json_decode((new GetProjects)->handle(new Request), true);

    expect($result)->toHaveCount(1)
        ->and($result[0])->toMatchArray([
            'title' => 'Helpdesk Copilot',
            'slug' => 'helpdesk-copilot',
            'url' => route('projects.show', $project),
        ]);
});

it('filters projects by keyword across title, summary and tech stack', function (string $filter) {
    Project::factory()->create(['title' => 'Helpdesk Copilot', 'summary' => 'An AI assistant', 'tech_stack' => ['Laravel']]);
    Project::factory()->create(['title' => 'Status Pulse', 'summary' => 'Uptime monitor', 'tech_stack' => ['Redis']]);

    $result = json_decode((new GetProjects)->handle(new Request(['filter' => $filter])), true);

    expect(array_column($result, 'title'))->toBe(['Helpdesk Copilot']);
})->with([
    'title' => ['helpdesk'],
    'summary' => ['ai assistant'],
    'tech stack' => ['laravel'],
]);

it('says so when nothing matches the filter', function () {
    Project::factory()->create(['tech_stack' => ['Laravel']]);

    expect((string) (new GetProjects)->handle(new Request(['filter' => 'Rust'])))
        ->toBe('No published projects match "Rust".');
});

it('says so when there are no projects at all', function () {
    expect((string) (new GetProjects)->handle(new Request))
        ->toBe('There are no published projects yet.');
});
