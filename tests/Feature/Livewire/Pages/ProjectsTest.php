<?php

use App\Models\Project;

it('lists published projects only', function () {
    $published = Project::factory()->create();
    $draft = Project::factory()->draft()->create();
    $scheduled = Project::factory()->scheduled()->create();

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertSee($published->title)
        ->assertDontSee($draft->title)
        ->assertDontSee($scheduled->title);
});

it('filters projects by technology', function () {
    $laravel = Project::factory()->create(['tech_stack' => ['Laravel']]);
    $vue = Project::factory()->create(['tech_stack' => ['Vue']]);

    $this->get(route('projects.index', ['tech' => 'Laravel']))
        ->assertSee($laravel->title)
        ->assertDontSee($vue->title);
});

it('shows a project with its markdown write-up and links', function () {
    $project = Project::factory()->create([
        'body' => "## The problem\n\nIt was **slow**.",
        'github_url' => 'https://github.com/example/repo',
    ]);

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertSee($project->title)
        ->assertSee('<h2>The problem</h2>', false)
        ->assertSee('<strong>slow</strong>', false)
        ->assertSee('https://github.com/example/repo');
});

it('returns 404 for projects that are not published yet', function (string $state) {
    $project = Project::factory()->{$state}()->create();

    $this->get(route('projects.show', $project))->assertNotFound();
})->with(['draft', 'scheduled']);

it('prefers the SEO overrides for the page title and description', function () {
    $project = Project::factory()->create([
        'seo_title' => 'Custom SEO title',
        'seo_description' => 'Custom SEO description',
    ]);

    $this->get(route('projects.show', $project))
        ->assertSee('<title>Custom SEO title · '.config('portfolio.owner_name').'</title>', false)
        ->assertSee('<meta name="description" content="Custom SEO description">', false);
});
