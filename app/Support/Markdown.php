<?php

namespace App\Support;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * The single place markdown is turned into HTML.
 *
 * Raw HTML in the source is escaped and unsafe links (javascript:, data:...)
 * are stripped, so content from the admin panel or the AI can't inject markup.
 */
class Markdown
{
    public static function render(?string $markdown): HtmlString
    {
        return new HtmlString(Str::markdown((string) $markdown, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 20,
        ]));
    }
}
