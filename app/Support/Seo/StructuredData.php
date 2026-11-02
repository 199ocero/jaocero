<?php

namespace App\Support\Seo;

use App\Models\About;
use App\Models\Experience;
use App\Models\Post;
use App\Models\Project;
use App\Models\Skill;
use App\Support\Highlight;

/**
 * Builds the schema.org JSON-LD graph. Every page describes the WebSite and its owner (Person),
 * and adds its own nodes (ProfilePage, BlogPosting, CollectionPage...) linked to them by @id.
 */
class StructuredData
{
    /**
     * The full JSON-LD document for a page.
     *
     * @param  list<array<string, mixed>>  $nodes
     * @return array<string, mixed>
     */
    public static function graph(array $nodes = []): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [self::website(), self::person(), ...$nodes],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => self::id('website'),
            'url' => route('home'),
            'name' => (string) config('portfolio.owner_name'),
            'description' => (string) config('portfolio.tagline'),
            'inLanguage' => str_replace('_', '-', app()->getLocale()),
            'publisher' => ['@id' => self::id('person')],
        ];
    }

    /**
     * The portfolio owner, built from the About profile, skills and current role.
     *
     * @return array<string, mixed>
     */
    public static function person(): array
    {
        return SeoCache::remember(SeoCache::PERSON, function (): array {
            $about = About::current();
            $currentRole = Experience::query()->whereNull('end_date')->latest('start_date')->first();

            return array_filter([
                '@type' => 'Person',
                '@id' => self::id('person'),
                'name' => (string) config('portfolio.owner_name'),
                'url' => route('home'),
                'description' => ucfirst(Highlight::plain((string) config('portfolio.headline'))),
                'jobTitle' => $about->headline,
                'image' => $about->avatarUrl(),
                'homeLocation' => $about->location ? ['@type' => 'Place', 'name' => $about->location] : null,
                'worksFor' => $currentRole ? ['@type' => 'Organization', 'name' => $currentRole->company] : null,
                'knowsAbout' => Skill::groupedByCategory()->flatten(1)->pluck('name')->all(),
                'sameAs' => array_values($about->socialLinks()),
            ]);
        });
    }

    /**
     * A profile page (home, about) whose main subject is the owner.
     *
     * @return array<string, mixed>
     */
    public static function profilePage(string $name, string $url): array
    {
        return [
            ...self::webPage('ProfilePage', $name, $url),
            'mainEntity' => ['@id' => self::id('person')],
            'dateModified' => About::current()->updated_at?->toIso8601String(),
        ];
    }

    /**
     * A listing page (projects, blog) with its items.
     *
     * @param  array<array{name: string, url: string}>  $items  In display order
     * @return array<string, mixed>
     */
    public static function collectionPage(string $name, string $description, string $url, array $items): array
    {
        $items = array_values($items);

        return [
            ...self::webPage('CollectionPage', $name, $url),
            'description' => $description,
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => array_map(fn (array $item, int $index): array => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'url' => $item['url'],
                ], $items, array_keys($items)),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function webPage(string $type, string $name, string $url): array
    {
        return [
            '@type' => $type,
            '@id' => $url.'#webpage',
            'url' => $url,
            'name' => $name,
            'isPartOf' => ['@id' => self::id('website')],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function blogPosting(Post $post): array
    {
        $url = route('blog.show', $post);

        return array_filter([
            '@type' => 'BlogPosting',
            '@id' => $url.'#article',
            'url' => $url,
            'mainEntityOfPage' => $url,
            'headline' => $post->seo_title ?: $post->title,
            'description' => $post->seo_description ?: $post->excerpt,
            'image' => $post->coverImageUrl(),
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'keywords' => $post->tags ?: null,
            'author' => ['@id' => self::id('person')],
            'publisher' => ['@id' => self::id('person')],
            'isPartOf' => ['@id' => self::id('website')],
        ]);
    }

    /**
     * Projects with a public repository are SoftwareSourceCode, the rest are CreativeWork.
     *
     * @return array<string, mixed>
     */
    public static function project(Project $project): array
    {
        $url = route('projects.show', $project);

        return array_filter([
            '@type' => $project->github_url ? 'SoftwareSourceCode' : 'CreativeWork',
            '@id' => $url.'#project',
            'url' => $url,
            'mainEntityOfPage' => $url,
            'name' => $project->title,
            'description' => $project->seo_description ?: $project->summary,
            'image' => $project->thumbnailUrl(),
            'codeRepository' => $project->github_url,
            'keywords' => $project->tech_stack ?: null,
            'datePublished' => $project->published_at?->toIso8601String(),
            'dateModified' => $project->updated_at?->toIso8601String(),
            'author' => ['@id' => self::id('person')],
        ]);
    }

    /**
     * @param  array<string, string>  $trail  Crumb name => URL, starting from the home page
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $trail): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(fn (int $index, int|string $name, string $url): array => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => (string) $name,
                'item' => $url,
            ], array_keys(array_keys($trail)), array_keys($trail), $trail),
        ];
    }

    /**
     * A stable node identifier, e.g. "https://example.com/#person".
     */
    private static function id(string $fragment): string
    {
        return route('home').'/#'.$fragment;
    }
}
