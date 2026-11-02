<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Concerns\ReturnsJson;
use App\Models\About;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetContactInfo implements Tool
{
    use ReturnsJson;

    public function description(): Stringable|string
    {
        return 'Get the ways to contact the owner: email, social profiles, the contact page URL, and whether they are open to work.';
    }

    public function handle(Request $request): Stringable|string
    {
        $about = About::current();

        return $this->json(array_filter([
            'open_to_work' => $about->open_to_work,
            'email' => $about->email,
            'contact_page' => route('contact'),
            'social_links' => $about->socialLinks(),
        ], fn (mixed $value): bool => $value !== null && $value !== []));
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
