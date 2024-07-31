<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class UsersOverview extends BaseWidget
{
    use HasWidgetShield;
    protected static ?string $pollingInterval = '30s';
    protected function getStats(): array
    {
        return [
            Stat::make('All users', User::role('user')->count())->description('Total Users'),
            Stat::make('Orders', Order::whereDate('created_at', Carbon::today())->count())->description('Total toaday Orders'),
        ];
    }
}