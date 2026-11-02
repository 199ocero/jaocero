<?php

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Models\Project;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    config()->set('portfolio.admin_emails', ['owner@example.com']);

    $this->actingAs(User::factory()->create(['email' => 'owner@example.com']));
});

it('creates a project with its tech stack', function () {
    Livewire::test(CreateProject::class)
        ->fillForm([
            'title' => 'Helpdesk Copilot',
            'slug' => 'helpdesk-copilot',
            'summary' => 'An AI assistant for support teams.',
            'tech_stack' => ['Laravel', 'Livewire'],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $project = Project::firstWhere('slug', 'helpdesk-copilot');

    expect($project->tech_stack)->toBe(['Laravel', 'Livewire'])
        ->and($project->published_at)->toBeNull();
});

it('fills in the slug from the title', function () {
    Livewire::test(CreateProject::class)
        ->fillForm(['title' => 'Status Pulse'])
        ->assertSchemaStateSet(['slug' => 'status-pulse']);
});

it('rejects a slug that is already taken', function () {
    Project::factory()->create(['slug' => 'taken']);

    Livewire::test(CreateProject::class)
        ->fillForm([
            'title' => 'Another project',
            'slug' => 'taken',
            'summary' => 'Summary',
        ])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'unique']);
});
