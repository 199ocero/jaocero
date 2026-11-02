<?php

use App\Ai\Tools\GetAbout;
use App\Models\About;
use Laravel\Ai\Tools\Request;

it("returns the owner's profile", function () {
    config()->set('portfolio.owner_name', 'Sam Rivera');
    About::factory()->create([
        'bio' => 'I build calm software.',
        'location' => 'Lisbon',
        'open_to_work' => true,
        'github_url' => 'https://github.com/sam',
    ]);

    expect((string) (new GetAbout)->handle(new Request))
        ->toContain('# Sam Rivera')
        ->toContain('I build calm software.')
        ->toContain('Location: Lisbon')
        ->toContain('Availability: Open to work')
        ->toContain('GitHub: https://github.com/sam');
});

it('reports when the owner is not looking for work', function () {
    About::factory()->create(['open_to_work' => false]);

    expect((string) (new GetAbout)->handle(new Request))
        ->toContain('Availability: Not currently looking for work');
});
