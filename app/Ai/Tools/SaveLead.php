<?php

namespace App\Ai\Tools;

use App\Enums\MessageSource;
use App\Models\Message;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SaveLead implements Tool
{
    public function description(): Stringable|string
    {
        return 'Save a message for the owner from a visitor who wants to hire or contact them. '
            .'Only call this after the visitor has explicitly given their name, email and message in the conversation. Never guess or invent these details.';
    }

    public function handle(Request $request): Stringable|string
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Message::create([
            ...$validated,
            'company' => ($validated['company'] ?? null) ?: null,
            'source' => MessageSource::Chatbot,
        ]);

        return 'The message was saved. Thank the visitor and let them know the owner will reply by email.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->description("The visitor's name.")->required(),
            'email' => $schema->string()->description("The visitor's email address.")->required(),
            'company' => $schema->string()->description("The visitor's company, if they mentioned one."),
            'message' => $schema->string()->description('What the visitor wants to tell or ask the owner, in their own words.')->required(),
        ];
    }
}
