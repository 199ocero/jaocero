<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Concerns\ReturnsJson;
use App\Models\Skill;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetSkills implements Tool
{
    use ReturnsJson;

    public function description(): Stringable|string
    {
        return "Get the owner's skills and tech stack, grouped by category (backend, frontend, AI, DevOps...). Proficiency is from 1 to 100 when known.";
    }

    public function handle(Request $request): Stringable|string
    {
        $skillsByCategory = Skill::groupedByCategory();

        if ($skillsByCategory->isEmpty()) {
            return 'No skills have been listed yet.';
        }

        return $this->json($skillsByCategory
            ->map(fn ($skills) => $skills->map(fn (Skill $skill): array => array_filter([
                'name' => $skill->name,
                'proficiency' => $skill->proficiency,
            ]))->values())
            ->all());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
