<?php

namespace App\Ai\Tools;

use App\Models\About;
use App\Support\Seo\AboutMarkdown;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetAbout implements Tool
{
    public function description(): Stringable|string
    {
        return "Get the owner's profile: bio, headline, location, whether they are open to work, and their social links.";
    }

    public function handle(Request $request): Stringable|string
    {
        return AboutMarkdown::render(About::current());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
