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
        ->assertSee('<span class="marker-animated marker"', false)
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

it('uses the configured greeting and highlights the configured phrase', function () {
    config()->set('portfolio.hero.greeting', 'Hey, {name} here:');
    config()->set('portfolio.headline', 'a developer who builds with AI');
    config()->set('portfolio.headline_highlight', 'AI');

    $this->get('/')
        ->assertSeeText('Hey, Sam Rivera here: a developer who builds with AI')
        ->assertSee('<span class="marker-animated marker"', false);
});

it('shows the configured floating notes around the hero', function () {
    config()->set('portfolio.hero.note', 'welcome!');
    config()->set('portfolio.hero.chat_note', 'try the chat');
    config()->set('portfolio.hero.cursor_label', 'hello?');

    $this->get('/')
        ->assertSee('welcome!')
        ->assertSee('try the chat')
        ->assertSee('hello?');
});

it('hides floating notes that are left empty', function () {
    config()->set('portfolio.hero.note', null);
    config()->set('portfolio.hero.chat_note', null);
    config()->set('portfolio.hero.cursor_label', null);

    $this->get('/')
        ->assertOk()
        ->assertDontSee('hi there!')
        ->assertDontSee('psst… ask my AI anything')
        ->assertDontSee('you?');
});

it('shows the stickers picked in the settings instead of the skills', function () {
    Skill::factory()->create(['name' => 'Quokkaflow', 'sort_order' => 0]);
    config()->set('portfolio.hero.stickers.items', [
        ['text' => 'Laravel Cloud', 'color' => 'rose'],
        ['text' => 'Coffee', 'color' => null],
    ]);

    $this->get('/')
        ->assertSeeInOrder(['bg-rose-200', 'Laravel Cloud', 'bg-amber-200', 'Coffee'], false)
        ->assertDontSee('>Quokkaflow<', false);
});

it('hides the stickers when they are turned off', function () {
    Skill::factory()->create(['name' => 'Quokkaflow']);
    config()->set('portfolio.hero.stickers.enabled', false);

    $this->get('/')->assertDontSee('>Quokkaflow<', false);
});

it('shows the stickers from the configured screen width', function () {
    Skill::factory()->create(['name' => 'Quokkaflow']);
    config()->set('portfolio.hero.stickers.breakpoint', '2xl');

    $this->get('/')
        ->assertSee('2xl:block', false)
        ->assertDontSee('xl:block"', false);
});

it('gives long highlighted phrases a marker that wraps with the text', function () {
    config()->set('portfolio.headline', 'a developer who builds fast and efficient apps with AI');
    config()->set('portfolio.headline_highlight', 'fast and efficient apps');

    $this->get('/')->assertSee('<span class="marker-animated marker-wrap"', false);
});
