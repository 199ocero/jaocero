<?php

namespace App\Livewire\Pages;

use App\Enums\StickerColor;
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
            'stickers' => $this->stickers(),
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

    /**
     * The stickers floating around the chat: the ones picked in Site settings, or else the first four skills.
     *
     * @return list<array{text: string, color: string}>
     */
    private function stickers(): array
    {
        if (! config('portfolio.hero.stickers.enabled')) {
            return [];
        }

        $texts = collect((array) config('portfolio.hero.stickers.items'))
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['text'] ?? null))
            ->values();

        $stickers = $texts->isNotEmpty()
            ? $texts->map(fn (array $item, int $position): array => [
                'text' => (string) $item['text'],
                'color' => (StickerColor::tryFrom((string) ($item['color'] ?? '')) ?? StickerColor::forPosition($position))->value,
            ])
            : Skill::ordered()->limit(4)->pluck('name')->map(fn (string $name, int $position): array => [
                'text' => $name,
                'color' => StickerColor::forPosition($position)->value,
            ]);

        return array_values($stickers->take(4)->all());
    }
}
