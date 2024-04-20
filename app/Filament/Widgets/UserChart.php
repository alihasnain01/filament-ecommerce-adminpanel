<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Widgets\ChartWidget;

class UserChart extends ChartWidget
{

    use HasPageShield;

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
