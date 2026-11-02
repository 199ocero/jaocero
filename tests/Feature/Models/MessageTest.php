<?php

use App\Models\Message;

it('marks a message as read', function () {
    $message = Message::factory()->create();

    $message->markAsRead();

    expect($message->fresh()->isRead())->toBeTrue();
});

it('only returns unread messages from the unread scope', function () {
    $unread = Message::factory()->create();
    Message::factory()->read()->create();

    expect(Message::unread()->pluck('id')->all())->toBe([$unread->id]);
});
