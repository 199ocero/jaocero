<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Supports a tiny bit of markup in config text: wrap a phrase in *asterisks*
 * (e.g. PORTFOLIO_HEADLINE="a Laravel developer who builds with *AI*")
 * to draw a hand-drawn marker highlight behind it.
 */
class Highlight
{
    private const string PATTERN = '/\*([^*]+)\*/';

    /**
     * Escaped HTML with highlighted phrases wrapped in a marker span.
     */
    public static function html(?string $text, string $class = 'marker'): HtmlString
    {
        return new HtmlString((string) preg_replace(
            self::PATTERN,
            '<span class="'.e($class).'">$1</span>',
            e((string) $text),
        ));
    }

    /**
     * The text split into plain and highlighted parts, e.g. for word-by-word animations.
     *
     * @return list<array{text: string, highlighted: bool}>
     */
    public static function segments(?string $text): array
    {
        $parts = preg_split(self::PATTERN, (string) $text, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];
        $segments = [];

        foreach ($parts as $index => $part) {
            if (trim($part) !== '') {
                // With a capture group, odd indexes are the highlighted phrases.
                $segments[] = ['text' => trim($part), 'highlighted' => $index % 2 === 1];
            }
        }

        return $segments;
    }

    /**
     * Plain text with the highlight markers removed, for meta tags and AI prompts.
     */
    public static function plain(?string $text): string
    {
        return (string) preg_replace(self::PATTERN, '$1', (string) $text);
    }
}
