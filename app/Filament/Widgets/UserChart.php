<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class UserChart extends ChartWidget
{

    use HasWidgetShield;

    protected static ?string $heading = 'User Chart';

    public ?string $filter = 'today';

    public static ?int $sort = 2;

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}