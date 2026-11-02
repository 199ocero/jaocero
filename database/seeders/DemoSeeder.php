<?php

namespace Database\Seeders;

use App\Enums\SkillCategory;
use App\Models\About;
use App\Models\Experience;
use App\Models\Post;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Neutral sample content so the site looks complete in development and for forks.
 * Replace it with your own content through the admin panel at /admin.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAbout();
        $this->seedSkills();
        $this->seedExperience();
        $this->seedProjects();
        $this->seedPosts();
    }

    private function seedAbout(): void
    {
        About::query()->delete();

        About::create([
            'headline' => 'Laravel developer who builds with AI',
            'bio' => "I'm a full-stack developer focused on Laravel, Livewire and FilamentPHP. I enjoy turning messy business processes into calm, well-tested software.\n\nLately I've been building AI features into everyday apps: assistants that answer questions from real data, smart search, and automations that save teams hours every week.",
            'location' => 'Remote',
            'github_url' => 'https://github.com/example',
            'linkedin_url' => 'https://www.linkedin.com/in/example',
            'x_url' => 'https://x.com/example',
            'email' => 'hello@example.com',
            'open_to_work' => true,
        ]);
    }

    private function seedSkills(): void
    {
        $skillsByCategory = [
            SkillCategory::Backend->value => ['PHP' => 95, 'Laravel' => 95, 'REST APIs' => 90, 'Queues & Jobs' => 85],
            SkillCategory::Frontend->value => ['Livewire' => 92, 'Alpine.js' => 88, 'Tailwind CSS' => 90, 'Vue' => 70],
            SkillCategory::AI->value => ['Laravel AI SDK' => 85, 'Prompt design' => 80, 'RAG & embeddings' => 75],
            SkillCategory::Database->value => ['MySQL' => 88, 'PostgreSQL' => 80, 'Redis' => 78],
            SkillCategory::DevOps->value => ['Laravel Cloud' => 80, 'GitHub Actions' => 82, 'Docker' => 70],
            SkillCategory::Testing->value => ['Pest' => 90],
        ];

        foreach ($skillsByCategory as $category => $skills) {
            $order = 0;

            foreach ($skills as $name => $proficiency) {
                Skill::updateOrCreate(
                    ['name' => $name],
                    ['category' => $category, 'proficiency' => $proficiency, 'sort_order' => $order++],
                );
            }
        }
    }

    private function seedExperience(): void
    {
        $jobs = [
            [
                'company' => 'Northwind Labs',
                'role' => 'Senior Laravel Developer',
                'start_date' => now()->subYears(2)->startOfMonth(),
                'end_date' => null,
                'description' => 'Lead developer on a multi-tenant SaaS platform built with Laravel, Livewire and FilamentPHP. Shipped an AI support assistant that resolves a third of incoming tickets.',
            ],
            [
                'company' => 'Brightline Studio',
                'role' => 'Full-Stack Developer',
                'start_date' => now()->subYears(5)->startOfMonth(),
                'end_date' => now()->subYears(2)->subMonth()->startOfMonth(),
                'description' => 'Built client web apps and internal admin panels. Introduced automated testing with Pest and CI pipelines across all projects.',
            ],
            [
                'company' => 'Freelance',
                'role' => 'Web Developer',
                'start_date' => now()->subYears(7)->startOfMonth(),
                'end_date' => now()->subYears(5)->subMonth()->startOfMonth(),
                'description' => 'Delivered websites and small business tools for local companies, from first sketch to deployment.',
            ],
        ];

        foreach ($jobs as $job) {
            Experience::updateOrCreate(['company' => $job['company'], 'role' => $job['role']], $job);
        }
    }

    private function seedProjects(): void
    {
        $projects = [
            [
                'title' => 'Helpdesk Copilot',
                'summary' => 'An AI assistant that drafts replies to support tickets using the team\'s own knowledge base.',
                'tech_stack' => ['Laravel', 'Laravel AI SDK', 'Livewire', 'PostgreSQL'],
                'is_featured' => true,
            ],
            [
                'title' => 'Invoice Flow',
                'summary' => 'A FilamentPHP back office for freelancers to track clients, time and recurring invoices.',
                'tech_stack' => ['Laravel', 'FilamentPHP', 'MySQL', 'Pest'],
                'is_featured' => true,
            ],
            [
                'title' => 'Open Recipes',
                'summary' => 'A community recipe site with semantic search powered by embeddings.',
                'tech_stack' => ['Laravel', 'Livewire', 'Alpine.js', 'Tailwind CSS'],
                'is_featured' => true,
            ],
            [
                'title' => 'Status Pulse',
                'summary' => 'A lightweight uptime monitor that pings endpoints and posts incidents to Slack.',
                'tech_stack' => ['Laravel', 'Queues', 'Redis'],
                'is_featured' => false,
            ],
            [
                'title' => 'Markdown Notes',
                'summary' => 'A fast, keyboard-first notes app with markdown, tags and full-text search.',
                'tech_stack' => ['Livewire', 'Alpine.js', 'SQLite'],
                'is_featured' => false,
            ],
        ];

        foreach ($projects as $index => $project) {
            $slug = Str::slug($project['title']);

            Project::updateOrCreate(['slug' => $slug], [
                ...$project,
                'slug' => $slug,
                'body' => $this->projectBody($project['title'], $project['summary']),
                'github_url' => "https://github.com/example/{$slug}",
                'live_url' => $project['is_featured'] ? "https://{$slug}.example.com" : null,
                'sort_order' => $index,
                'published_at' => now()->subMonths($index + 1),
            ]);
        }
    }

    private function seedPosts(): void
    {
        $posts = [
            ['title' => 'Building a Portfolio You Can Talk To', 'tags' => ['ai', 'laravel'], 'excerpt' => 'How I wired the Laravel AI SDK, Livewire streaming and FilamentPHP into a portfolio that answers questions about my work.'],
            ['title' => 'Testing AI Features Without Calling the API', 'tags' => ['testing', 'ai'], 'excerpt' => 'Using agent fakes in Pest to keep AI features fast, deterministic and free to test.'],
            ['title' => 'Five FilamentPHP Tips for Cleaner Admin Panels', 'tags' => ['filament', 'laravel'], 'excerpt' => 'Small habits that keep Filament resources readable as an app grows.'],
            ['title' => 'Streaming Responses with Livewire', 'tags' => ['livewire'], 'excerpt' => 'A practical look at wire:stream for chat-style interfaces.'],
        ];

        foreach ($posts as $index => $post) {
            $slug = Str::slug($post['title']);

            Post::updateOrCreate(['slug' => $slug], [
                ...$post,
                'slug' => $slug,
                'body' => $this->postBody($post['excerpt']),
                'published_at' => now()->subWeeks(($index * 3) + 1),
            ]);
        }
    }

    private function projectBody(string $title, string $summary): string
    {
        return <<<MARKDOWN
        {$summary}

        ## The problem

        Teams were losing time on repetitive work that software could handle. {$title} started as a weekend experiment to fix that.

        ## What I built

        - A clean Laravel backend with well-tested domain logic
        - A fast, reactive interface built with Livewire and Alpine.js
        - An admin panel for managing data without touching the database

        ## Results

        The first version shipped in a few weeks and is now used daily. Every feature is covered by Pest tests and deployed automatically through CI.
        MARKDOWN;
    }

    private function postBody(string $excerpt): string
    {
        return <<<MARKDOWN
        {$excerpt}

        ## Why this matters

        Good tools disappear into the background. The goal is always to make the next change easier than the last one.

        ## The approach

        1. Start with the smallest version that works.
        2. Cover it with tests before it grows.
        3. Refactor once the shape of the problem is clear.

        ```php
        it('works', function () {
            expect(true)->toBeTrue();
        });
        ```

        ## Wrapping up

        Keep things simple, measure what matters, and ship often.
        MARKDOWN;
    }
}
