<?php

namespace Database\Factories;

use App\Models\About;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<About>
 */
class AboutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'headline' => 'Laravel developer who builds with AI',
            'bio' => fake()->paragraphs(2, true),
            'location' => fake()->city().', '.fake()->country(),
            'github_url' => 'https://github.com/example',
            'linkedin_url' => 'https://www.linkedin.com/in/example',
            'x_url' => 'https://x.com/example',
            'email' => 'hello@example.com',
            'open_to_work' => true,
        ];
    }
}
