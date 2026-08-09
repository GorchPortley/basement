<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // firstOrCreate so the seeder can be run again without blowing up on
        // the unique email, same as the driver and design seeders below.
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            User::factory()->raw(['name' => 'Test User', 'email' => 'test@example.com']),
        );

        $this->call([
            UserSeeder::class,
            DriverSeeder::class,
            DesignSeeder::class,
        ]);
    }
}
