<?php

use App\Filament\Resources\Messages\Pages\ListMessages;
use App\Filament\Resources\Messages\Pages\ViewMessage;
use App\Models\Message;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(function () {
    config()->set('portfolio.admin_emails', ['owner@example.com']);

    $this->actingAs(User::factory()->create(['email' => 'owner@example.com']));
});

it('marks a message as read when it is opened', function () {
    $message = Message::factory()->create();

    Livewire::test(ViewMessage::class, ['record' => $message->id])->assertOk();

    expect($message->fresh()->isRead())->toBeTrue();
});

it('marks a message as read from the table', function () {
    $message = Message::factory()->create();

    Livewire::test(ListMessages::class)
        ->callAction(TestAction::make('markAsRead')->table($message));

    expect($message->fresh()->isRead())->toBeTrue();
});

it('hides the mark as read action for messages that are already read', function () {
    $message = Message::factory()->read()->create();

    Livewire::test(ListMessages::class)
        ->assertActionHidden(TestAction::make('markAsRead')->table($message));
});
