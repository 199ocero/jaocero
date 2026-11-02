<?php

use App\Ai\Tools\SaveLead;
use App\Enums\MessageSource;
use App\Models\Message;
use Illuminate\Validation\ValidationException;
use Laravel\Ai\Tools\Request;

it('saves the lead as a chatbot message', function () {
    $result = (new SaveLead)->handle(new Request([
        'name' => 'Jordan Lee',
        'email' => 'jordan@example.com',
        'company' => 'Acme',
        'message' => 'We would like to hire you for a Laravel project.',
    ]));

    expect((string) $result)->toContain('saved')
        ->and(Message::sole())
        ->name->toBe('Jordan Lee')
        ->email->toBe('jordan@example.com')
        ->company->toBe('Acme')
        ->source->toBe(MessageSource::Chatbot);
});

it('stores a missing company as null', function () {
    (new SaveLead)->handle(new Request([
        'name' => 'Jordan Lee',
        'email' => 'jordan@example.com',
        'message' => 'Hello!',
    ]));

    expect(Message::sole()->company)->toBeNull();
});

it('rejects incomplete or invalid details without saving', function (array $arguments) {
    expect(fn () => (new SaveLead)->handle(new Request($arguments)))
        ->toThrow(ValidationException::class);

    expect(Message::count())->toBe(0);
})->with([
    'missing name' => [['email' => 'jordan@example.com', 'message' => 'Hi']],
    'missing email' => [['name' => 'Jordan', 'message' => 'Hi']],
    'invalid email' => [['name' => 'Jordan', 'email' => 'not-an-email', 'message' => 'Hi']],
    'missing message' => [['name' => 'Jordan', 'email' => 'jordan@example.com']],
]);
