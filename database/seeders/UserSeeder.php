<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * DriverSeeder assigns a batch of drivers to every user that exists at
     * the time it runs, so keep this pool as its minimum owner supply.
     */
    public function run(): void
    {
        User::factory(10)->create();
    }
}
