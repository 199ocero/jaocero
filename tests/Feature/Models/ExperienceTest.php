<?php

use App\Models\Experience;

it('treats a role without an end date as current', function () {
    expect(Experience::factory()->current()->make()->isCurrent())->toBeTrue()
        ->and(Experience::factory()->make()->isCurrent())->toBeFalse();
});

it('formats the period with "Present" for current roles', function () {
    $experience = Experience::factory()->current()->make(['start_date' => '2022-03-15']);

    expect($experience->period())->toBe('Mar 2022 – Present');
});

it('lists current roles first, then the most recent', function () {
    $old = Experience::factory()->create(['start_date' => '2015-01-01', 'end_date' => '2018-01-01']);
    $recent = Experience::factory()->create(['start_date' => '2018-02-01', 'end_date' => '2022-01-01']);
    $current = Experience::factory()->current()->create(['start_date' => '2022-02-01']);

    expect(Experience::latestFirst()->pluck('id')->all())->toBe([$current->id, $recent->id, $old->id]);
});
