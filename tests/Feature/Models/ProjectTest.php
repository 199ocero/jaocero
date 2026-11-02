<?php

use App\Models\Project;

it('only returns projects whose publish date has passed', function () {
    $published = Project::factory()->create();
    Project::factory()->draft()->create();
    Project::factory()->scheduled()->create();

    expect(Project::published()->pluck('id')->all())->toBe([$published->id]);
});

it('orders published projects by sort order', function () {
    $second = Project::factory()->create(['sort_order' => 2]);
    $first = Project::factory()->create(['sort_order' => 1]);

    expect(Project::published()->pluck('id')->all())->toBe([$first->id, $second->id]);
});

it('only returns featured projects from the featured scope', function () {
    $featured = Project::factory()->featured()->create();
    Project::factory()->create();

    expect(Project::featured()->pluck('id')->all())->toBe([$featured->id]);
});
