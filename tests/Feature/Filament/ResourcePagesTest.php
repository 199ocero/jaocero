<?php

use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Message;
use App\Models\Post;
use App\Models\Project;
use App\Models\User;

beforeEach(function () {
    config()->set('portfolio.admin_emails', ['owner@example.com']);

    $this->actingAs(User::factory()->create(['email' => 'owner@example.com']));
});

it('renders every admin page', function (string $url) {
    $this->get($url)->assertOk();
})->with([
    'dashboard' => '/admin',
    'projects' => '/admin/projects',
    'new project' => '/admin/projects/create',
    'posts' => '/admin/posts',
    'new post' => '/admin/posts/create',
    'skills' => '/admin/skills',
    'experience' => '/admin/experiences',
    'messages' => '/admin/messages',
    'chat logs' => '/admin/chat-conversations',
    'about' => '/admin/about',
]);

it('renders the edit pages for projects and posts', function () {
    $project = Project::factory()->create();
    $post = Post::factory()->create();

    $this->get(ProjectResource::getUrl('edit', ['record' => $project]))->assertOk()->assertSee($project->title);
    $this->get(PostResource::getUrl('edit', ['record' => $post]))->assertOk()->assertSee($post->title);
});

it('shows a chat transcript', function () {
    $message = ChatMessage::factory()->create(['content' => 'What have you built?']);

    $this->get("/admin/chat-conversations/{$message->chat_conversation_id}")
        ->assertOk()
        ->assertSee('What have you built?');
});

it('does not offer pages for creating messages or chat logs', function () {
    $this->get('/admin/messages/create')->assertNotFound();
    $this->get('/admin/chat-conversations/create')->assertNotFound();
    $this->get('/admin/messages/'.Message::factory()->create()->id.'/edit')->assertNotFound();
    $this->get('/admin/chat-conversations/'.ChatConversation::factory()->create()->id.'/edit')->assertNotFound();
});
