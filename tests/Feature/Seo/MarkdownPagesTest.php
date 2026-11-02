<?php

use App\Models\About;
use App\Models\Post;
use App\Models\Project;

it('serves a markdown version of each page with a canonical link to the HTML page', function (string $markdownPath, string $htmlPath) {
    Project::factory()->create(['slug' => 'helpdesk-copilot']);
    Post::factory()->create(['slug' => 'testing-ai']);

    $response = $this->get($markdownPath);

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8')
        ->assertHeader('Link', '<'.url($htmlPath).'>; rel="canonical"');
})->with([
    'home' => ['/index.md', '/'],
    'about' => ['/about.md', '/about'],
    'projects' => ['/projects.md', '/projects'],
    'project' => ['/projects/helpdesk-copilot.md', '/projects/helpdesk-copilot'],
    'blog' => ['/blog.md', '/blog'],
    'post' => ['/blog/testing-ai.md', '/blog/testing-ai'],
]);

it('renders a project page as markdown', function () {
    $project = Project::factory()->create(['title' => 'Helpdesk Copilot', 'body' => 'The write-up.']);

    $content = $this->get(route('markdown.projects.show', $project))->getContent();

    expect($content)
        ->toStartWith('# Helpdesk Copilot')
        ->toEndWith("The write-up.\n");
});

it('includes the quick answers, skills and experience in the about page', function () {
    config()->set('portfolio.owner_name', 'Sam Rivera');
    About::factory()->create(['open_to_work' => true]);

    $content = $this->get('/about.md')->getContent();

    expect($content)
        ->toContain("## Quick answers\n\n### What does Sam Rivera do?")
        ->toContain("### Is Sam Rivera available for hire?\n\nYes. Sam Rivera is open to new opportunities.");
});

it('returns not found for markdown versions of unpublished or missing content', function (string $path) {
    Project::factory()->draft()->create(['slug' => 'draft-project']);
    Post::factory()->scheduled()->create(['slug' => 'scheduled-post']);

    $this->get($path)->assertNotFound();
})->with([
    'draft project' => '/projects/draft-project.md',
    'scheduled post' => '/blog/scheduled-post.md',
    'missing post' => '/blog/does-not-exist.md',
]);

it('serves markdown to clients that prefer it', function () {
    $post = Post::factory()->create(['title' => 'Testing AI']);

    $response = $this->get(route('blog.show', $post), ['Accept' => 'text/markdown, text/html;q=0.8']);

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8')
        ->assertHeader('Vary', 'Accept');
    expect($response->getContent())->toStartWith('# Testing AI');
    expect($response->headers->all('link'))->toContain('<'.route('blog.show', $post).'>; rel="canonical"');
});

it('returns not found to markdown clients for unpublished content', function () {
    $draft = Post::factory()->draft()->create();

    $this->get(route('blog.show', $draft), ['Accept' => 'text/markdown'])->assertNotFound();
});

it('serves HTML to browsers and advertises the markdown version and llms.txt', function () {
    $response = $this->get(route('projects.index'), ['Accept' => 'text/html,application/xhtml+xml,*/*;q=0.8']);

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/html; charset=utf-8')
        ->assertHeader('Vary', 'Accept')
        ->assertHeader('Link', '<'.route('markdown.projects.index').'>; rel="alternate"; type="text/markdown", <'.route('llms.index').'>; rel="describedby"; type="text/markdown"')
        ->assertSee('<link rel="alternate" type="text/markdown" href="'.route('markdown.projects.index').'">', false);
});

it('does not advertise a markdown version for pages without one', function () {
    $this->get(route('contact'))
        ->assertOk()
        ->assertHeaderMissing('Link')
        ->assertDontSee('type="text/markdown"', false);
});
