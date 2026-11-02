<?php

namespace App\Livewire\Pages;

use App\Models\About as AboutProfile;
use App\Models\Experience;
use App\Models\Skill;
use App\Support\Seo\QuickAnswers;
use App\Support\Seo\SeoData;
use App\Support\Seo\StructuredData;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class About extends Component
{
    public function render(): View
    {
        $about = AboutProfile::current();

        return view('livewire.pages.about', [
            'about' => $about,
            'quickAnswers' => QuickAnswers::all(),
            'skillsByCategory' => Skill::groupedByCategory(),
            'experiences' => Experience::latestFirst()->get(),
        ])->layoutData([
            'seo' => new SeoData(
                title: 'About',
                description: $about->bioParagraphs()[0] ?? 'About '.config('portfolio.owner_name').': skills, experience and how to get in touch.',
                canonicalUrl: route('about'),
                image: $about->avatarUrl(),
                type: 'profile',
                jsonLd: [StructuredData::profilePage('About '.config('portfolio.owner_name'), route('about'))],
            ),
        ]);
    }
}
