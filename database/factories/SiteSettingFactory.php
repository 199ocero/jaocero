<?php

namespace Database\Factories;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteSetting>
 */
class SiteSettingFactory extends Factory
{
    /**
     * Define the model's default state: the config defaults.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return SiteSetting::defaults();
    }
}
