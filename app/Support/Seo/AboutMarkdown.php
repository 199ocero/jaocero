<?php

namespace App\Support\Seo;

use App\Models\About;
use App\Support\Highlight;

/**
 * Renders the owner's profile as clean markdown for AI agents (chat tools, llms.txt, .md pages).
 */
class AboutMarkdown
{
    public static function render(About $about): string
    {
        $facts = array_filter([
            'Headline' => $about->headline ?: Highlight::plain((string) config('portfolio.headline')),
            'Location' => $about->location,
            'Availability' => $about->open_to_work ? 'Open to work' : 'Not currently looking for work',
            'Email' => $about->email,
            ...$about->socialLinks(),
            'Resume' => $about->resumeUrl(),
            'Contact page' => route('contact'),
        ]);

        return MarkdownDocument::make()
            ->heading((string) config('portfolio.owner_name'))
            ->quote((string) config('portfolio.tagline'))
            ->facts($facts)
            ->paragraph(implode("\n\n", $about->bioParagraphs()))
            ->toString();
    }
}
