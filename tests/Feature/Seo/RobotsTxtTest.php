<?php

it('keeps crawlers out of the admin panel and points them to the sitemap', function () {
    $response = $this->get('/robots.txt');

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    expect($response->getContent())
        ->toContain("User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /livewire")
        ->toEndWith('Sitemap: '.route('sitemap')."\n");
});

it('always allows AI search bots', function () {
    config()->set('portfolio.seo.allow_ai_training', false);
    config()->set('portfolio.seo.ai_search_bots', ['OAI-SearchBot', 'Claude-SearchBot']);

    $content = $this->get('/robots.txt')->getContent();

    expect($content)->toContain("User-agent: OAI-SearchBot\nUser-agent: Claude-SearchBot\nAllow: /\nDisallow: /admin");
});

it('allows AI training bots when AI training is allowed', function () {
    config()->set('portfolio.seo.allow_ai_training', true);
    config()->set('portfolio.seo.ai_training_bots', ['GPTBot', 'ClaudeBot']);

    $content = $this->get('/robots.txt')->getContent();

    expect($content)->toContain("User-agent: GPTBot\nUser-agent: ClaudeBot\nAllow: /\nDisallow: /admin");
});

it('blocks AI training bots when AI training is not allowed', function () {
    config()->set('portfolio.seo.allow_ai_training', false);
    config()->set('portfolio.seo.ai_training_bots', ['GPTBot', 'ClaudeBot']);

    $content = $this->get('/robots.txt')->getContent();

    expect($content)->toContain("User-agent: GPTBot\nUser-agent: ClaudeBot\nDisallow: /\n");
});
