<?php

namespace App\Models\Concerns;

use App\Support\Seo\SeoCache;

/**
 * Regenerates llms.txt, the sitemap, the feed and structured data when this content changes.
 */
trait ClearsSeoCache
{
    public static function bootClearsSeoCache(): void
    {
        static::saved(fn () => SeoCache::flush());
        static::deleted(fn () => SeoCache::flush());
    }
}
