<?php

use App\Ai\Tools\GetExperience;
use App\Models\Experience;
use Laravel\Ai\Tools\Request;

it('lists experience with the current role first', function () {
    Experience::factory()->create(['company' => 'Old Co', 'start_date' => '2015-01-01', 'end_date' => '2018-01-01']);
    Experience::factory()->current()->create(['company' => 'Now Co', 'role' => 'Lead Developer', 'start_date' => '2022-03-01']);

    $result = json_decode((new GetExperience)->handle(new Request), true);

    expect(array_column($result, 'company'))->toBe(['Now Co', 'Old Co'])
        ->and($result[0])->toMatchArray([
            'role' => 'Lead Developer',
            'period' => 'Mar 2022 – Present',
            'current' => true,
        ]);
});

it('says so when no experience is listed', function () {
    expect((string) (new GetExperience)->handle(new Request))->toBe('No work experience has been listed yet.');
});
