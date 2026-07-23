<?php

namespace Database\Seeders;

use App\Models\Component;
use App\Models\Design;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        | Users -------------------------------------------------------------- */
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@sdlabs.test',
        ]);
        $admin->forceFill(['is_admin' => true])->save();

        $designer = User::factory()->create([
            'name' => 'Demo Designer',
            'email' => 'designer@sdlabs.test',
        ]);

        $users = User::factory(4)->create();
        $pool = $users->push($admin, $designer);

        /*
        | Hero components (with synthetic factory FRD data) ------------------- */
        $woofer = Component::create([
            'owner_id' => $designer->id, 'owner_type' => User::class,
            'brand' => 'Dayton Audio', 'model' => 'DC160-8', 'category' => 'Woofer',
            'size' => 6.5, 'impedance' => '8', 'power' => 80, 'price' => 38.80,
            'link' => 'https://example.com/dc160-8',
            'summary' => 'Classic 6.5" paper-cone woofer.',
            'official' => true, 'active' => true,
        ]);
        $woofer->addMediaFromString($this->frd('woofer'))->usingFileName('factory.frd')->toMediaCollection('frequency');

        $tweeter = Component::create([
            'owner_id' => $designer->id, 'owner_type' => User::class,
            'brand' => 'Dayton Audio', 'model' => 'DC28F-8', 'category' => 'Tweeter',
            'size' => 1.1, 'impedance' => '8', 'power' => 60, 'price' => 26.60,
            'link' => 'https://example.com/dc28f-8',
            'summary' => 'Silk-dome tweeter with smooth extension.',
            'official' => true, 'active' => true,
        ]);
        $tweeter->addMediaFromString($this->frd('tweeter'))->usingFileName('factory.frd')->toMediaCollection('frequency');

        $sub = Component::create([
            'owner_id' => $designer->id, 'owner_type' => User::class,
            'brand' => 'Dayton Audio', 'model' => 'RSS265HO-4', 'category' => 'Subwoofer',
            'size' => 10, 'impedance' => '4', 'power' => 200, 'price' => 79.00,
            'link' => 'https://example.com/rss265ho-4',
            'summary' => 'High-output 10" reference subwoofer.',
            'official' => false, 'active' => true,
        ]);
        $sub->addMediaFromString($this->frd('sub'))->usingFileName('factory.frd')->toMediaCollection('frequency');

        /*
        | Hero designs ------------------------------------------------------- */
        $ons = Design::create([
            'owner_id' => $designer->id, 'owner_type' => User::class,
            'name' => 'Overnight Sensations',
            'summary' => 'A beloved budget two-way that punches far above its price.',
            'description' => "A compact sealed two-way using a 6.5\" woofer and silk-dome tweeter.\n\nEasy to build in a weekend, forgiving of placement, and a great first project.",
            'category' => 'Two-Way', 'access' => 'free', 'price' => 0, 'build_cost' => 125,
            'impedance' => 8, 'power' => 100, 'official' => true, 'active' => true,
            'payload' => ['bill_of_materials' => [
                ['item' => '0.5" MDF sheet', 'quantity' => '1', 'notes' => 'Cabinet'],
                ['item' => 'Crossover components', 'quantity' => '1 set', 'notes' => 'See schematic'],
                ['item' => 'Acoustic stuffing', 'quantity' => '1 bag', 'notes' => ''],
            ]],
        ]);
        $ons->components()->attach($woofer->id, ['position' => 'LF', 'quantity' => 1, 'high_frequency' => 2500, 'air_volume' => 10]);
        $ons->components()->attach($tweeter->id, ['position' => 'HF', 'quantity' => 1, 'low_frequency' => 2500, 'high_frequency' => 20000]);

        $microSub = Design::create([
            'owner_id' => $designer->id, 'owner_type' => User::class,
            'name' => 'Micro Reference Sub',
            'summary' => 'A tight sealed 10" subwoofer for small rooms.',
            'description' => 'A sealed subwoofer tuned for accuracy over output.',
            'category' => 'Subwoofer', 'access' => 'gated', 'price' => 15, 'build_cost' => 220,
            'impedance' => 4, 'power' => 200, 'official' => false, 'active' => true,
        ]);
        $microSub->components()->attach($sub->id, ['position' => 'LF', 'quantity' => 1, 'air_volume' => 30]);

        // A draft to demonstrate owner preview + admin moderation.
        Design::factory()->draft()->create([
            'owner_id' => $admin->id, 'owner_type' => User::class,
            'name' => 'WIP Prototype',
        ]);

        /*
        | Filler ------------------------------------------------------------- */
        Component::factory(10)->recycle($pool)->create();
        Design::factory(8)->recycle($pool)->create();

        // Give filler designs a couple of random components each.
        $componentIds = Component::pluck('id');

        Design::doesntHave('components')->get()->each(function (Design $design) use ($componentIds): void {
            if ($componentIds->isEmpty()) {
                return;
            }

            $picks = $componentIds->random(min(3, $componentIds->count()));
            $positions = ['LF', 'MF', 'HF'];

            foreach ($picks->values() as $i => $componentId) {
                $design->components()->attach($componentId, [
                    'position' => $positions[$i % count($positions)],
                    'quantity' => 1,
                ]);
            }
        });
    }

    /**
     * Generate a synthetic FRD (frequency-response) text file for demo curves.
     * Columns: frequency(Hz)  amplitude(dB)  phase(deg).
     */
    private function frd(string $type): string
    {
        $lines = ["* Synthetic demo FRD ({$type}) — for wireframe/testing only"];

        for ($f = 20.0; $f <= 20000.0; $f *= 1.05) {
            [$spl, $phase] = match ($type) {
                'woofer' => [
                    87 - ($f > 1800 ? 12 * log($f / 1800, 2) : 0),
                    -rad2deg(atan2($f, 1800)),
                ],
                'tweeter' => [
                    88 - ($f < 1800 ? 12 * log(1800 / $f, 2) : 0) - ($f > 16000 ? 6 * log($f / 16000, 2) : 0),
                    rad2deg(atan2(1800, $f)),
                ],
                default => [ // sub
                    90 - ($f > 120 ? 24 * log($f / 120, 2) : 0) - ($f < 30 ? 24 * log(30 / $f, 2) : 0),
                    -rad2deg(atan2($f, 90)),
                ],
            };

            $spl = max(20, min(115, $spl));
            $lines[] = sprintf('%.1f  %.2f  %.2f', round($f, 1), $spl, $phase);
        }

        return implode("\n", $lines);
    }
}
