<?php

use App\Models\About;
use App\Models\Post;
use App\Models\Project;

it('follows the llms.txt format with a title, summary and link sections', function () {
    config()->set('portfolio.owner_name', 'Sam Rivera');
    config()->set('portfolio.headline', 'a Laravel developer who builds with *AI*');
    config()->set('portfolio.tagline', 'A portfolio you can talk to.');
    About::factory()->create(['bio' => 'I build calm software.', 'location' => 'Lisbon', 'open_to_work' => true]);
    $project = Project::factory()->create(['title' => 'Helpdesk Copilot', 'summary' => 'AI replies for support teams.']);
    $post = Post::factory()->create(['title' => 'Testing AI', 'excerpt' => 'Fakes keep tests fast.']);

    $response = $this->get('/llms.txt');

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
    expect($response->getContent())
        ->toStartWith("# Sam Rivera\n\n> A Laravel developer who builds with AI. A portfolio you can talk to.\n\nI build calm software. Location: Lisbon. Sam Rivera is open to new opportunities.")
        ->toContain("## About\n\n- [About Sam Rivera](".route('markdown.about').')')
        ->toContain("## Projects\n\n- [Helpdesk Copilot](".route('markdown.projects.show', $project).'): AI replies for support teams.')
        ->toContain("## Blog\n\n- [Testing AI](".route('markdown.blog.show', $post).'): Fakes keep tests fast.')
        ->toContain("## Optional\n\n- [Full content](".route('llms.full').')');
});

it('lists only published content', function () {
    $draftProject = Project::factory()->draft()->create();
    $scheduledPost = Post::factory()->scheduled()->create();

    $content = $this->get('/llms.txt')->getContent();

    expect($content)
        ->not->toContain($draftProject->title)
        ->not->toContain($scheduledPost->title);
});

it('moves posts beyond the latest ten to the optional section', function () {
    $oldestPost = Post::factory()->create(['title' => 'The oldest post', 'published_at' => now()->subYear()]);
    Post::factory()->count(10)->create(['published_at' => now()->subDay()]);

    $content = $this->get('/llms.txt')->getContent();

    expect(str($content)->after('## Optional')->toString())->toContain('[The oldest post]');
    expect(str($content)->between('## Blog', '## Optional')->toString())->not->toContain('The oldest post');
});

it('serves every published page in full in llms-full.txt', function () {
    About::factory()->create(['bio' => 'I build calm software.']);
    Project::factory()->create(['body' => 'The project write-up.']);
    Post::factory()->create(['body' => 'The full blog post body.']);
    Post::factory()->draft()->create(['body' => 'A secret draft.']);

    $response = $this->get('/llms-full.txt');

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
    expect($response->getContent())
        ->toContain('I build calm software.')
        ->toContain('The project write-up.')
        ->toContain('The full blog post body.')
        ->not->toContain('A secret draft.');
});
