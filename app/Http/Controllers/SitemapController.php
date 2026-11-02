<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use App\Support\Seo\SeoCache;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Prepended here because Blade would read the "?>" in it as the end of a PHP block.
     */
    private const string XML_DECLARATION = '<?xml version="1.0" encoding="UTF-8"?>'."\n";

    public function __invoke(): Response
    {
        $xml = SeoCache::remember(SeoCache::SITEMAP, fn (): string => self::XML_DECLARATION.view('seo.sitemap', [
            'pages' => [route('home'), route('projects.index'), route('blog.index'), route('about'), route('contact')],
            'projects' => Project::published()->get(),
            'posts' => Post::published()->get(),
        ])->render());

        return response($xml)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
