<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $testUser = User::firstWhere('email', 'test@example.com')
            ?? User::factory()->create(['name' => 'Test User', 'email' => 'test@example.com']);

        Driver::factory(12)
            ->for($testUser, 'owner')
            ->create();

        Driver::factory(3)
            ->for($testUser, 'owner')
            ->inactive()
            ->create();

        User::where('id', '!=', $testUser->id)
            ->get()
            ->each(function (User $user) {
                Driver::factory(rand(3, 10))
                    ->for($user, 'owner')
                    ->create();
            });
    }
}
