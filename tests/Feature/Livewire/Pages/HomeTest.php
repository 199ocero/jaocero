<?php

use App\Models\About;
use App\Models\Post;
use App\Models\Project;
use App\Models\Skill;

beforeEach(function () {
    config()->set('portfolio.owner_name', 'Sam Rivera');
    config()->set('portfolio.headline', 'a Laravel developer');
    config()->set('portfolio.tagline', 'Ask my AI anything about my work.');
});

it('introduces the owner using the configured name, headline and tagline', function () {
    $this->get('/')
        ->assertOk()
        ->assertSeeText("Hi, I'm Sam Rivera — a Laravel developer")
        ->assertSee('Ask my AI anything about my work.');
});

it('renders the owner bio as crawlable text', function () {
    About::factory()->create(['bio' => "First paragraph.\n\nSecond paragraph."]);

    $this->get('/')
        ->assertSeeInOrder(['About Sam Rivera', 'First paragraph.', 'Second paragraph.']);
});

it('shows featured projects and the latest posts', function () {
    $featured = Project::factory()->featured()->create();
    $notFeatured = Project::factory()->create();
    $draftFeatured = Project::factory()->featured()->draft()->create();
    $post = Post::factory()->create();
    $draftPost = Post::factory()->draft()->create();

    $this->get('/')
        ->assertSee($featured->title)
        ->assertDontSee($notFeatured->title)
        ->assertDontSee($draftFeatured->title)
        ->assertSee($post->title)
        ->assertDontSee($draftPost->title);
});

it('links to the source code when a source URL is configured', function () {
    config()->set('portfolio.source_url', 'https://github.com/example/portfolio');

    $this->get('/')->assertSee('https://github.com/example/portfolio');
});

it('shows skills as stickers around the chat', function () {
    Skill::factory()->create(['name' => 'Livewire', 'sort_order' => 0]);

    $this->get('/')->assertSee('Livewire');
});

it('uses the tagline as the meta and social description', function () {
    $this->get('/')
        ->assertSee('<meta name="description" content="Ask my AI anything about my work.">', false)
        ->assertSee('<meta property="og:description" content="Ask my AI anything about my work.">', false)
        ->assertSee('<meta name="twitter:description" content="Ask my AI anything about my work.">', false);
});

it('highlights the marked phrase of the headline', function () {
    config()->set('portfolio.headline', 'a developer who builds with *AI*');

    $this->get('/')
        ->assertSee('<span class="marker marker-animated"', false)
        ->assertSeeText('who builds with AI')
        ->assertDontSee('*AI*');
});

it('renders the configured default theme', function (string $theme, bool $startsDark) {
    config()->set('portfolio.default_theme', $theme);

    $response = $this->get('/')->assertSee('data-default-theme="'.$theme.'"', false);

    $startsDark
        ? $response->assertSee('class="dark"', false)
        : $response->assertDontSee('class="dark"', false);
})->with([
    'light' => ['light', false],
    'dark' => ['dark', true],
]);
