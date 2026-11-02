<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $title = Str::title(rtrim(fake()->unique()->sentence(3), '.')),
            'slug' => Str::slug($title),
            'summary' => fake()->sentence(14),
            'body' => fake()->paragraphs(4, true),
            'tech_stack' => fake()->randomElements(['Laravel', 'Livewire', 'Alpine.js', 'Tailwind CSS', 'FilamentPHP', 'MySQL', 'Redis', 'Vue', 'Pest'], 4),
            'github_url' => 'https://github.com/example/'.Str::slug($title),
            'live_url' => null,
            'thumbnail' => null,
            'is_featured' => false,
            'sort_order' => 0,
            'published_at' => now()->subDays(fake()->numberBetween(1, 365)),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['is_featured' => true]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['published_at' => null]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => ['published_at' => now()->addWeek()]);
    }
}
