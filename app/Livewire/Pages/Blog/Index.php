<?php

namespace App\Livewire\Pages\Blog;

use App\Models\Post;
use App\Support\Seo\SeoData;
use App\Support\Seo\StructuredData;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    /**
     * Optional tag filter, e.g. /blog?tag=laravel.
     */
    #[Url]
    public string $tag = '';

    public function render(): View
    {
        $posts = Post::published()
            ->when($this->tag !== '', fn (Builder $query) => $query->whereJsonContains('tags', $this->tag))
            ->paginate(10);
        $description = 'Articles by '.config('portfolio.owner_name').' about building software, Laravel and AI.';

        return view('livewire.pages.blog.index', [
            'posts' => $posts,
        ])->layoutData([
            'seo' => new SeoData(
                title: 'Blog',
                description: $description,
                canonicalUrl: route('blog.index'),
                jsonLd: [StructuredData::collectionPage('Blog', $description, route('blog.index'), $posts
                    ->getCollection()
                    ->map(fn (Post $post): array => ['name' => $post->title, 'url' => route('blog.show', $post)])
                    ->all())],
            ),
        ]);
    }
}
