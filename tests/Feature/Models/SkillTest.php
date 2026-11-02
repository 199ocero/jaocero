<?php

use App\Enums\SkillCategory;
use App\Models\Skill;

it('groups skills by category in the order categories are defined', function () {
    Skill::factory()->create(['name' => 'Docker', 'category' => SkillCategory::DevOps, 'sort_order' => 0]);
    Skill::factory()->create(['name' => 'Vue', 'category' => SkillCategory::Frontend, 'sort_order' => 1]);
    Skill::factory()->create(['name' => 'Laravel', 'category' => SkillCategory::Backend, 'sort_order' => 2]);
    Skill::factory()->create(['name' => 'Livewire', 'category' => SkillCategory::Frontend, 'sort_order' => 0]);

    $grouped = Skill::groupedByCategory();

    expect($grouped->keys()->all())->toBe(['Backend', 'Frontend', 'DevOps'])
        ->and($grouped['Frontend']->pluck('name')->all())->toBe(['Livewire', 'Vue']);
});
