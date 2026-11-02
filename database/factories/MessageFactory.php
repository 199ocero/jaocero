<?php

namespace Database\Factories;

use App\Enums\MessageSource;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'company' => fake()->optional()->company(),
            'message' => fake()->paragraph(),
            'source' => MessageSource::ContactForm,
        ];
    }

    public function fromChatbot(): static
    {
        return $this->state(fn (array $attributes) => ['source' => MessageSource::Chatbot]);
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => ['read_at' => now()]);
    }
}
