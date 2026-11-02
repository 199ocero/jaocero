<?php

use App\Support\Highlight;

it('wraps phrases in asterisks with a marker span', function () {
    expect((string) Highlight::html('builds with *AI*'))
        ->toBe('builds with <span class="marker">AI</span>');
});

it('escapes HTML before adding the highlight markup', function () {
    expect((string) Highlight::html('<b>bold</b> *and* safe'))
        ->toBe('&lt;b&gt;bold&lt;/b&gt; <span class="marker">and</span> safe');
});

it('removes the asterisks for plain-text contexts', function () {
    expect(Highlight::plain('builds with *AI* and *care*'))->toBe('builds with AI and care');
});

it('leaves text without asterisks untouched', function () {
    expect(Highlight::plain('a Laravel developer'))->toBe('a Laravel developer')
        ->and((string) Highlight::html('a Laravel developer'))->toBe('a Laravel developer');
});

it('splits text into plain and highlighted segments', function () {
    expect(Highlight::segments('builds with *AI* and *care* daily'))->toBe([
        ['text' => 'builds with', 'highlighted' => false],
        ['text' => 'AI', 'highlighted' => true],
        ['text' => 'and', 'highlighted' => false],
        ['text' => 'care', 'highlighted' => true],
        ['text' => 'daily', 'highlighted' => false],
    ]);
});

it('marks the first occurrence of a phrase, keeping its original casing', function () {
    expect(Highlight::mark('A developer who builds with AI and ai tools', 'ai'))
        ->toBe('A developer who builds with *AI* and ai tools');
});

it('leaves the text alone when the phrase is blank, missing or the text marks its own', function (?string $phrase, string $text) {
    expect(Highlight::mark($text, $phrase))->toBe($text);
})->with([
    'blank phrase' => [null, 'builds with AI'],
    'missing phrase' => ['Rust', 'builds with AI'],
    'already marked' => ['builds', 'builds with *AI*'],
]);
