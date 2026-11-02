<?php

namespace App\Livewire\Pages;

use App\Models\About;
use App\Models\Post;
use App\Models\Project;
use App\Models\Skill;
use App\Support\Seo\SeoData;
use App\Support\Seo\StructuredData;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Home extends Component
{
    public function render(): View
    {
        return view('livewire.pages.home', [
            'about' => About::current(),
            'featuredProjects' => Project::published()->featured()->limit(3)->get(),
            'latestPosts' => Post::published()->limit(3)->get(),
            'stickers' => Skill::ordered()->limit(4)->pluck('name')->all(),
        ])->layoutData([
            'seo' => new SeoData(
                title: (string) config('portfolio.owner_name'),
                description: (string) config('portfolio.tagline'),
                canonicalUrl: route('home'),
                type: 'profile',
                jsonLd: [StructuredData::profilePage((string) config('portfolio.owner_name'), route('home'))],
            ),
        ]);
    }
}
