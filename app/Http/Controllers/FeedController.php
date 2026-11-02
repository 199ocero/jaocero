<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Support\Seo\SeoCache;
use Illuminate\Http\Response;

/**
 * An Atom feed of the latest blog posts.
 */
class FeedController extends Controller
{
    /**
     * Prepended here because Blade would read the "?>" in it as the end of a PHP block.
     */
    private const string XML_DECLARATION = '<?xml version="1.0" encoding="UTF-8"?>'."\n";

    public function __invoke(): Response
    {
        $xml = SeoCache::remember(SeoCache::FEED, fn (): string => self::XML_DECLARATION.view('seo.feed', [
            'posts' => Post::published()->limit(20)->get(),
        ])->render());

        return response($xml)->header('Content-Type', 'application/atom+xml; charset=UTF-8');
    }
}
