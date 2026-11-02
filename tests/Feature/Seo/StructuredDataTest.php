<?php

use App\Models\About;
use App\Models\Experience;
use App\Models\Post;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Testing\TestResponse;

/**
 * The page's JSON-LD graph nodes, keyed by @type.
 *
 * @return array<string, array<string, mixed>>
 */
function jsonLdNodes(TestResponse $response): array
{
    preg_match('#<script type="application/ld\+json">(.*?)</script>#s', (string) $response->getContent(), $matches);

    $graph = json_decode($matches[1] ?? '', true, flags: JSON_THROW_ON_ERROR)['@graph'];

    return collect($graph)->keyBy('@type')->all();
}

it('describes the site and its owner on every page', function () {
    config()->set('portfolio.owner_name', 'Sam Rivera');
    About::factory()->create(['github_url' => 'https://github.com/sam', 'linkedin_url' => null, 'x_url' => null, 'location' => 'Lisbon']);
    Skill::factory()->create(['name' => 'Laravel']);
    Experience::factory()->current()->create(['company' => 'Northwind Labs']);

    $nodes = jsonLdNodes($this->get(route('contact')));

    expect($nodes['WebSite'])->toMatchArray([
        '@id' => route('home').'/#website',
        'name' => 'Sam Rivera',
        'publisher' => ['@id' => route('home').'/#person'],
    ]);
    expect($nodes['Person'])->toMatchArray([
        '@id' => route('home').'/#person',
        'name' => 'Sam Rivera',
        'sameAs' => ['https://github.com/sam'],
        'knowsAbout' => ['Laravel'],
        'worksFor' => ['@type' => 'Organization', 'name' => 'Northwind Labs'],
        'homeLocation' => ['@type' => 'Place', 'name' => 'Lisbon'],
    ]);
    expect($nodes['ContactPage']['url'])->toBe(route('contact'));
});

it('marks the home page as a profile page about the owner', function () {
    $nodes = jsonLdNodes($this->get(route('home')));

    expect($nodes['ProfilePage'])->toMatchArray([
        '@id' => route('home').'#webpage',
        'mainEntity' => ['@id' => route('home').'/#person'],
    ]);
});

it('describes a blog post with its author, dates and breadcrumbs', function () {
    $post = Post::factory()->create([
        'title' => 'Testing AI',
        'published_at' => '2026-05-01 09:00:00',
    ]);

    $nodes = jsonLdNodes($this->get(route('blog.show', $post)));

    expect($nodes['BlogPosting'])->toMatchArray([
        'headline' => 'Testing AI',
        'url' => route('blog.show', $post),
        'datePublished' => '2026-05-01T09:00:00+00:00',
        'author' => ['@id' => route('home').'/#person'],
    ]);
    expect($nodes['BreadcrumbList']['itemListElement'])->toBe([
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => route('blog.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Testing AI', 'item' => route('blog.show', $post)],
    ]);
});

it('describes projects with a public repository as source code', function () {
    $project = Project::factory()->create(['github_url' => 'https://github.com/example/repo']);

    $nodes = jsonLdNodes($this->get(route('projects.show', $project)));

    expect($nodes['SoftwareSourceCode']['codeRepository'])->toBe('https://github.com/example/repo');
});

it('describes projects without a repository as creative work', function () {
    $project = Project::factory()->create(['github_url' => null]);

    $nodes = jsonLdNodes($this->get(route('projects.show', $project)));

    expect($nodes)->toHaveKey('CreativeWork')->not->toHaveKey('SoftwareSourceCode');
});

it('lists the visible projects on the projects page', function () {
    $laravel = Project::factory()->create(['tech_stack' => ['Laravel']]);
    Project::factory()->create(['tech_stack' => ['Vue']]);

    $nodes = jsonLdNodes($this->get(route('projects.index', ['tech' => 'Laravel'])));

    expect($nodes['CollectionPage']['mainEntity']['itemListElement'])->toBe([
        ['@type' => 'ListItem', 'position' => 1, 'name' => $laravel->title, 'url' => route('projects.show', $laravel)],
    ]);
});

it('cannot be broken out of by content containing a closing script tag', function () {
    $post = Post::factory()->create(['title' => '</script><script>alert(1)</script>']);

    $response = $this->get(route('blog.show', $post));

    $response->assertDontSee('</script><script>alert(1)', false);
    expect(jsonLdNodes($response)['BlogPosting']['headline'])->toBe('</script><script>alert(1)</script>');
});
