<?php

use App\Livewire\Chat;
use App\Models\Project;
use Livewire\Livewire;

it('offers the floating chat on pages other than home', function (string $routeName) {
    $this->get(route($routeName))
        ->assertOk()
        ->assertSee('Ask my AI')
        ->assertSee('chat-widget-panel', false);
})->with(['projects.index', 'blog.index', 'about', 'contact']);

it('offers the floating chat on project pages', function () {
    $project = Project::factory()->create();

    $this->get(route('projects.show', $project))->assertSee('chat-widget-panel', false);
});

it('does not show the floating chat on the home page, which has the chat built in', function () {
    $this->get(route('home'))->assertDontSee('chat-widget-panel', false);
});

it('renders a close button and its own input id in compact mode', function () {
    Livewire::test(Chat::class, ['compact' => true])
        ->assertSee('Close chat')
        ->assertSee('id="chat-widget-input"', false);
});

it('renders without a close button in the home page hero', function () {
    Livewire::test(Chat::class)
        ->assertDontSee('Close chat')
        ->assertSee('id="chat-input"', false);
});
