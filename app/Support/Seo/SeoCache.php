<?php

namespace App\Support\Seo;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Caches generated SEO documents (llms.txt, sitemap, feed, structured data).
 *
 * Entries are cleared whenever portfolio content changes (see ClearsSeoCache) and
 * expire after an hour, so scheduled posts and projects appear once they go live.
 */
class SeoCache
{
    public const string LLMS_TXT = 'seo.llms-txt';

    public const string LLMS_FULL_TXT = 'seo.llms-full-txt';

    public const string SITEMAP = 'seo.sitemap';

    public const string FEED = 'seo.feed';

    public const string PERSON = 'seo.person';

    private const array KEYS = [self::LLMS_TXT, self::LLMS_FULL_TXT, self::SITEMAP, self::FEED, self::PERSON];

    /**
     * @template TValue
     *
     * @param  Closure(): TValue  $callback
     * @return TValue
     */
    public static function remember(string $key, Closure $callback): mixed
    {
        return Cache::remember($key, now()->addHour(), $callback);
    }

    public static function flush(): void
    {
        foreach (self::KEYS as $key) {
            Cache::forget($key);
        }
    }
}
