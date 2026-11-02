<?php

use App\Ai\Tools\GetBlogPosts;
use App\Models\Post;
use Laravel\Ai\Tools\Request;

it('lists published posts newest first with their URLs', function () {
    $older = Post::factory()->create(['published_at' => now()->subMonth()]);
    $newer = Post::factory()->create(['published_at' => now()->subDay()]);
    Post::factory()->draft()->create();

    $result = json_decode((new GetBlogPosts)->handle(new Request), true);

    expect(array_column($result, 'url'))->toBe([route('blog.show', $newer), route('blog.show', $older)]);
});

it('returns five posts by default', function () {
    Post::factory()->count(7)->create();

    expect(json_decode((new GetBlogPosts)->handle(new Request), true))->toHaveCount(5);
});

it('keeps the limit between 1 and 10', function (int $requested, int $expected) {
    Post::factory()->count(12)->create();

    $result = json_decode((new GetBlogPosts)->handle(new Request(['limit' => $requested])), true);

    expect($result)->toHaveCount($expected);
})->with([
    'within range' => [3, 3],
    'too many' => [50, 10],
    'zero' => [0, 1],
]);

it('says so when there are no posts', function () {
    expect((string) (new GetBlogPosts)->handle(new Request))->toBe('There are no published blog posts yet.');
});
