<?php

namespace App\Http\Middleware;

use App\Support\Seo\PageMarkdown;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Content negotiation for AI agents.
 *
 * Requests that prefer `Accept: text/markdown` (e.g. coding agents) get the page's markdown
 * version instead of HTML. Every response advertises the markdown version and /llms.txt
 * in `Link` headers, and varies on `Accept` so caches keep the two versions apart.
 */
class ServeMarkdownToAgents
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $page = $route?->getName();
        $parameters = $route?->parameters() ?? [];
        $markdownUrl = PageMarkdown::url($page, $parameters);

        if ($markdownUrl === null || ! $request->isMethodSafe()) {
            return $next($request);
        }

        $response = $request->prefers(['text/html', 'text/markdown']) === 'text/markdown'
            ? response(PageMarkdown::render((string) $page, $parameters))->withHeaders([
                'Content-Type' => 'text/markdown; charset=UTF-8',
                'Link' => '<'.$request->url().'>; rel="canonical"',
            ])
            : $next($request);

        $response->headers->set('Vary', 'Accept', replace: false);
        $response->headers->set('Link', implode(', ', [
            '<'.$markdownUrl.'>; rel="alternate"; type="text/markdown"',
            '<'.route('llms.index').'>; rel="describedby"; type="text/markdown"',
        ]), replace: false);

        return $response;
    }
}
