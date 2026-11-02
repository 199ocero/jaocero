<?php

use App\Enums\MessageSource;
use App\Livewire\Pages\Contact;
use App\Models\Message;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

it('saves the message from the contact form', function () {
    Livewire::test(Contact::class)
        ->set('name', 'Jordan Lee')
        ->set('email', 'jordan@example.com')
        ->set('company', 'Acme')
        ->set('message', 'We would love to work with you.')
        ->call('send')
        ->assertHasNoErrors()
        ->assertSet('sent', true)
        ->assertSet('message', '');

    $message = Message::sole();

    expect($message)
        ->name->toBe('Jordan Lee')
        ->email->toBe('jordan@example.com')
        ->company->toBe('Acme')
        ->source->toBe(MessageSource::ContactForm);
});

it('stores an empty company as null', function () {
    Livewire::test(Contact::class)
        ->set('name', 'Jordan Lee')
        ->set('email', 'jordan@example.com')
        ->set('message', 'We would love to work with you.')
        ->call('send');

    expect(Message::sole()->company)->toBeNull();
});

it('requires a name, email and message', function () {
    Livewire::test(Contact::class)
        ->call('send')
        ->assertHasErrors(['name' => 'required', 'email' => 'required', 'message' => 'required']);

    expect(Message::count())->toBe(0);
});

it('rejects invalid input', function (string $field, string $value, string $rule) {
    Livewire::test(Contact::class)
        ->set($field, $value)
        ->call('send')
        ->assertHasErrors([$field => $rule]);
})->with([
    'invalid email' => ['email', 'not-an-email', 'email'],
    'message too short' => ['message', 'Hi', 'min'],
    'message too long' => ['message', str_repeat('a', 5001), 'max'],
]);

it('silently drops submissions that fill in the honeypot', function () {
    Livewire::test(Contact::class)
        ->set('name', 'Bot')
        ->set('email', 'bot@example.com')
        ->set('message', 'Buy cheap things now!!!')
        ->set('website', 'https://spam.example.com')
        ->call('send')
        ->assertSet('sent', true);

    expect(Message::count())->toBe(0);
});

it('rate limits messages per visitor', function () {
    config()->set('portfolio.contact.rate_limit.max_attempts', 2);
    RateLimiter::clear('contact:127.0.0.1');

    $send = fn () => Livewire::test(Contact::class)
        ->set('name', 'Jordan Lee')
        ->set('email', 'jordan@example.com')
        ->set('message', 'We would love to work with you.')
        ->call('send');

    $send()->assertHasNoErrors();
    $send()->assertHasNoErrors();
    $send()->assertHasErrors('form')->assertSee('Please try again in 10 minutes.');

    expect(Message::count())->toBe(2);
});
