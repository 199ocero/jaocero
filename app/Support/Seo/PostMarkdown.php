<?php

namespace App\Support\Seo;

use App\Models\Post;

/**
 * Renders a blog post as clean markdown for AI agents (chat tools, llms.txt, .md pages).
 */
class PostMarkdown
{
    public static function render(Post $post): string
    {
        $facts = array_filter([
            'Page' => route('blog.show', $post),
            'Author' => (string) config('portfolio.owner_name'),
            'Published' => $post->published_at?->toDateString(),
            'Updated' => $post->updated_at?->toDateString(),
            'Tags' => $post->tags ? implode(', ', $post->tags) : null,
        ]);

        return MarkdownDocument::make()
            ->heading($post->title)
            ->quote($post->excerpt)
            ->facts($facts)
            ->paragraph($post->body)
            ->toString();
    }
}
