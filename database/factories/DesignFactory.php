<?php

namespace Database\Factories;

use App\Models\Design;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Design>
 */
class DesignFactory extends Factory
{
    protected $model = Design::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // The form stores the key, e.g. "three_way", not the label.
        $type = fake()->randomElement(array_keys(Design::TYPES));

        $low = fake()->numberBetween(24, 70);
        $high = fake()->randomElement([18, 20, 22, 24]);

        $title = ucwords(fake()->words(2, true)).' '.fake()->randomElement(['MTM', 'Mini', 'Monitor', 'Tower', 'Bookshelf', 'Sub']);

        return [
            'active' => fake()->boolean(80),
            'owner_id' => User::factory(),
            'owner_type' => User::class,
            'payload' => [
                'meta' => [
                    'title' => $title,
                    'type' => $type,
                    'tagline' => fake()->sentence(6),
                    'build_cost' => fake()->randomFloat(2, 120, 4000),
                ],
                'descriptions' => [
                    'summary' => '<p>'.fake()->paragraph(3).'</p>',
                    'description' => $this->fakeRichTextDescription($title),
                ],
                'specs' => [
                    'Power' => fake()->numberBetween(30, 400).' W',
                    'Sensitivity' => fake()->randomFloat(1, 82, 95).' dB',
                    'Frequency Range' => $low.' Hz – '.$high.' kHz',
                    'Impedance' => fake()->randomElement([4, 6, 8]).' Ω',
                ],
            ],
        ];
    }

    /**
     * Indicate that the design is not published.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Build HTML resembling real RichEditor (Tiptap) output.
     */
    protected function fakeRichTextDescription(string $title): string
    {
        $steps = collect(range(1, 3))
            ->map(fn () => '<li>'.fake()->sentence(rand(4, 8)).'</li>')
            ->implode('');

        return implode('', [
            '<p><strong>'.$title.'</strong> '.fake()->sentence(10).'</p>',
            '<p>'.fake()->paragraph(4).'</p>',
            '<ol>'.$steps.'</ol>',
            '<p>'.fake()->paragraph(3).'</p>',
        ]);
    }
}
