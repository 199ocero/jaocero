<?php

use App\Models\About;

it('creates the about record on first access and reuses it afterwards', function () {
    $first = About::current();
    $second = About::current();

    expect($second->is($first))->toBeTrue()
        ->and(About::count())->toBe(1);
});

it('only lists social links that are filled in', function () {
    $about = About::factory()->make([
        'github_url' => 'https://github.com/example',
        'linkedin_url' => null,
        'x_url' => '',
    ]);

    expect($about->socialLinks())->toBe(['GitHub' => 'https://github.com/example']);
});
