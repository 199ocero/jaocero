<?php

namespace App\Support\Seo;

use App\Models\About;
use App\Models\Post;
use App\Models\Project;
use App\Support\Highlight;

/**
 * Builds /llms.txt (an index of the site for LLMs, following the llmstxt.org format)
 * and /llms-full.txt (all portfolio content in a single markdown document).
 */
class LlmsTxt
{
    /**
     * How many of the latest posts are listed under "Blog". Older ones go under "Optional".
     */
    private const int LATEST_POSTS = 10;

    public static function index(): string
    {
        return SeoCache::remember(SeoCache::LLMS_TXT, function (): string {
            $about = About::current();
            $posts = Post::published()->get();

            return MarkdownDocument::make()
                ->heading((string) config('portfolio.owner_name'))
                ->quote(ucfirst(Highlight::plain((string) config('portfolio.headline'))).'. '.config('portfolio.tagline'))
                ->paragraph(self::summary($about))
                ->section('About', implode("\n", [
                    '- '.MarkdownDocument::link('About '.config('portfolio.owner_name'), route('markdown.about'), 'Bio, quick answers, skills and work experience'),
                    '- '.MarkdownDocument::link('Contact', route('contact'), 'Contact form for work enquiries'),
                ]))
                ->section('Projects', PageMarkdown::projectLinks(Project::published()->get()))
                ->section('Blog', PageMarkdown::postLinks($posts->take(self::LATEST_POSTS)))
                ->section('Optional', implode("\n", array_filter([
                    PageMarkdown::postLinks($posts->skip(self::LATEST_POSTS)),
                    '- '.MarkdownDocument::link('Full content', route('llms.full'), 'Every page of this site in one markdown file'),
                    $about->resumeUrl() ? '- '.MarkdownDocument::link('Resume', $about->resumeUrl()) : null,
                    ...array_map(
                        fn (string $network, string $url): string => '- '.MarkdownDocument::link($network, $url),
                        array_keys($about->socialLinks()),
                        $about->socialLinks(),
                    ),
                ])))
                ->toString();
        });
    }

    public static function full(): string
    {
        return SeoCache::remember(SeoCache::LLMS_FULL_TXT, fn (): string => implode("\n---\n\n", [
            PageMarkdown::about(),
            ...Project::published()->get()->map(fn (Project $project): string => ProjectMarkdown::render($project)),
            ...Post::published()->get()->map(fn (Post $post): string => PostMarkdown::render($post)),
        ]));
    }

    /**
     * A plain-text paragraph answering who the owner is and how to reach them.
     */
    private static function summary(About $about): string
    {
        $name = (string) config('portfolio.owner_name');
        $contactUrl = route('contact');
        $homeUrl = route('home');

        return implode(' ', array_filter([
            $about->bioParagraphs()[0] ?? null,
            $about->location ? "Location: {$about->location}." : null,
            $about->open_to_work ? "{$name} is open to new opportunities." : null,
            "To get in touch, use the contact form ({$contactUrl}) or ask the AI assistant on the home page ({$homeUrl}).",
        ]));
    }
}
