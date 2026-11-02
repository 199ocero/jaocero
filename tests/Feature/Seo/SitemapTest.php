<?php

use App\Models\Post;
use App\Models\Project;

it('lists the static pages and only published projects and posts', function () {
    $project = Project::factory()->create();
    $draftProject = Project::factory()->draft()->create();
    $post = Post::factory()->create(['updated_at' => '2026-05-01 10:00:00']);
    $scheduledPost = Post::factory()->scheduled()->create();

    $response = $this->get('/sitemap.xml');

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
    expect($response->getContent())
        ->toStartWith('<?xml version="1.0" encoding="UTF-8"?>')
        ->toContain('<loc>'.route('home').'</loc>')
        ->toContain('<loc>'.route('about').'</loc>')
        ->toContain('<loc>'.route('projects.show', $project).'</loc>')
        ->toContain('<loc>'.route('blog.show', $post).'</loc>')
        ->toContain('<lastmod>2026-05-01T10:00:00+00:00</lastmod>')
        ->not->toContain(route('projects.show', $draftProject))
        ->not->toContain(route('blog.show', $scheduledPost));
});
