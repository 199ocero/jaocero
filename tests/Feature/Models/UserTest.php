<?php

use App\Models\User;
use Filament\Facades\Filament;

it('allows only allowlisted emails into the admin panel', function (string $email, bool $allowed) {
    config()->set('portfolio.admin_emails', ['owner@example.com']);

    $user = User::factory()->make(['email' => $email]);

    expect($user->canAccessPanel(Filament::getPanel('admin')))->toBe($allowed);
})->with([
    'allowlisted' => ['owner@example.com', true],
    'allowlisted, different case' => ['Owner@Example.com', true],
    'not allowlisted' => ['visitor@example.com', false],
]);

it('denies everyone when the allowlist is empty', function () {
    config()->set('portfolio.admin_emails', []);

    $user = User::factory()->make(['email' => 'owner@example.com']);

    expect($user->canAccessPanel(Filament::getPanel('admin')))->toBeFalse();
});
