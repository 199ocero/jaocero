<?php

use App\Enums\SkillCategory;
use App\Models\About;
use App\Models\Experience;
use App\Models\Skill;

it('shows the bio, skills grouped by category and the experience timeline', function () {
    About::factory()->create(['bio' => 'I build calm, well-tested software.', 'location' => 'Lisbon']);
    Skill::factory()->create(['name' => 'Laravel', 'category' => SkillCategory::Backend]);
    Skill::factory()->create(['name' => 'Tailwind CSS', 'category' => SkillCategory::Frontend]);
    Experience::factory()->current()->create(['company' => 'Northwind Labs', 'role' => 'Senior Developer']);

    $this->get(route('about'))
        ->assertOk()
        ->assertSee('I build calm, well-tested software.')
        ->assertSee('Lisbon')
        ->assertSeeInOrder(['Backend', 'Laravel'])
        ->assertSeeInOrder(['Frontend', 'Tailwind CSS'])
        ->assertSee('Northwind Labs')
        ->assertSee('Present');
});

it('offers the resume for download when one is uploaded', function () {
    About::factory()->create(['resume' => 'about/resume.pdf']);

    $this->get(route('about'))->assertSee('Download resume');
});

it('hides the resume button when no resume is uploaded', function () {
    About::factory()->create(['resume' => null]);

    $this->get(route('about'))->assertDontSee('Download resume');
});

it('answers common questions about the owner', function () {
    config()->set('portfolio.owner_name', 'Sam Rivera');
    config()->set('portfolio.headline', 'a Laravel developer who builds with *AI*');
    About::factory()->create(['location' => 'Lisbon', 'email' => 'sam@example.com']);
    Skill::factory()->create(['name' => 'Laravel', 'category' => SkillCategory::Backend]);
    Skill::factory()->create(['name' => 'Livewire', 'category' => SkillCategory::Frontend]);
    Experience::factory()->current()->create(['company' => 'Northwind Labs', 'role' => 'Senior Developer']);

    $this->get(route('about'))
        ->assertOk()
        ->assertSeeInOrder(['What does Sam Rivera do?', 'Sam Rivera is a Laravel developer who builds with AI. Location: Lisbon.'])
        ->assertSeeInOrder(["What is Sam Rivera's tech stack?", 'Sam Rivera mostly works with Laravel and Livewire.'], false)
        ->assertSeeInOrder(['Where has Sam Rivera worked?', 'Sam Rivera currently works as Senior Developer at Northwind Labs.'])
        ->assertSee('href="mailto:sam@example.com"', false);
});

it('says whether the owner is available for hire', function (bool $openToWork, string $answer) {
    config()->set('portfolio.owner_name', 'Sam Rivera');
    About::factory()->create(['open_to_work' => $openToWork]);

    $this->get(route('about'))->assertSee($answer, false);
})->with([
    'open to work' => [true, 'Yes. Sam Rivera is open to new opportunities.'],
    'not looking' => [false, "Sam Rivera isn't actively looking right now"],
]);

it('skips the questions it has no content to answer', function () {
    config()->set('portfolio.owner_name', 'Sam Rivera');

    $this->get(route('about'))
        ->assertOk()
        ->assertDontSee("What is Sam Rivera's tech stack?", false)
        ->assertDontSee('Where has Sam Rivera worked?');
});
