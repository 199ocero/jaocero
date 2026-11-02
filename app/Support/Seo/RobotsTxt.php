<?php

namespace App\Support\Seo;

/**
 * Builds /robots.txt from config('portfolio.seo').
 *
 * AI search and assistant bots are always allowed, because that is how the site gets
 * cited and linked in AI answers. AI training bots follow PORTFOLIO_ALLOW_AI_TRAINING.
 */
class RobotsTxt
{
    /**
     * Paths no crawler needs: the admin panel and Livewire's endpoints.
     */
    private const array PRIVATE_PATHS = ['/admin', '/livewire'];

    public static function render(): string
    {
        $allowTraining = (bool) config('portfolio.seo.allow_ai_training');

        $groups = [
            self::group(['*']),
            self::group(
                self::bots('ai_search_bots'),
                comment: 'AI search and assistant bots: allowed so this site can be cited in AI answers.',
            ),
            self::group(
                self::bots('ai_training_bots'),
                allow: $allowTraining,
                comment: $allowTraining
                    ? 'AI training bots: allowed (PORTFOLIO_ALLOW_AI_TRAINING=true).'
                    : 'AI training bots: blocked (PORTFOLIO_ALLOW_AI_TRAINING=false).',
            ),
        ];

        return implode("\n\n", array_filter($groups))."\n\nSitemap: ".route('sitemap')."\n";
    }

    /**
     * A group of user agents with the same rules. Crawlers only follow the most specific
     * group that names them, so each group repeats the private paths.
     *
     * @param  list<string>  $userAgents
     */
    private static function group(array $userAgents, bool $allow = true, ?string $comment = null): ?string
    {
        if ($userAgents === []) {
            return null;
        }

        $lines = array_map(fn (string $userAgent): string => "User-agent: {$userAgent}", $userAgents);

        if ($comment !== null) {
            array_unshift($lines, "# {$comment}");
        }

        if (! $allow) {
            return implode("\n", [...$lines, 'Disallow: /']);
        }

        return implode("\n", [
            ...$lines,
            'Allow: /',
            ...array_map(fn (string $path): string => "Disallow: {$path}", self::PRIVATE_PATHS),
        ]);
    }

    /**
     * @return list<string>
     */
    private static function bots(string $configKey): array
    {
        return array_values(array_filter((array) config("portfolio.seo.{$configKey}"), 'is_string'));
    }
}
