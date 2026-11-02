<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Concerns\ReturnsJson;
use App\Models\Experience;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetExperience implements Tool
{
    use ReturnsJson;

    public function description(): Stringable|string
    {
        return "Get the owner's work experience: roles, companies, dates and descriptions, current role first.";
    }

    public function handle(Request $request): Stringable|string
    {
        $experiences = Experience::latestFirst()->get();

        if ($experiences->isEmpty()) {
            return 'No work experience has been listed yet.';
        }

        return $this->json($experiences->map(fn (Experience $experience): array => [
            'role' => $experience->role,
            'company' => $experience->company,
            'period' => $experience->period(),
            'current' => $experience->isCurrent(),
            'description' => $experience->description,
        ])->all());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
