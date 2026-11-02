<?php

namespace App\View\Components;

use App\Models\About;
use App\Support\Seo\PageMarkdown;
use App\Support\Seo\SeoData;
use App\Support\Seo\StructuredData;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Renders the title, meta description, canonical URL, Open Graph, Twitter and JSON-LD tags,
 * plus links to the page's markdown version, llms.txt and the feed.
 */
class Seo extends Component
{
    public function __construct(public SeoData $seo) {}

    /**
     * The page image, falling back to the owner's share image, then the bundled default.
     */
    public function imageUrl(): string
    {
        $image = $this->seo->image
            ?? About::current()->ogImageUrl()
            ?? (string) config('portfolio.seo.default_og_image');

        return url($image);
    }

    /**
     * The JSON-LD graph, encoded so it is safe to place inside a script tag.
     */
    public function jsonLd(): string
    {
        return (string) json_encode(
            StructuredData::graph($this->seo->jsonLd),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR,
        );
    }

    /**
     * The markdown version of the current page, when it has one.
     */
    public function markdownUrl(): ?string
    {
        $route = request()->route();

        return PageMarkdown::url($route?->getName(), $route?->parameters() ?? []);
    }

    public function render(): View
    {
        return view('components.seo');
    }
}
