<?php

use App\Support\Markdown;

it('converts markdown to HTML', function () {
    expect((string) Markdown::render('**bold** and [a link](https://example.com)'))
        ->toContain('<strong>bold</strong>')
        ->toContain('<a href="https://example.com">a link</a>');
});

it('escapes raw HTML instead of rendering it', function () {
    expect((string) Markdown::render('<img src=x onerror=alert(1)>'))
        ->not->toContain('<img')
        ->toContain('&lt;img');
});

it('strips unsafe link protocols', function () {
    expect((string) Markdown::render('[click](javascript:alert(1))'))
        ->not->toContain('javascript:');
});

it('renders an empty string for null input', function () {
    expect((string) Markdown::render(null))->toBe('');
});

it('shows an unfinished streamed link as its plain text', function (string $partial) {
    expect(trim((string) Markdown::renderPartial($partial)))->toBe('<p>See Helpdesk</p>');
})->with([
    'url still streaming' => 'See [Helpdesk](https://exam',
    'label closed, no url yet' => 'See [Helpdesk]',
    'label still streaming' => 'See [Helpdesk',
]);

it('renders finished links while streaming', function () {
    expect((string) Markdown::renderPartial('See [Helpdesk](https://example.com) and more'))
        ->toContain('<a href="https://example.com">Helpdesk</a>');
});
