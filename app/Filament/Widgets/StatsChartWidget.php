<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Product;
use App\Models\Pack;
use App\Models\Service;
use App\Models\User;
use App\Models\Client;
use Carbon\Carbon;

class StatsChartWidget extends LineChartWidget
{
    protected static ?string $heading = 'Évolution des entités (6 mois)';
    protected static ?int    $sort    = 1;
    protected static ?string $maxHeight = '280px';

    // Filtre période (optionnel — affiche un sélecteur dans le header du widget)
    public ?string $filter = '6months';

    protected function getFilters(): ?array
    {
        return [
            '3months' => '3 derniers mois',
            '6months' => '6 derniers mois',
            '12months' => '12 derniers mois',
        ];
    }

    protected function getData(): array
    {
        $nbMonths = match($this->filter) {
            '3months'  => 3,
            '12months' => 12,
            default    => 6,
        };

        $labels = [];
        $months = [];

        for ($i = $nbMonths - 1; $i >= 0; $i--) {
            $month    = Carbon::now()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');
            $months[] = $month;
        }

        return [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Produits',
                    'data'            => $this->countPerMonth(Product::class, $months),
                    'borderColor'     => '#0d9488',
                    'backgroundColor' => 'rgba(13, 148, 136, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Packs',
                    'data'            => $this->countPerMonth(Pack::class, $months),
                    'borderColor'     => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Services',
                    'data'            => $this->countPerMonth(Service::class, $months),
                    'borderColor'     => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Clients',
                    'data'            => $this->countPerMonth(Client::class, $months),
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Utilisateurs',
                    'data'            => $this->countPerMonth(User::class, $months),
                    'borderColor'     => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display'  => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'mode' => 'index',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => ['stepSize' => 1],
                    'grid'        => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }

    // ── Compte les créations par mois ─────────────────────────────────────────
    private function countPerMonth(string $modelClass, array $months): array
    {
        return array_map(fn(Carbon $month) =>
            $modelClass::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count(),
            $months
        );
    }
}