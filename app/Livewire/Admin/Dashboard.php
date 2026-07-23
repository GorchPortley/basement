<?php

namespace App\Livewire\Admin;

use App\Models\Component;
use App\Models\Design;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component as LivewireComponent;

class Dashboard extends LivewireComponent
{
    private const PALETTE = ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#8AC926', '#C9CBCF'];

    public function boot(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);
    }

    public function render(): View
    {
        $stats = [
            'users' => User::count(),
            'designs' => Design::count(),
            'designs_published' => Design::where('active', true)->count(),
            'components' => Component::count(),
            'components_published' => Component::where('active', true)->count(),
            'official' => Design::where('official', true)->count() + Component::where('official', true)->count(),
        ];

        // 30-day activity series.
        $days = collect(range(29, 0, -1))->map(fn (int $i) => Carbon::today()->subDays($i));
        $labels = $days->map(fn (Carbon $d): string => $d->format('M j'))->all();

        $activityConfig = [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Designs',
                        'data' => $this->dailyCounts(Design::query(), $days),
                        'borderColor' => self::PALETTE[0],
                        'backgroundColor' => self::PALETTE[0],
                        'tension' => 0.3,
                        'fill' => false,
                    ],
                    [
                        'label' => 'Components',
                        'data' => $this->dailyCounts(Component::query(), $days),
                        'borderColor' => self::PALETTE[3],
                        'backgroundColor' => self::PALETTE[3],
                        'tension' => 0.3,
                        'fill' => false,
                    ],
                    [
                        'label' => 'New users',
                        'data' => $this->dailyCounts(User::query(), $days),
                        'borderColor' => self::PALETTE[1],
                        'backgroundColor' => self::PALETTE[1],
                        'tension' => 0.3,
                        'fill' => false,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'interaction' => ['mode' => 'index', 'intersect' => false],
                'plugins' => ['legend' => ['position' => 'bottom']],
                'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
            ],
        ];

        // Designs by category.
        $categoryCounts = Design::query()
            ->selectRaw('category, count(*) as c')
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('c', 'category');

        $categoryConfig = [
            'type' => 'doughnut',
            'data' => [
                'labels' => $categoryCounts->keys()->all(),
                'datasets' => [[
                    'data' => $categoryCounts->values()->all(),
                    'backgroundColor' => array_slice(self::PALETTE, 0, max(1, $categoryCounts->count())),
                    'borderWidth' => 0,
                ]],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => ['legend' => ['position' => 'right']],
            ],
        ];

        return view('livewire.admin.dashboard', [
            'stats' => $stats,
            'activityConfig' => $activityConfig,
            'categoryConfig' => $categoryConfig,
            'hasCategories' => $categoryCounts->isNotEmpty(),
        ]);
    }

    /**
     * Count rows per day, filling gaps with zeros.
     *
     * @param  Collection<int, Carbon>  $days
     * @return array<int, int>
     */
    private function dailyCounts(Builder $query, Collection $days): array
    {
        $counts = $query
            ->selectRaw('date(created_at) as d, count(*) as c')
            ->where('created_at', '>=', $days->first()->startOfDay())
            ->groupBy('d')
            ->pluck('c', 'd');

        return $days->map(fn (Carbon $d): int => (int) ($counts[$d->format('Y-m-d')] ?? 0))->all();
    }
}
