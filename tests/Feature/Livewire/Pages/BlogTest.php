<?php

use App\Models\Post;

it('lists published posts only', function () {
    $published = Post::factory()->create();
    $draft = Post::factory()->draft()->create();
    $scheduled = Post::factory()->scheduled()->create();

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSee($published->title)
        ->assertDontSee($draft->title)
        ->assertDontSee($scheduled->title);
});

it('filters posts by tag', function () {
    $tagged = Post::factory()->create(['tags' => ['livewire']]);
    $other = Post::factory()->create(['tags' => ['devops']]);

    $this->get(route('blog.index', ['tag' => 'livewire']))
        ->assertSee($tagged->title)
        ->assertDontSee($other->title);
});

it('paginates posts ten per page', function () {
    Post::factory()->count(11)->create();

    $this->get(route('blog.index'))->assertSee('Page 1 of 2');
});

it('renders a post body as markdown', function () {
    $post = Post::factory()->create(['body' => "Intro.\n\n- first\n- second"]);

    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee('<li>first</li>', false)
        ->assertSee('min read');
});

it('escapes raw HTML in a post body', function () {
    $post = Post::factory()->create(['body' => 'Hello <script>alert(1)</script>']);

    $this->get(route('blog.show', $post))
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('&lt;script&gt;', false);
});

it('returns 404 for posts that are not published yet', function (string $state) {
    $post = Post::factory()->{$state}()->create();

    $this->get(route('blog.show', $post))->assertNotFound();
})->with(['draft', 'scheduled']);
