<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Concerns\ReturnsJson;
use App\Models\Post;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetBlogPosts implements Tool
{
    use ReturnsJson;

    public const int DEFAULT_LIMIT = 5;

    public const int MAX_LIMIT = 10;

    public function description(): Stringable|string
    {
        return "List the owner's latest published blog posts, newest first, with excerpt, tags, date and page URL.";
    }

    public function handle(Request $request): Stringable|string
    {
        $limit = max(1, min(self::MAX_LIMIT, (int) $request->integer('limit', self::DEFAULT_LIMIT)));

        $posts = Post::published()->limit($limit)->get();

        if ($posts->isEmpty()) {
            return 'There are no published blog posts yet.';
        }

        return $this->json($posts->map(fn (Post $post): array => [
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'published' => $post->published_at?->toDateString(),
            'tags' => $post->tags ?? [],
            'reading_time_minutes' => $post->readingTimeInMinutes(),
            'url' => route('blog.show', $post),
        ])->all());
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'limit' => $schema->integer()
                ->description('How many posts to return (1 to '.self::MAX_LIMIT.', default '.self::DEFAULT_LIMIT.').')
                ->min(1)
                ->max(self::MAX_LIMIT),
        ];
    }
}
