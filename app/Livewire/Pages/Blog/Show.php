<?php

namespace App\Livewire\Pages\Blog;

use App\Models\Post;
use App\Support\Seo\SeoData;
use App\Support\Seo\StructuredData;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Post $post;

    public function mount(Post $post): void
    {
        abort_unless($post->isPublished(), 404);

        $this->post = $post;
    }

    public function render(): View
    {
        return view('livewire.pages.blog.show')->layoutData([
            'seo' => new SeoData(
                title: $this->post->seo_title ?: $this->post->title,
                description: $this->post->seo_description ?: $this->post->excerpt,
                canonicalUrl: route('blog.show', $this->post),
                image: $this->post->coverImageUrl(),
                type: 'article',
                publishedAt: $this->post->published_at,
                modifiedAt: $this->post->updated_at,
                jsonLd: [
                    StructuredData::blogPosting($this->post),
                    StructuredData::breadcrumbs([
                        'Home' => route('home'),
                        'Blog' => route('blog.index'),
                        $this->post->title => route('blog.show', $this->post),
                    ]),
                ],
            ),
        ]);
    }
}
