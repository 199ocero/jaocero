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

it('hides the floating chat when it is turned off in the settings', function () {
    config()->set('portfolio.chat_widget.enabled', false);

    $this->get(route('about'))->assertDontSee('chat-widget-panel', false);
});

it('shows the button label and note from the settings', function () {
    config()->set('portfolio.chat_widget.label', 'Chat with me');
    config()->set('portfolio.chat_widget.note', 'say hi!');

    $this->get(route('about'))
        ->assertSee('Chat with me')
        ->assertSee('say hi!');
});

it('hides the note when it is empty', function () {
    config()->set('portfolio.chat_widget.note', null);

    $this->get(route('about'))->assertDontSee('psst… still here');
});

it('can sit in the bottom left corner', function () {
    config()->set('portfolio.chat_widget.position', 'bottom-left');

    $this->get(route('about'))->assertSee('left-4 items-start', false);
});
