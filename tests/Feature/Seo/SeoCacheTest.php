<?php

use App\Models\About;
use App\Models\Project;

it('regenerates llms.txt when a project is published', function () {
    $this->get('/llms.txt')->assertOk();

    $project = Project::factory()->create(['title' => 'Fresh Project']);

    $this->get('/llms.txt')->assertSee('Fresh Project');

    $project->delete();

    $this->get('/llms.txt')->assertDontSee('Fresh Project');
});

it('regenerates the owner structured data when the profile changes', function () {
    $about = About::factory()->create(['location' => 'Lisbon']);
    $this->get(route('home'))->assertSee('"name":"Lisbon"', false);

    $about->update(['location' => 'Porto']);

    $this->get(route('home'))->assertSee('"name":"Porto"', false);
});
