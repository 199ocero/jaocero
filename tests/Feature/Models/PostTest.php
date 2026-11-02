<?php

use App\Models\Post;

it('returns only published posts, newest first', function () {
    $older = Post::factory()->create(['published_at' => now()->subMonth()]);
    $newer = Post::factory()->create(['published_at' => now()->subDay()]);
    Post::factory()->draft()->create();
    Post::factory()->scheduled()->create();

    expect(Post::published()->pluck('id')->all())->toBe([$newer->id, $older->id]);
});

it('estimates reading time at roughly 200 words per minute', function (int $words, int $minutes) {
    $post = Post::factory()->make(['body' => str_repeat('word ', $words)]);

    expect($post->readingTimeInMinutes())->toBe($minutes);
})->with([
    'never less than a minute' => [10, 1],
    'exactly two minutes' => [400, 2],
    'rounds up' => [401, 3],
]);
