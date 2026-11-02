<?php

namespace App\Support\Seo;

use Carbon\CarbonInterface;

/**
 * Everything the layout needs to render a page's meta, Open Graph, Twitter and JSON-LD tags.
 */
final readonly class SeoData
{
    /**
     * @param  'website'|'article'|'profile'  $type
     * @param  list<array<string, mixed>>  $jsonLd  Page-specific schema.org nodes, added to the WebSite and Person graph
     */
    public function __construct(
        public string $title,
        public string $description,
        public ?string $canonicalUrl = null,
        public ?string $image = null,
        public string $type = 'website',
        public ?CarbonInterface $publishedAt = null,
        public ?CarbonInterface $modifiedAt = null,
        public array $jsonLd = [],
    ) {}

    /**
     * Page title with the owner's name appended, e.g. "Projects · Alex Morgan".
     */
    public function fullTitle(): string
    {
        $ownerName = (string) config('portfolio.owner_name');

        return $this->title === $ownerName ? $this->title : "{$this->title} · {$ownerName}";
    }

    public function canonicalUrl(): string
    {
        return $this->canonicalUrl ?? url()->current();
    }
}
