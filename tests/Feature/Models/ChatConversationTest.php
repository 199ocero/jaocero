<?php

use App\Models\ChatConversation;
use App\Models\ChatMessage;

it('counts only the visitor questions as turns', function () {
    $conversation = ChatConversation::factory()->create();
    ChatMessage::factory()->count(2)->for($conversation, 'conversation')->create();
    ChatMessage::factory()->fromAssistant()->for($conversation, 'conversation')->create();

    expect($conversation->userTurns())->toBe(2);
});

it('returns messages in the order they were sent', function () {
    $conversation = ChatConversation::factory()->create();
    $first = ChatMessage::factory()->for($conversation, 'conversation')->create();
    $second = ChatMessage::factory()->fromAssistant()->for($conversation, 'conversation')->create();

    expect($conversation->messages->pluck('id')->all())->toBe([$first->id, $second->id]);
});
