<?php

use App\Filament\Pages\ManageAbout;
use App\Models\About;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    config()->set('portfolio.admin_emails', ['owner@example.com']);

    $this->actingAs(User::factory()->create(['email' => 'owner@example.com']));
});

it('loads the existing profile into the form', function () {
    About::factory()->create(['location' => 'Remote']);

    Livewire::test(ManageAbout::class)
        ->assertSchemaStateSet(['location' => 'Remote']);
});

it('saves the profile to the single about record', function () {
    Livewire::test(ManageAbout::class)
        ->fillForm([
            'headline' => 'Laravel developer',
            'location' => 'Manila',
            'email' => 'hello@example.com',
            'open_to_work' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    expect(About::count())->toBe(1)
        ->and(About::current())
        ->location->toBe('Manila')
        ->open_to_work->toBeTrue();
});

it('rejects an invalid email address', function () {
    Livewire::test(ManageAbout::class)
        ->fillForm(['email' => 'not-an-email'])
        ->call('save')
        ->assertHasFormErrors(['email' => 'email']);
});
