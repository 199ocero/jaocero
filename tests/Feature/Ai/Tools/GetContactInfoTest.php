<?php

use App\Ai\Tools\GetContactInfo;
use App\Models\About;
use Laravel\Ai\Tools\Request;

it('returns the email, social links and contact page', function () {
    About::factory()->create([
        'email' => 'hello@example.com',
        'github_url' => 'https://github.com/example',
        'linkedin_url' => null,
        'x_url' => null,
        'open_to_work' => true,
    ]);

    expect(json_decode((new GetContactInfo)->handle(new Request), true))->toBe([
        'open_to_work' => true,
        'email' => 'hello@example.com',
        'contact_page' => route('contact'),
        'social_links' => ['GitHub' => 'https://github.com/example'],
    ]);
});

it('leaves out details that are not filled in', function () {
    About::factory()->create(['email' => null, 'github_url' => null, 'linkedin_url' => null, 'x_url' => null]);

    expect(json_decode((new GetContactInfo)->handle(new Request), true))
        ->not->toHaveKeys(['email', 'social_links'])
        ->toHaveKey('contact_page');
});
