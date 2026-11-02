<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $title = Str::title(rtrim(fake()->unique()->sentence(5), '.')),
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(20),
            'body' => implode("\n\n", ['## '.fake()->sentence(4), fake()->paragraph(), fake()->paragraph(), fake()->paragraph()]),
            'cover_image' => null,
            'tags' => fake()->randomElements(['laravel', 'livewire', 'ai', 'testing', 'devops', 'php'], 2),
            'published_at' => now()->subDays(fake()->numberBetween(1, 365)),
        ];
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
