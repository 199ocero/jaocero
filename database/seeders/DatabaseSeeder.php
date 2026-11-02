<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(DemoSeeder::class);

        if (! app()->isProduction()) {
            // Add admin@example.com to PORTFOLIO_ADMIN_EMAILS to sign in with password "password".
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
            ]);
        }
    }
}
