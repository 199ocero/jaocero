<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company' => fake()->company(),
            'role' => fake()->jobTitle(),
            'start_date' => $start = fake()->dateTimeBetween('-8 years', '-1 year'),
            'end_date' => fake()->dateTimeBetween($start, 'now'),
            'description' => fake()->paragraph(),
        ];
    }

    public function current(): static
    {
        return $this->state(fn (array $attributes) => ['end_date' => null]);
    }
}
