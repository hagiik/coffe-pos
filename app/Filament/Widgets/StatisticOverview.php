<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Enums\IconPosition;

class StatisticOverview extends BaseWidget
{
    use HasWidgetShield;
    protected static bool $isLazy = false;
    protected function getStats(): array
    {
        return [
             Stat::make('Total Order', Order::count())
                ->description('Total Keseluruhan Pesanan')
                ->descriptionIcon('heroicon-o-shopping-cart', IconPosition::After)
                // ->chart([1, 3, 5, 10, 20, 40])
                ->color('info'),
            
            Stat::make('Total Sales', 'Rp ' . number_format(Order::where('status', 'Selesai')->sum('total_price'), 0, ',', '.'))
                ->description('Hasil Keseluruhan Penjualan')
                ->descriptionIcon('heroicon-o-credit-card', IconPosition::After)
                // ->chart([1000000, 2000000, 3000000, 4000000, 5000000, 6000000])
                ->color('success'),
            
            Stat::make('Total Customer', Order::distinct('customer_name')->count('customer_name'))
                ->description('Total Keseluruhan Pelanggan')
                ->descriptionIcon('heroicon-o-user', IconPosition::After)
                // ->chart([1, 3, 5, 10, 20, 40])
                ->color('info'),
        ];
    }
}
