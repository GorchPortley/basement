<?php

namespace Database\Factories;

use App\Enums\DesignAccess;
use App\Enums\DesignCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Design>
 */
class DesignFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'owner_type' => User::class,
            'name' => Str::title(fake()->unique()->words(2, true)),
            'summary' => fake()->sentence(8),
            'description' => fake()->paragraphs(3, true),
            'category' => fake()->randomElement(DesignCategory::values()),
            'access' => fake()->randomElement(DesignAccess::values()),
            'price' => fake()->randomFloat(2, 0, 80),
            'build_cost' => fake()->randomFloat(2, 50, 600),
            'impedance' => fake()->randomElement([4, 8]),
            'power' => fake()->randomElement([50, 100, 150, 200]),
            'official' => fake()->boolean(20),
            'active' => true,
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}
