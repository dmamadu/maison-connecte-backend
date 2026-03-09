<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Pack;
use App\Models\Service;
use App\Models\User;
use App\Models\Contact;   // ajuste selon ton modèle réel
use App\Models\Client;
use Carbon\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        return [
            // ── Produits ─────────────────────────────────────────────────────
            Stat::make('Produits', Product::count())
                ->description($this->trend(Product::class, $thisMonth, $lastMonth, $lastMonthEnd))
                ->descriptionIcon($this->trendIcon(Product::class, $thisMonth, $lastMonth, $lastMonthEnd))
                ->color($this->trendColor(Product::class, $thisMonth, $lastMonth, $lastMonthEnd))
                ->icon('heroicon-o-cube')
                ->chart($this->sparkline(Product::class)),

            // ── Packs ─────────────────────────────────────────────────────────
            Stat::make('Packs', Pack::count())
                ->description($this->trend(Pack::class, $thisMonth, $lastMonth, $lastMonthEnd))
                ->descriptionIcon($this->trendIcon(Pack::class, $thisMonth, $lastMonth, $lastMonthEnd))
                ->color($this->trendColor(Pack::class, $thisMonth, $lastMonth, $lastMonthEnd))
                ->icon('heroicon-o-archive-box')
                ->chart($this->sparkline(Pack::class)),

            // ── Services ─────────────────────────────────────────────────────
            Stat::make('Services', Service::count())
                ->description($this->trend(Service::class, $thisMonth, $lastMonth, $lastMonthEnd))
                ->descriptionIcon($this->trendIcon(Service::class, $thisMonth, $lastMonth, $lastMonthEnd))
                ->color($this->trendColor(Service::class, $thisMonth, $lastMonth, $lastMonthEnd))
                ->icon('heroicon-o-wrench-screwdriver')
                ->chart($this->sparkline(Service::class)),

            // ── Utilisateurs ─────────────────────────────────────────────────
            Stat::make('Utilisateurs', User::count())
                ->description($this->trend(User::class, $thisMonth, $lastMonth, $lastMonthEnd))
                ->descriptionIcon($this->trendIcon(User::class, $thisMonth, $lastMonth, $lastMonthEnd))
                ->color($this->trendColor(User::class, $thisMonth, $lastMonth, $lastMonthEnd))
                ->icon('heroicon-o-users')
                ->chart($this->sparkline(User::class)),

            // ── Clients ──────────────────────────────────────────────────────
            Stat::make('Clients', Client::count())
                ->description(Client::where('is_active', true)->count() . ' actifs')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->icon('heroicon-o-building-office-2')
                ->chart($this->sparkline(Client::class)),

            // ── Messages non lus ─────────────────────────────────────────────
            // Stat::make('Messages', Contact::where('is_read', false)->count())
            //     ->description('Non lus')
            //     ->descriptionIcon('heroicon-m-envelope')
            //     ->color('warning')
            //     ->icon('heroicon-o-chat-bubble-left-ellipsis'),
        ];
    }

    // ── Sparkline : courbe 7 derniers jours ───────────────────────────────────
    private function sparkline(string $model): array
    {
        return collect(range(6, 0))
            ->map(fn($d) => $model::whereDate('created_at', Carbon::now()->subDays($d))->count())
            ->toArray();
    }

    // ── Tendance : nb créés ce mois vs mois dernier ───────────────────────────
    private function countInPeriod(string $model, Carbon $start, Carbon $end): int
    {
        return $model::whereBetween('created_at', [$start, $end])->count();
    }

    private function trend(string $model, Carbon $thisMonth, Carbon $lastMonth, Carbon $lastMonthEnd): string
    {
        $current  = $this->countInPeriod($model, $thisMonth, Carbon::now());
        $previous = $this->countInPeriod($model, $lastMonth, $lastMonthEnd);

        if ($previous === 0) return $current > 0 ? "+{$current} ce mois" : 'Aucun ce mois';

        $diff = $current - $previous;
        $pct  = round(abs($diff) / $previous * 100);

        return $diff >= 0
            ? "+{$pct}% vs mois dernier"
            : "-{$pct}% vs mois dernier";
    }

    private function trendIcon(string $model, Carbon $thisMonth, Carbon $lastMonth, Carbon $lastMonthEnd): string
    {
        $current  = $this->countInPeriod($model, $thisMonth, Carbon::now());
        $previous = $this->countInPeriod($model, $lastMonth, $lastMonthEnd);

        return $current >= $previous
            ? 'heroicon-m-arrow-trending-up'
            : 'heroicon-m-arrow-trending-down';
    }

    private function trendColor(string $model, Carbon $thisMonth, Carbon $lastMonth, Carbon $lastMonthEnd): string
    {
        $current  = $this->countInPeriod($model, $thisMonth, Carbon::now());
        $previous = $this->countInPeriod($model, $lastMonth, $lastMonthEnd);

        return $current >= $previous ? 'success' : 'danger';
    }
}