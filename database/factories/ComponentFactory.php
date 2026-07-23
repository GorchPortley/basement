<?php

namespace Database\Factories;

use App\Enums\ComponentCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Component>
 */
class ComponentFactory extends Factory
{
    public function definition(): array
    {
        $brands = ['Dayton Audio', 'Tang Band', 'SB Acoustics', 'Peerless', 'SEAS', 'Wavecor', 'Scan-Speak'];

        return [
            'owner_id' => User::factory(),
            'owner_type' => User::class,
            'brand' => fake()->randomElement($brands),
            'model' => strtoupper(fake()->bothify('??-####')),
            'category' => fake()->randomElement(ComponentCategory::values()),
            'size' => fake()->randomElement([1, 1.1, 3, 4, 5.25, 6.5, 8, 10, 12]),
            'impedance' => (string) fake()->randomElement([4, 6, 8]),
            'power' => fake()->randomElement([30, 50, 80, 100, 150]),
            'price' => fake()->randomFloat(2, 15, 300),
            'link' => 'https://example.com/'.fake()->slug(),
            'summary' => fake()->sentence(6),
            'official' => fake()->boolean(30),
            'active' => true,
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function official(): static
    {
        return $this->state(fn () => ['official' => true]);
    }
}
