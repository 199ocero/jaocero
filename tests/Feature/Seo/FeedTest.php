<?php

use App\Models\Post;

it('publishes an Atom feed of published posts only', function () {
    $post = Post::factory()->create(['title' => 'Streaming with Livewire', 'body' => 'Uses **wire:stream**.']);
    $draft = Post::factory()->draft()->create();

    $response = $this->get('/feed.xml');

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/atom+xml; charset=UTF-8');
    expect($response->getContent())
        ->toStartWith('<?xml version="1.0" encoding="UTF-8"?>')
        ->toContain('<title>Streaming with Livewire</title>')
        ->toContain('<id>'.route('blog.show', $post).'</id>')
        ->toContain('&lt;strong&gt;wire:stream&lt;/strong&gt;')
        ->not->toContain($draft->title);
});

it('is linked from every page head', function () {
    $this->get(route('about'))
        ->assertOk()
        ->assertSee('<link rel="alternate" type="application/atom+xml" title="'.config('portfolio.owner_name').'" href="'.route('feed').'">', false);
});
