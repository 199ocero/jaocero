<?php

use App\Models\About;
use App\Models\Experience;
use App\Models\Post;
use App\Models\Project;
use App\Models\Skill;
use Database\Seeders\DemoSeeder;

it('seeds a complete set of published demo content', function () {
    $this->seed(DemoSeeder::class);

    expect(About::count())->toBe(1)
        ->and(Project::published()->count())->toBe(5)
        ->and(Project::featured()->count())->toBe(3)
        ->and(Post::published()->count())->toBe(4)
        ->and(Skill::count())->toBeGreaterThan(10)
        ->and(Experience::count())->toBe(3);
});

it('can be run twice without duplicating content', function () {
    $this->seed(DemoSeeder::class);
    $this->seed(DemoSeeder::class);

    expect(About::count())->toBe(1)
        ->and(Project::count())->toBe(5)
        ->and(Post::count())->toBe(4);
});
