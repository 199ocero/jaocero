<?php

use App\Models\About;
use App\Models\Post;
use App\Models\Project;
use App\Support\Seo\AboutMarkdown;
use App\Support\Seo\PostMarkdown;
use App\Support\Seo\ProjectMarkdown;

it('renders a project as a markdown document', function () {
    $project = Project::factory()->create([
        'title' => 'Helpdesk Copilot',
        'summary' => 'AI replies for support teams.',
        'body' => "## The problem\n\nSlow replies.",
        'tech_stack' => ['Laravel'],
        'live_url' => null,
        'github_url' => 'https://github.com/example/helpdesk',
    ]);

    expect(ProjectMarkdown::render($project))
        ->toStartWith("# Helpdesk Copilot\n\n> AI replies for support teams.")
        ->toContain('- Tech stack: Laravel')
        ->toContain('- Source code: https://github.com/example/helpdesk')
        ->not->toContain('Live site')
        ->toEndWith("## The problem\n\nSlow replies.\n");
});

it('renders a post with its author and dates', function () {
    config()->set('portfolio.owner_name', 'Sam Rivera');
    $post = Post::factory()->create(['title' => 'Testing AI', 'published_at' => '2026-05-01', 'tags' => ['ai', 'testing']]);

    expect(PostMarkdown::render($post))
        ->toStartWith('# Testing AI')
        ->toContain('- Author: Sam Rivera')
        ->toContain('- Published: 2026-05-01')
        ->toContain('- Tags: ai, testing')
        ->toContain('- Page: '.route('blog.show', $post));
});

it('renders the owner profile with the configured name and tagline', function () {
    config()->set('portfolio.owner_name', 'Sam Rivera');
    config()->set('portfolio.tagline', 'A portfolio you can talk to.');
    $about = About::factory()->create(['bio' => "First.\n\nSecond.", 'email' => 'sam@example.com']);

    expect(AboutMarkdown::render($about))
        ->toStartWith("# Sam Rivera\n\n> A portfolio you can talk to.")
        ->toContain('- Email: sam@example.com')
        ->toContain("First.\n\nSecond.");
});
