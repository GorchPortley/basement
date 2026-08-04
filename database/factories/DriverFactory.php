<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement([
            'Subwoofer',
            'Woofer',
            'Midrange',
            'Tweeter',
            'Passive Radiator',
            'Compression Driver',
            'Horn',
            'Waveguide',
        ]);

        $brand = fake()->randomElement([
            'Dayton Audio',
            'Tang Band',
            'SB Acoustics',
            'Peerless',
            'Scan-Speak',
            'Faital Pro',
            'Eminence',
            'Beyma',
            'B&C Speakers',
            'Celestion',
            'Fostex',
            'Aurasound',
            'Wavecor',
            'Morel',
            'Visaton',
        ]);

        $qes = fake()->randomFloat(3, 0.2, 1.2);
        $qms = fake()->randomFloat(2, 1, 8);

        $nominalSize = fake()->randomElement([3, 4, 5.25, 6.5, 8, 10, 12, 15, 18]);

        return [
            'active' => fake()->boolean(80),
            'owner_id' => User::factory(),
            'owner_type' => User::class,
            'payload' => [
                'meta' => [
                    'type' => $type,
                    'brand' => $brand,
                    'size' => $nominalSize,
                    'impedance' => fake()->randomElement([2, 4, 8, 16, '2+2', '4+4', '8+8']),
                    'model' => strtoupper(fake()->bothify('??-###??')),
                    'tag' => fake()->optional()->word(),
                    'link' => fake()->url(),
                    'price' => fake()->randomFloat(2, 15, 899),
                ],
                'descriptions' => [
                    'description' => $this->fakeRichTextDescription($brand, $type),
                ],
                'specs' => [
                    'outside_dimeter' => round($nominalSize + fake()->randomFloat(2, 0.3, 1.5), 2).'"',
                    'mount_diameter' => round($nominalSize - fake()->randomFloat(2, 0.1, 0.4), 2).'"',
                    'depth' => round($nominalSize * fake()->randomFloat(2, 0.35, 0.6), 2).'"',
                    'tsparam' => [
                        'SPL' => fake()->randomFloat(1, 82, 98).' dB',
                        'Sd' => fake()->randomFloat(1, 20, 800).' cm2',
                        'Mms' => fake()->randomFloat(2, 2, 250).' g',
                        'Cms' => fake()->randomFloat(3, 0.05, 2).' mm/N',
                        'Rms' => fake()->randomFloat(2, 0.5, 10).' kg/s',
                        'Le' => fake()->randomFloat(2, 0.1, 3).' mH',
                        'Re' => fake()->randomFloat(2, 2, 8).' ohm',
                        'Bl' => fake()->randomFloat(2, 3, 25).' Tm',
                        'fs' => fake()->randomFloat(1, 18, 120).' Hz',
                        'Qes' => (string) $qes,
                        'Qms' => (string) $qms,
                        'Qts' => (string) round(($qes * $qms) / ($qes + $qms), 3),
                        'Vas' => fake()->randomFloat(1, 1, 300).' L',
                        'Xmax' => fake()->randomFloat(1, 1, 30).' mm',
                        'Xmech' => fake()->randomFloat(1, 2, 40).' mm',
                        'Pe' => fake()->randomFloat(0, 20, 1000).' W',
                        'Vd' => fake()->randomFloat(2, 0.05, 5).' L',
                        'n0%' => fake()->randomFloat(2, 0.1, 10).'%',
                    ],
                ],
            ],
        ];
    }

    /**
     * Indicate that the driver is inactive.
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
    protected function fakeRichTextDescription(string $brand, string $type): string
    {
        $features = collect(range(1, 3))
            ->map(fn () => '<li>'.fake()->sentence(rand(4, 8)).'</li>')
            ->implode('');

        return implode('', [
            '<p><strong>'.$brand.' '.$type.'</strong> '.fake()->sentence(10).'</p>',
            '<p>'.fake()->paragraph(4).'</p>',
            '<ul>'.$features.'</ul>',
            '<p>'.fake()->paragraph(3).'</p>',
        ]);
    }
}
