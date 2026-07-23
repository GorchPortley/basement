<?php

namespace App\Livewire;

use App\Models\Component;
use App\Models\ComponentDesign;
use App\Models\Design;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component as LivewireComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Renders loudspeaker frequency-response curves parsed from FRD files.
 *
 * FRD is a plain-text format: each line is "frequency  amplitude(dB)  phase(deg)".
 * When a design uses several drivers, this also computes the *complex sum* of
 * their responses (magnitude + phase combined per frequency) so you can see how
 * the finished speaker measures — the core analytical feature ported from the
 * original SDLabs app.
 *
 * Sources come from Spatie media collections:
 *   - a Component's factory "frequency" collection, or
 *   - every driver placement in a Design (measured pivot media, else factory).
 */
class FrequencyResponseViewer extends LivewireComponent
{
    public ?Design $design = null;

    public ?Component $component = null;

    public array $chartData = [];        // per-trace amplitude curves

    public array $summedResponse = [];   // complex-summed system response

    public array $phaseData = [];        // per-trace phase curves

    public string $activeTab = 'amplitude';

    private const FREQ_RANGE = ['min' => 20, 'max' => 20000];

    private const AMP_RANGE = ['min' => 0, 'max' => 120];

    private const COLORS = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
        '#9966FF', '#FF9F40', '#8AC926', '#C9CBCF',
    ];

    public function mount(?Design $design = null, ?Component $component = null): void
    {
        $this->design = $design;
        $this->component = $component;

        $processed = $this->buildTraces($this->gatherSources());

        $this->chartData = $processed['chartData'];
        $this->summedResponse = $processed['summedResponse'];
        $this->phaseData = $processed['phaseData'];
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    /**
     * Collect FRD text from the relevant media, each as ['label' => .., 'content' => ..].
     *
     * @return array<int, array{label: string, content: string}>
     */
    private function gatherSources(): array
    {
        $sources = [];

        if ($this->component) {
            foreach ($this->component->getMedia('frequency') as $media) {
                if ($content = $this->readMedia($media)) {
                    $sources[] = ['label' => $this->component->name, 'content' => $content];
                }
            }
        }

        if ($this->design) {
            $this->design->loadMissing('components');

            foreach ($this->design->components as $component) {
                $pivot = $component->pivot;

                // Prefer measured (in-situ) data attached to the placement pivot.
                $media = ($pivot instanceof ComponentDesign && $pivot->getKey())
                    ? $pivot->getMedia('frequency')
                    : collect();

                if ($media->isEmpty()) {
                    $media = $component->getMedia('frequency');
                }

                $position = $pivot->position?->value ?? '';
                $label = trim($position.' '.$component->name);

                foreach ($media as $file) {
                    if ($content = $this->readMedia($file)) {
                        $sources[] = ['label' => $label, 'content' => $content];
                    }
                }
            }

            // Any design-level curves (e.g. a full-system measurement).
            foreach ($this->design->getMedia('frd') as $file) {
                if ($content = $this->readMedia($file)) {
                    $sources[] = ['label' => pathinfo($file->file_name, PATHINFO_FILENAME), 'content' => $content];
                }
            }
        }

        return $sources;
    }

    /** Read a media file's contents across disks, returning null on any failure. */
    private function readMedia(Media $media): ?string
    {
        // Local / public disks: read straight off the filesystem.
        try {
            $path = $media->getPath();
            if (is_file($path)) {
                return file_get_contents($path) ?: null;
            }
        } catch (\Throwable) {
            // fall through to the disk abstraction
        }

        // Remote disks (S3 / Garage).
        try {
            return Storage::disk($media->disk)->get($media->getPathRelativeToRoot());
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param  array<int, array{label: string, content: string}>  $sources
     * @return array{chartData: array, summedResponse: array, phaseData: array}
     */
    private function buildTraces(array $sources): array
    {
        $processed = [];
        $colorIndex = 0;

        foreach ($sources as $source) {
            $points = $this->parseFrd($source['content']);

            if (! empty($points)) {
                $processed[] = [
                    'data' => $points,
                    'label' => $source['label'],
                    'color' => self::COLORS[$colorIndex % count(self::COLORS)],
                ];
                $colorIndex++;
            }
        }

        $amplitude = $this->processAmplitudeData($processed);

        return [
            'chartData' => $amplitude['chartData'],
            'summedResponse' => $amplitude['summedResponse'],
            'phaseData' => $this->processPhaseData($processed),
        ];
    }

    /** @return array<int, array{frequency: float, amplitude: float, phase: float}> */
    private function parseFrd(string $content): array
    {
        $lines = array_filter(
            explode("\n", trim($content)),
            fn (string $line): bool => $line !== '' && $line[0] !== '*' && $line[0] !== '#',
        );

        $data = [];

        foreach ($lines as $line) {
            $values = preg_split('/[\s,]+/', trim($line));

            if (! $values || count($values) < 2) {
                continue;
            }

            $freq = (float) $values[0];
            $amp = (float) $values[1];
            $phase = (float) ($values[2] ?? 0);

            if ($freq < self::FREQ_RANGE['min'] || $freq > self::FREQ_RANGE['max']) {
                continue;
            }

            if ($amp < self::AMP_RANGE['min'] || $amp > self::AMP_RANGE['max']) {
                continue;
            }

            $data[] = ['frequency' => $freq, 'amplitude' => $amp, 'phase' => $phase];
        }

        return $data;
    }

    private function processAmplitudeData(array $processed): array
    {
        $chartData = [];
        $summedData = [];

        foreach ($processed as $dataset) {
            $chartData[] = [
                'label' => $dataset['label'],
                'data' => array_map(fn (array $p): array => [
                    'x' => $p['frequency'],
                    'y' => $p['amplitude'],
                ], $dataset['data']),
                'borderColor' => $dataset['color'],
                'fill' => false,
            ];

            // Accumulate the complex (real/imaginary) sum across all drivers.
            foreach ($dataset['data'] as $point) {
                $freq = $point['frequency'];
                $amplitude = pow(10, $point['amplitude'] / 20.0);
                $phase = deg2rad($point['phase']);

                $real = $amplitude * cos($phase);
                $imag = $amplitude * sin($phase);

                if (! isset($summedData[$freq])) {
                    $summedData[$freq] = ['real' => $real, 'imag' => $imag];
                } else {
                    $summedData[$freq]['real'] += $real;
                    $summedData[$freq]['imag'] += $imag;
                }
            }
        }

        $summedResponse = [];

        if (! empty($summedData)) {
            ksort($summedData);

            $points = [];
            foreach ($summedData as $freq => $complex) {
                $magnitude = sqrt($complex['real'] ** 2 + $complex['imag'] ** 2);
                $db = 20 * log10(max($magnitude, 1e-20));
                $points[] = ['x' => (float) $freq, 'y' => $db];
            }

            $summedResponse = $this->smoothLowFrequencies($points);
        }

        return ['chartData' => $chartData, 'summedResponse' => $summedResponse];
    }

    /** Moving-average smoothing below a threshold to tame noisy low-frequency data. */
    private function smoothLowFrequencies(array $data, int $lowFreqThreshold = 60, int $windowSize = 33): array
    {
        usort($data, fn (array $a, array $b): int => $a['x'] <=> $b['x']);

        $smoothed = [];

        foreach ($data as $index => $point) {
            if ($point['x'] < $lowFreqThreshold) {
                $start = (int) max(0, $index - floor($windowSize / 2));
                $end = (int) min(count($data) - 1, $index + floor($windowSize / 2));

                $window = array_slice($data, $start, $end - $start + 1);
                $avg = array_sum(array_column($window, 'y')) / count($window);

                $smoothed[] = ['x' => $point['x'], 'y' => $avg];
            } else {
                $smoothed[] = $point;
            }
        }

        return $smoothed;
    }

    private function processPhaseData(array $processed): array
    {
        $phaseData = [];

        foreach ($processed as $dataset) {
            $phaseData[] = [
                'label' => $dataset['label'].' phase',
                'data' => array_map(fn (array $p): array => [
                    'x' => $p['frequency'],
                    'y' => $this->normalizePhase($p['phase']),
                ], $dataset['data']),
                'borderColor' => $dataset['color'],
                'fill' => false,
            ];
        }

        return $phaseData;
    }

    private function normalizePhase(float $phase): float
    {
        while ($phase > 180) {
            $phase -= 360;
        }
        while ($phase < -180) {
            $phase += 360;
        }

        return $phase;
    }

    public function render(): View
    {
        return view('livewire.frequency-response-viewer', [
            'hasData' => ! empty($this->chartData),
            'showSummed' => count($this->chartData) > 1,
        ]);
    }
}
