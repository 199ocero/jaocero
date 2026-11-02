<?php

namespace App\Http\Controllers;

use App\Support\Seo\PageMarkdown;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * Serves the markdown version of a page, e.g. /about.md or /blog/{slug}.md.
 */
class MarkdownPageController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $route = $request->route();
        $page = Str::after((string) $route?->getName(), 'markdown.');
        $parameters = $route?->parameters() ?? [];

        return response(PageMarkdown::render($page, $parameters))->withHeaders([
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Link' => '<'.route($page, $parameters).'>; rel="canonical"',
        ]);
    }
}
