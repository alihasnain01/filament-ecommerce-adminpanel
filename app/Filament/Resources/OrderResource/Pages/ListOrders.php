<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'New' => Tab::make()
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'new');
                })->badge(Order::query()->where('status', 'new')->count() > 0 ? Order::query()->where('status', 'new')->count() : '')
                ->badgeColor('info'),
            'Processing' => Tab::make()
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'processing');
                })
                ->badge(Order::query()->where('status', 'processing')->count() > 0 ? Order::query()->where('status', 'processing')->count() : '')
                ->badgeColor('warning'),
            'Shipped' => Tab::make()
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'shipped');
                })->badge(Order::query()->where('status', 'shipped')->count() > 0 ? Order::query()->where('status', 'shipped')->count() : ''),
            'Delivered' => Tab::make()
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'delivered');
                })->badge(Order::query()->where('status', 'delivered')->count() > 0 ? Order::query()->where('status', 'delivered')->count() : '')
                ->badgeColor('success'),
            'Cancelled' => Tab::make()
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'cancelled');
                })
                ->badge(Order::query()->where('status', 'cancelled')->count() > 0 ? Order::query()->where('status', 'cancelled')->count() : '')
                ->badgeColor('danger'),
        ];
    }
}
