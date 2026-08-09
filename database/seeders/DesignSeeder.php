<?php

namespace Database\Seeders;

use App\Models\Design;
use App\Models\Driver;
use App\Models\DriverDesign;
use App\Models\User;
use Illuminate\Database\Seeder;

class DesignSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Run this after DriverSeeder so there are drivers to attach.
     */
    public function run(): void
    {
        $testUser = User::firstWhere('email', 'test@example.com')
            ?? User::factory()->create(['name' => 'Test User', 'email' => 'test@example.com']);

        Design::factory(6)
            ->for($testUser, 'owner')
            ->create()
            ->each(fn (Design $design) => $this->attachDrivers($design));

        Design::factory(2)
            ->for($testUser, 'owner')
            ->inactive()
            ->create()
            ->each(fn (Design $design) => $this->attachDrivers($design));

        User::where('id', '!=', $testUser->id)
            ->get()
            ->each(function (User $user) {
                Design::factory(rand(1, 4))
                    ->for($user, 'owner')
                    ->create()
                    ->each(fn (Design $design) => $this->attachDrivers($design));
            });
    }

    /**
     * Give the design a couple of drivers from the library, one per position.
     */
    protected function attachDrivers(Design $design): void
    {
        $positions = array_slice(array_keys(DriverDesign::POSITIONS), 0, rand(1, 3));

        foreach ($positions as $position) {
            $driver = Driver::where('active', true)->inRandomOrder()->first();

            if (! $driver) {
                return;
            }

            $design->driverDesigns()->create([
                'driver_id' => $driver->id,
                'payload' => [
                    'position' => $position,
                    'description' => '<p>'.fake()->sentence(12).'</p>',
                ],
            ]);
        }
    }
}
