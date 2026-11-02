<?php

use App\Ai\Tools\GetSkills;
use App\Enums\SkillCategory;
use App\Models\Skill;
use Laravel\Ai\Tools\Request;

it('groups skills by category, in category order', function () {
    Skill::factory()->create(['name' => 'Livewire', 'category' => SkillCategory::Frontend, 'sort_order' => 0, 'proficiency' => 90]);
    Skill::factory()->create(['name' => 'Laravel', 'category' => SkillCategory::Backend, 'sort_order' => 1, 'proficiency' => null]);

    $result = json_decode((new GetSkills)->handle(new Request), true);

    expect($result)->toBe([
        'Backend' => [['name' => 'Laravel']],
        'Frontend' => [['name' => 'Livewire', 'proficiency' => 90]],
    ]);
});

it('says so when no skills are listed', function () {
    expect((string) (new GetSkills)->handle(new Request))->toBe('No skills have been listed yet.');
});
