<?php

use App\Models\SiteSetting;
use App\Support\Seo\SeoCache;
use Illuminate\Support\Facades\Cache;

it('creates the settings record from the config defaults on first access and reuses it afterwards', function () {
    config()->set('portfolio.owner_name', 'Sam Rivera');

    $first = SiteSetting::current();
    $second = SiteSetting::current();

    expect($second->is($first))->toBeTrue()
        ->and(SiteSetting::count())->toBe(1)
        ->and($first->owner_name)->toBe('Sam Rivera');
});

it('overrides the config with the saved settings', function () {
    SiteSetting::factory()->create([
        'owner_name' => 'Sam Rivera',
        'chat_max_turns' => 3,
        'allow_ai_training' => false,
    ]);

    expect(config('portfolio.owner_name'))->toBe('Sam Rivera')
        ->and(config('portfolio.chat.max_turns'))->toBe(3)
        ->and(config('portfolio.seo.allow_ai_training'))->toBeFalse();
});

it('keeps the config default for a blank required setting', function () {
    config()->set('portfolio.owner_name', 'Sam Rivera');

    SiteSetting::factory()->create(['owner_name' => null]);

    expect(config('portfolio.owner_name'))->toBe('Sam Rivera');
});

it('turns off optional settings that are left blank', function () {
    config()->set('portfolio.source_url', 'https://github.com/example/portfolio');

    SiteSetting::factory()->create(['source_url' => null, 'chat_subtitle' => null, 'widget_note' => null]);

    expect(config('portfolio.source_url'))->toBeNull()
        ->and(config('portfolio.chat.subtitle'))->toBeNull()
        ->and(config('portfolio.chat_widget.note'))->toBeNull();
});

it('clears the SEO cache when the settings change', function () {
    Cache::put(SeoCache::LLMS_TXT, 'stale');

    SiteSetting::current()->update(['owner_name' => 'Sam Rivera']);

    expect(Cache::has(SeoCache::LLMS_TXT))->toBeFalse();
});
