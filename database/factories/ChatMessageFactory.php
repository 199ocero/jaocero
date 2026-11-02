<?php

namespace Database\Factories;

use App\Enums\ChatRole;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chat_conversation_id' => ChatConversation::factory(),
            'role' => ChatRole::User,
            'content' => fake()->sentence(),
        ];
    }

    public function fromAssistant(): static
    {
        return $this->state(fn (array $attributes) => ['role' => ChatRole::Assistant]);
    }
}
